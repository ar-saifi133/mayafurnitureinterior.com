<?php
require_once '../config/config.php';
require_once 'functions/authentication.php';

$auth = new AdminManager();
$auth->checkSession();

$userRow = $auth->getAdminDetails($_SESSION['ADMIN_USER_ID']);
$profileImage = !empty($userRow['image'])
  ? 'images/' . htmlspecialchars($userRow['image'])
  : 'images/sol-cones-logo.png';

$websiteTitle = 'K&R Legal';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <title><?php echo isset($websiteTitle) ? $websiteTitle : 'Admin'; ?> | Admin Profile & Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="images/favicon.png">

  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
  <link href="libs/toastr/css/toastr.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
         <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
  <link href="libs/prism/prism.css" rel="stylesheet" type="text/css" />
  <link href="libs/prism/prism-line-numbers.css" rel="stylesheet" type="text/css" />
  <link href="css/app.min.css" rel="stylesheet" type="text/css" />
  <style>
    .invalid-feedback {
      display: block;
    }
    /* dark/light mode icon toggle */
    [data-bs-theme="light"] .dark-mode { display: inline-block !important; }
    [data-bs-theme="light"] .light-mode { display: none !important; }
    [data-bs-theme="dark"] .dark-mode { display: none !important; }
    [data-bs-theme="dark"] .light-mode { display: inline-block !important; }
  </style>
</head>
<body>
  <?php include 'include/header.php'; ?>
  <?php include 'include/sidebar.php'; ?>
  <div class="page-wrapper">
    <div class="page-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <h4 class="page-title mb-4">Admin Profile & Settings</h4>
          </div>
        </div>
        <div class="row main-content-row">
          <div class="col-lg-4 mb-3">
            <div class="card">
              <div class="card-body">
                <div class="text-center">
                  <form id="profile-image-form">

                    <label for="image-upload-input" id="image-upload-label" class="position-relative d-inline-block mb-3" 
                    data-bs-toggle="tooltip" title="Click to change profile picture">

                      <img src="<?php echo $profileImage; ?>?v=<?php echo time(); ?>" alt="Profile Image" 
                      class="rounded-circle" id="display-image" style="height: 140px; width: 140px; object-fit: cover;">

                      <div class="camera-overlay">
                        <i class="fas fa-camera"></i>
                      </div>
                    </label>

                  </form>

                    <h4 class="fw-semibold fs-22 mb-1" id="display-username">
                        <?php echo htmlspecialchars(isset($userRow['username']) ? $userRow['username'] : ''); ?>
                    </h4>

                    <p class="mb-3 text-muted fw-medium" id="display-email">
                        <?php echo htmlspecialchars(isset($userRow['email']) ? $userRow['email'] : ''); ?>
                    </p>

                </div>
                <hr>
                <div class="text-start">
                  <div class="profile-info-item">
                    <div class="icon-wrapper">
                      <i class="las la-phone"></i>
                    </div>
                    <div class="info-text ms-2">
                      <div class="label">Phone Number</div>
                        <div class="value" id="display-phone">
                            <?php echo htmlspecialchars(isset($userRow['phone']) ? $userRow['phone'] : ''); ?>
                        </div>
                    </div>
                  </div>
                  <div class="profile-info-item">
                    <div class="icon-wrapper">
                      <i class="las la-globe"></i>
                    </div>
                    <div class="info-text ms-2">
                      <div class="label">Last Login IP</div>
                        <div class="value">
                            <?php echo htmlspecialchars(isset($userRow['login_ip']) ? $userRow['login_ip'] : 'N/A'); ?>
                        </div>
                    </div>
                  </div>
                  <div class="profile-info-item">
                    <div class="icon-wrapper">
                      <i class="las la-calendar-check"></i>
                    </div>
                    <div class="info-text ms-2">
                      <div class="label">Last Login Date</div>
                        <div class="value">
                            <?php echo date('j M, Y - g:i A', strtotime($userRow['login_date'])); ?>
                        </div>
                    </div>
                  </div>
                  <div class="profile-info-item">
                    <div class="icon-wrapper">
                      <i class="las la-history"></i>
                    </div>
                    <div class="info-text ms-2">
                      <div class="label">Last Backup Taken</div>
                      <div class="value" id="last-backup-ago">Loading...</div>
                    </div>
                  </div>
                </div>
                <div class="real-time-clock">
                  <div class="time" id="clock-time">--:--:--</div>
                  <div class="date" id="clock-date">----------</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-8 mb-3">
            <div class="card h-100">
              <div class="card-header">
                <ul class="nav nav-tabs-custom card-header-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab-link" data-bs-toggle="tab" href="#editProfileTab" role="tab">Edit Profile</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="password-tab-link" data-bs-toggle="tab" href="#changePasswordTab" role="tab">Change Password</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="database-tab-link" data-bs-toggle="tab" href="#databaseTab" role="tab">Database Management</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="editProfileTab" role="tabpanel">
                    <h5 class="mb-4">Personal Information</h5>
                    <form id="profile-details-form" novalidate>
                      <input type="hidden" name="action" value="update_profile">
                      <input type="hidden" name="oldimage" value="<?php echo htmlspecialchars(isset($userRow['image']) ? $userRow['image'] : ''); ?>">
                      <input type="file" name="image" id="image-upload-input" class="d-none" accept="image/*">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label required">Username</label>
                          <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input name="username" type="text" class="form-control" value="<?php echo htmlspecialchars(isset($userRow['username']) ? $userRow['username'] : ''); ?>" required>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label required">Email</label>
                          <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            <input name="email" type="email" class="form-control" value="<?php echo htmlspecialchars(isset($userRow['email']) ? $userRow['email'] : ''); ?>" required>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label required">Phone</label>
                          <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            <input name="phone" type="tel" class="form-control" value="<?php echo htmlspecialchars(isset($userRow['phone']) ? $userRow['phone'] : ''); ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="changePasswordTab" role="tabpanel">
                    <h5 class="mb-4">Change Your Password</h5>
                    <form id="password-form" novalidate>
                      <input type="hidden" name="action" value="change_password">
                      <div class="mb-3">
                        <label class="form-label required">Current Password</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fa fa-lock"></i></span>
                          <input name="current_password" type="password" class="form-control password-input" id="current_password" required>
                          <span class="input-group-text show-pass toggle-password-button" style="display:none;">
                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye" style="display:none;"></i>
                          </span>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label required">New Password</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fa fa-lock"></i></span>
                          <input name="new_password" id="new_password" type="password" class="form-control password-input" required>
                          <span class="input-group-text show-pass toggle-password-button" style="display:none;">
                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye" style="display:none;"></i>
                          </span>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label required">Confirm New Password</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fa fa-lock"></i></span>
                          <input name="confirm_password" type="password" class="form-control password-input" id="confirm_password" required>
                          <span class="input-group-text show-pass toggle-password-button" style="display:none;">
                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye" style="display:none;"></i>
                          </span>
                        </div>
                      </div>
                      <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="databaseTab" role="tabpanel">
                    <div class="border-bottom pb-3 mb-3">
                      <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="pills-backups-tab" data-bs-toggle="pill" data-bs-target="#pills-backups" type="button" role="tab">
                            Active Backups <span id="backup-count-badge" class="badge"></span>
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="pills-recycled-tab" data-bs-toggle="pill" data-bs-target="#pills-recycled" type="button" role="tab">
                            Recycle Bin <span id="recycled-count-badge" class="badge"></span>
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="pills-analytics-tab" data-bs-toggle="pill" data-bs-target="#pills-analytics" type="button" role="tab">
                            <i class="las la-chart-bar me-1"></i>Analytics
                          </button>
                        </li>
                      </ul>
                    </div>
                    <div class="file-management-controls">
                      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div class="d-flex gap-2">
                          <div class="input-group flex-nowrap" style="width: auto; max-width: 250px;">
                            <span class="input-group-text"><i class="las la-search"></i></span>
                            <input type="text" id="db-search" class="form-control" placeholder="Search backups...">
                          </div>
                          <select id="db-sort-by" class="form-select" style="width: auto;">
                            <option value="modified_desc">Sort by: Newest</option>
                            <option value="modified_asc">Oldest</option>
                            <option value="size_desc">Size (Largest)</option>
                            <option value="size_asc">Size (Smallest)</option>
                          </select>
                        </div>
                        <div class="db-actions">
                          <button class="btn btn-sm btn-outline-secondary" id="db-refresh-btn" data-bs-toggle="tooltip" title="Refresh List">
                            <i class="las la-sync"></i>
                          </button>
                          <button class="btn btn-sm btn-info d-none" id="db-restore-all-btn" data-bs-toggle="tooltip" title="Restore all items">
                            <i class="las la-undo-alt"></i>
                          </button>
                          <button class="btn btn-sm btn-danger d-none" id="db-empty-bin-btn" data-bs-toggle="tooltip" title="Empty Recycle Bin">
                            <i class="las la-trash-alt"></i>
                          </button>
                          <button id="create-backup-btn" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Create New Backup">
                            <i class="las la-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="bulk-actions-bar mb-2 align-items-center gap-2" id="bulk-actions-backups">
                        <span class="fw-bold me-2" id="backup-selected-count"></span>
                        <button class="btn btn-sm btn-outline-warning" id="bulk-delete-btn">
                          <i class="las la-trash me-1"></i>Delete Selected
                        </button>
                      </div>
                      <div class="bulk-actions-bar mb-2 align-items-center gap-2" id="bulk-actions-recycled">
                        <span class="fw-bold me-2" id="recycled-selected-count"></span>
                        <button class="btn btn-sm btn-outline-info" id="bulk-restore-btn">
                          <i class="las la-undo me-1"></i>Restore Selected
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="bulk-perm-delete-btn">
                          <i class="las la-times-circle me-1"></i>Delete Selected Permanently
                        </button>
                      </div>
                    </div>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="pills-backups" role="tabpanel">
                        <div class="table-responsive db-table-wrapper">
                          <table class="table table-hover table-sm" id="backups-table">
                            <thead class="table-light" style="position: sticky; top: 0;">
                              <tr>
                                <th style="width: 1%;">
                                  <input class="form-check-input" type="checkbox" id="select-all-backups">
                                </th>
                                <th style="width: 5%;" class="text-center">S.No.</th>
                                <th class="sortable" data-sort="name">Filename <i class="sort-icon las la-sort"></i></th>
                                <th class="sortable" data-sort="size">Size <i class="sort-icon las la-sort"></i></th>
                                <th class="sortable" data-sort="modified">Date Created <i class="sort-icon las la-sort"></i></th>
                                <th class="text-end">Actions</th>
                              </tr>
                            </thead>
                            <tbody id="backups-tbody"></tbody>
                          </table>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="pills-recycled" role="tabpanel">
                        <div class="table-responsive db-table-wrapper">
                          <table class="table table-hover table-sm" id="recycled-table">
                            <thead class="table-light" style="position: sticky; top: 0;">
                              <tr>
                                <th style="width: 1%;">
                                  <input class="form-check-input" type="checkbox" id="select-all-recycled">
                                </th>
                                <th style="width: 5%;">S.No.</th>
                                <th class="sortable" data-sort="name">Filename <i class="sort-icon las la-sort"></i></th>
                                <th class="sortable" data-sort="size">Size <i class="sort-icon las la-sort"></i></th>
                                <th class="sortable" data-sort="modified">Date Deleted <i class="sort-icon las la-sort"></i></th>
                                <th class="text-end">Actions</th>
                              </tr>
                            </thead>
                            <tbody id="recycled-tbody"></tbody>
                          </table>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="pills-analytics" role="tabpanel">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="card bg-primary-subtle border-0">
                              <div class="card-body text-center">
                                <h6 class="text-muted">Active Backups Size</h6>
                                <h3 class="fw-bold" id="active-backup-size">...</h3>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="card bg-warning-subtle border-0">
                              <div class="card-body text-center">
                                <h6 class="text-muted">Recycle Bin Size</h6>
                                <h3 class="fw-bold" id="recycled-backup-size">...</h3>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="card bg-success-subtle border-0">
                              <div class="card-body text-center">
                                <h6 class="text-muted">Total Storage Used</h6>
                                <h3 class="fw-bold" id="total-backup-size">...</h3>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body p-2">
                            <canvas id="backup-chart" style="height: 295px;"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include 'include/footer.php'; ?>
    </div>
  </div>

  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="las la-exclamation-triangle me-2"></i>Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p id="deleteModalBodyText">Are you sure you want to delete?</p>
          <div id="deleteModalFilenameContainer" class="mt-2">
            <strong>Filename:</strong> <code id="deleteModalFilename"></code>
          </div>
          <hr>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="permanentDeleteCheck">
            <label class="form-check-label text-danger" for="permanentDeleteCheck">Delete permanently.</label>
          </div>
          <small class="text-muted" id="permanentDeleteHint">If unchecked, the file(s) will be moved to the recycle bin.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmDeleteBtnDb">Proceed</button>
        </div>
      </div>
    </div>
  </div>

  <script src="js/jquery-3.6.0.js"></script>
  <script src="js/jquery.validate.min.js"></script>
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="libs/toastr/js/toastr.min.js"></script>
  <script src="libs/prism/prism.js"></script>
  <script src="libs/prism/prism-line-numbers.js"></script>
  <script src="js/chart.js"></script>
  <script src="js/app.js"></script>
  <script src="js/database.management.min.js"></script>

</body>
</html>