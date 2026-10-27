<?php
// Enable error reporting for debugging live server issues
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

header('Content-Type: application/json');

try {
    if (!file_exists('config/config.php')) {
        throw new Exception("Configuration file missing.");
    }
    include_once 'config/config.php';

    // Detect AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
    }

    if (!class_exists('dbClass')) {
        throw new Exception("Database class not found.");
    }

    $db = new dbClass();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize input
        $name = strip_tags(trim($_POST['name'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone = strip_tags(trim($_POST['phone'] ?? ''));
        $message = strip_tags(trim($_POST['message'] ?? ''));
        $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

        // Validation
        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and Phone are required.']);
            exit;
        }

        // reCAPTCHA Verification using cURL
        if (empty($recaptcha_response)) {
            echo json_encode(['status' => 'error', 'message' => 'Please complete the reCAPTCHA.']);
            exit;
        }

        $secret = '6Lc2tNssAAAAAALErbJvCOucRxmYAXr5f-OumZy4';
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $verify_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'secret' => $secret,
                'response' => $recaptcha_response
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                echo json_encode(['status' => 'error', 'message' => 'CURL Error: ' . $curl_error]);
                exit;
            }
            $responseData = json_decode($response);
        } else {
            // Fallback if CURL is not enabled
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query([
                        'secret' => $secret,
                        'response' => $recaptcha_response
                    ])
                ]
            ];
            $context = stream_context_create($opts);
            $response = @file_get_contents($verify_url, false, $context);
            $responseData = json_decode($response);
        }

        if (!$responseData || !$responseData->success) {
            $msg = 'reCAPTCHA verification failed.';
            if ($responseData && isset($responseData->{'error-codes'})) {
                $msg .= ' Error codes: ' . implode(',', $responseData->{'error-codes'});
            }
            echo json_encode(['status' => 'error', 'message' => $msg]);
            exit;
        }

        // Insert into DB
        $query = "INSERT INTO contact_queries (name, email, phone, message) VALUES (:name, :email, :phone, :message)";
        $params = [
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':message' => $message
        ];

        $insertId = $db->insertData($query, $params);

        if ($insertId) {
            // Send Email
            // $recipients = ["office@ajinfotek.in", "hussain@mayafurnitureinterior.com", "mayafurniture.interior@gmail.com"];
            $recipients = ["office@ajinfotek.in", "zeeshanlateef2016@gmail.com"];
            $email_subject = "New Inquiry from " . $name;

            // Professional HTML Email Body
            $email_body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 2px solid #eee; }
                    .content { padding: 30px; }
                    .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #777; }
                    .field { margin-bottom: 15px; }
                    .label { font-weight: bold; color: #555; }
                    .value { margin-top: 5px; }
                    .message-box { background-color: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2 style='margin:0; color: #283361;'>New Website Inquiry</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <div class='label'>Name:</div>
                            <div class='value'>$name</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Email:</div>
                            <div class='value'>" . ($email ?: 'Not provided') . "</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Phone:</div>
                            <div class='value'>$phone</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Message:</div>
                            <div class='message-box'>" . nl2br($message ?: 'No message provided') . "</div>
                        </div>
                    </div>
                    <div class='footer'>
                        Sent from Maya Furniture &amp; Interior Contact Form on " . date('Y-m-d H:i:s') . "
                    </div>
                </div>
            </body>
            </html>
            ";

            $from_email = (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : "noreply@mayafurnitureinterior.com";
            $headers = "From: " . $from_email . "\n";
            $headers .= "Reply-To: " . $from_email . "\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/html; charset=UTF-8";

            $mail_sent = false;
            foreach ($recipients as $to) {
                if (mail($to, $email_subject, $email_body, $headers)) {
                    $mail_sent = true;
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Thank you! Your message has been sent successfully.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not save inquiry to database.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
}
?>