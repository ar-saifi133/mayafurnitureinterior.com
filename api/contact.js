const nodemailer = require('nodemailer');

module.exports = async function handler(req, res) {
  // Set CORS and JSON headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ status: 'error', message: 'Method Not Allowed' });
  }

  try {
    // Parse body (supports JSON or URL-encoded form data)
    let body = req.body;
    if (typeof body === 'string') {
      try {
        body = JSON.parse(body);
      } catch (e) {
        body = Object.fromEntries(new URLSearchParams(body));
      }
    } else if (!body) {
      body = {};
    }

    const name = (body.name || '').trim();
    const email = (body.email || '').trim();
    const phone = (body.phone || '').trim();
    const message = (body.message || '').trim();

    // Validation
    if (!name || !phone) {
      return res.status(400).json({
        status: 'error',
        message: 'Name and Phone number are required.'
      });
    }

    const recipientEmail = 'mayafurnitureinterior@gmail.com';
    const timestamp = new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' });

    // Always log the inquiry to Vercel serverless logs so no lead is ever lost
    console.log('--- NEW INQUIRY RECEIVED ---');
    console.log(`Time: ${timestamp}`);
    console.log(`Name: ${name}`);
    console.log(`Phone: ${phone}`);
    console.log(`Email: ${email || 'Not provided'}`);
    console.log(`Message: ${message || 'None'}`);
    console.log('----------------------------');

    // Email configuration from environment variables
    const emailUser = process.env.EMAIL_USER || process.env.SMTP_USER;
    const emailPass = process.env.EMAIL_PASS || process.env.SMTP_PASS;

    // Check if Resend API key is configured
    if (process.env.RESEND_API_KEY) {
      const resendResponse = await fetch('https://api.resend.com/emails', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${process.env.RESEND_API_KEY}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          from: process.env.RESEND_FROM || 'Maya Furniture <onboarding@resend.dev>',
          to: [recipientEmail],
          reply_to: email || undefined,
          subject: `New Inquiry from ${name}`,
          html: buildHtmlEmail(name, email, phone, message, timestamp)
        })
      });

      if (!resendResponse.ok) {
        const errorData = await resendResponse.json();
        console.error('Resend error:', errorData);
      }
    } else if (emailUser && emailPass) {
      // Send via Nodemailer (Gmail or custom SMTP)
      const transporter = nodemailer.createTransport({
        service: process.env.SMTP_SERVICE || 'gmail',
        host: process.env.SMTP_HOST || undefined,
        port: process.env.SMTP_PORT ? parseInt(process.env.SMTP_PORT) : undefined,
        secure: process.env.SMTP_SECURE === 'true',
        auth: {
          user: emailUser,
          pass: emailPass
        }
      });

      const recipients = Array.from(new Set([
        recipientEmail,
        emailUser
      ])).filter(Boolean);

      await transporter.sendMail({
        from: `"Maya Furniture Website" <${emailUser}>`,
        to: recipients,
        replyTo: email || undefined,
        subject: `New Inquiry from ${name} - Maya Furniture`,
        html: buildHtmlEmail(name, email, phone, message, timestamp)
      });
    } else {
      console.warn(
        'Notice: EMAIL_USER and EMAIL_PASS (or RESEND_API_KEY) are not set in Vercel Environment Variables. Lead was recorded in serverless logs.'
      );
    }

    return res.status(200).json({
      status: 'success',
      message: 'Thank you! Your inquiry has been sent successfully. We will get in touch shortly.'
    });

  } catch (error) {
    console.error('Serverless function error:', error);
    return res.status(500).json({
      status: 'error',
      message: 'Failed to process inquiry. Please try contacting us directly via phone or WhatsApp.'
    });
  }
};

function buildHtmlEmail(name, email, phone, message, timestamp) {
  const safeMessage = (message || 'No message provided').replace(/\n/g, '<br/>');
  return `
    <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background-color: #ffffff;">
      <div style="background-color: #2b2b2b; padding: 24px 20px; text-align: center;">
        <h2 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px;">New Website Lead</h2>
        <p style="color: #c9a050; margin: 6px 0 0 0; font-size: 13px; text-transform: uppercase;">Maya Furniture &amp; Interior</p>
      </div>
      <div style="padding: 28px 24px; color: #333333;">
        <div style="margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">
          <strong style="color: #777777; font-size: 12px; text-transform: uppercase;">Client Name:</strong>
          <div style="font-size: 18px; font-weight: 600; color: #111111; margin-top: 4px;">${name}</div>
        </div>
        <div style="margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">
          <strong style="color: #777777; font-size: 12px; text-transform: uppercase;">Phone Number:</strong>
          <div style="font-size: 16px; font-weight: 600; color: #111111; margin-top: 4px;">
            <a href="tel:${phone}" style="color: #c9a050; text-decoration: none;">${phone}</a>
          </div>
        </div>
        <div style="margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;">
          <strong style="color: #777777; font-size: 12px; text-transform: uppercase;">Email Address:</strong>
          <div style="font-size: 15px; color: #111111; margin-top: 4px;">
            ${email ? `<a href="mailto:${email}" style="color: #c9a050; text-decoration: none;">${email}</a>` : '<em>Not provided</em>'}
          </div>
        </div>
        <div style="margin-bottom: 10px;">
          <strong style="color: #777777; font-size: 12px; text-transform: uppercase;">Project / Inquiry Details:</strong>
          <div style="background-color: #f8f9fa; padding: 14px; border-radius: 6px; margin-top: 6px; font-size: 14px; line-height: 1.6; color: #444444; border-left: 3px solid #c9a050;">
            ${safeMessage}
          </div>
        </div>
      </div>
      <div style="background-color: #f7f7f7; padding: 12px 20px; text-align: center; font-size: 11px; color: #888888; border-top: 1px solid #eeeeee;">
        Submitted on ${timestamp} via mayafurnitureinterior.com
      </div>
    </div>
  `;
}
