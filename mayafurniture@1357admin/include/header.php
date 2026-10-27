<?php
$headerUserRow = $auth->getAdminHeaderInfo($_SESSION['ADMIN_USER_ID']);
$adminUsername = $headerUserRow['username'];
$adminImage = !empty($headerUserRow['image'])
  ? '../img/logomaya.png'
  : '../img/logomaya.png';
$websiteTitle = 'Maya Furniture';
?>

<div class="topbar d-print-none">
  <div class="container-fluid">
    <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">
      <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
        <li>
          <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
            <i class="iconoir-menu-scale"></i>
          </button>
        </li>
        <li class="mx-3 welcome-text">
          <h3 class="mb-0 fw-bold text-truncate">Admin Dashboard</h3>
        </li>
      </ul>
      <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
        <li class="topbar-item">
          <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
            <i class="fas fa-moon dark-mode" style="font-size:1.1rem;"></i>
            <i class="fas fa-sun light-mode" style="font-size:1.1rem;"></i>
          </a>
        </li>
        <li class="dropdown topbar-item">
          <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#">
            <img src="<?php echo $adminImage; ?>?v=<?php echo time(); ?>" alt="Profile" class="thumb-lg rounded-circle header-profile-image">
          </a>
          <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
              <div class="flex-shrink-0">
                <img src="<?php echo $adminImage; ?>?v=<?php echo time(); ?>" alt="Profile" class="thumb-md rounded-circle header-profile-image">
              </div>
              <div class="flex-grow-1 ms-2 text-truncate align-self-center">
               <h6 class="my-0 fw-medium text-dark fs-13 header-username">
                <?php echo htmlspecialchars($adminUsername); ?>
                </h6>
              </div>
            </div>
            <div class="dropdown-divider mt-0"></div>
            <small class="text-muted px-2 pb-1 d-block">Account</small>
            <a class="dropdown-item" href="profile.php">
              <i class="las la-user fs-18 me-1 align-text-bottom"></i> Profile
            </a>
            <a class="dropdown-item" href="profile.php?tab=password">
              <i class="las la-cog fs-18 me-1 align-text-bottom"></i> Change Password
            </a>
            <small class="text-muted px-2 pb-1 d-block">Database</small>
            <button class="dropdown-item" id="header-create-backup-btn">
              <i class="las la-database fs-18 me-1 align-text-bottom"></i> Backup Now
            </button>
            <a class="dropdown-item" href="profile.php?tab=database">
              <i class="las la-cogs fs-18 me-1 align-text-bottom"></i> DB Management
            </a>
            <div class="dropdown-divider mb-0"></div>
            <a class="dropdown-item text-danger" href="logout.php">
              <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout
            </a>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</div>

<script>
  (function() {
    try {
      function getThemeFromStorage() {
        // Try localStorage first (works better across page navigations)
        try {
          const theme = localStorage.getItem('adminTheme');
          if (theme) return theme;
        } catch (e) {}
        
        // Fallback to cookie
        const cookieString = document.cookie;
        if (!cookieString) return 'light';
        const cookies = cookieString.split(';');
        for (let i = 0; i < cookies.length; i++) {
          let cookie = cookies[i].trim();
          if (cookie.startsWith('theme=')) {
            return cookie.substring('theme='.length, cookie.length);
          }
        }
        return 'light';
      }
      const savedTheme = getThemeFromStorage();
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    } catch (e) {
      document.documentElement.setAttribute('data-bs-theme', 'light');
    }
    try {
      const savedSidebarState = localStorage.getItem('sidebarState');
      if (savedSidebarState === 'collapsed') {
        document.body.setAttribute('data-sidebar-size', 'collapsed');
      }
    } catch (e) {}
  })();

  document.addEventListener('DOMContentLoaded', function() {
    const backupButton = document.getElementById('header-create-backup-btn');
    if (backupButton) {
      backupButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        const btn = this;
        const icon = btn.querySelector('i');
        const originalIconClass = icon.className;

        if (typeof toastr === 'undefined') {
          alert('Notification library (toastr) is not available.');
          return;
        }
        btn.disabled = true;
        icon.className = 'las la-spinner fa-spin fs-18 me-1 align-text-bottom';
        
        $.post('db-backup.php', {}, function(res) {
          if (res.success) {
            toastr.success(res.message, 'Backup Created');
            if (typeof loadBackups === 'function') {
                loadBackups();
            }
          } else {
            toastr.error(res.message || 'Backup creation failed.', 'Error');
          }
        }, 'json')
        .fail(function() {
          toastr.error('A server error occurred while creating the backup.', 'Server Error');
        })
        .always(function() {
          btn.disabled = false;
          icon.className = originalIconClass; 
        });
      });
    }
  });
</script>