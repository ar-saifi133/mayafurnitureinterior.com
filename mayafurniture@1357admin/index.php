<?php
if (!isset($_SESSION)) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require '../config/config.php';
require 'functions/authentication.php';


$db = new dbClass();
$auth = new AdminManager();

if (isset($_SESSION['ADMIN_USER_ID']) && !empty($_SESSION['ADMIN_USER_ID'])) {
    header('Location: home.php');
    exit();
}

if (isset($_REQUEST['btn_login']) && $_REQUEST['btn_login'] == 'Login') {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    $result = $auth->adminLogin($email, $pass);

    // DEBUG: Remove this after testing
    if (!$result) {
        // die("DEBUG: Login failed. Email: [$email] | Pass: [$pass]");
    }

    if ($result == true) {
        header('Location: home.php');
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid email or password.";
        header('Location: index.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title><?php echo isset($websiteTitle) ? $websiteTitle : 'Admin'; ?> | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="icon" href="../assets/images/logo-admin.png">
    <!-- App css -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="libs/toastr/css/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icofont/1.0.1/icofont.min.css">

    <style>
        .auth-logo {
            width: 100%;

        }
    </style>


</head>

<body class="bg-login">
    <div class="container-xxl">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card main-bg-c">
                                <div class="card-body p-0 auth-header-box rounded-top">
                                    <div class="text-center p-3 d-flex justify-content-center align-items-center">
                                        <div class="logo logo-admin">
                                            <img src="../assets/images/logo-admin.png" width="60px" alt="logo"
                                                class="auth-logo">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4 pt-0">
                                    <form method="post" class="row g-3 needs-validation" novalidate="">

                                        <div class="vertical-radius">
                                            <label class="text-label text-black form-label required"
                                                for="email">Username</label>
                                            <div class="input-group mb-2">
                                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                                <input type="text" name="email" class="form-control  br-style"
                                                    id="email" placeholder="Enter email" required>
                                                <div class="invalid-feedback">
                                                    Please Enter a Username.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 vertical-radius">
                                            <label class="text-label text-black form-label required"
                                                for="dz-password">Password</label>
                                            <div class="input-group transparent-append">
                                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                                <input type="password" name="password" class="form-control"
                                                    id="userpassword" placeholder="Enter password" required>
                                                <span class="input-group-text show-pass br-style" id="togglePassword"
                                                    style="display:none;">
                                                    <i class="fa fa-eye-slash" id="eyeSlashIcon"></i>
                                                    <i class="fa fa-eye" id="eyeIcon" style="display:none;"></i>
                                                </span>
                                                <div class="invalid-feedback">
                                                    Please Enter Your Password.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0 p-0">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary login-btn" type="submit"
                                                        name="btn_login" value="Login">
                                                        Log In <i class="fas fa-sign-in-alt ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!-- container -->

    <script src="js/jquery-3.6.0.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="libs/simplebar/simplebar.min.js"></script>
    <!-- <script src="js/pages/form-validation.js"></script> -->
    <script src="libs/toastr/js/toastr.min.js"></script>
    <script src="js/toastr-init.js"></script>
    <script src="js/app.js"></script>

    <script>
        <?php if (isset($_SESSION['msg'])): ?>
            toastr.success("<?php echo $_SESSION['msg']; ?>");
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errmsg'])): ?>
            toastr.error("<?php echo $_SESSION['errmsg']; ?>");
            <?php unset($_SESSION['errmsg']); ?>
        <?php endif; ?>
    </script>

    <script>
            (function () {
                'use strict'
                var forms = document.querySelectorAll('.needs-validation')
                Array.prototype.slice.call(forms)
                    .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('userpassword');
            const togglePassword = document.getElementById('togglePassword');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');
            const eyeIcon = document.getElementById('eyeIcon');

            function updateToggleVisibility() {
                if (passwordInput.value.length === 0) {
                    togglePassword.style.display = 'none';
                } else {
                    togglePassword.style.display = 'inline-flex';
                }
            }

            function togglePasswordVisibility() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeSlashIcon.style.display = 'none';
                    eyeIcon.style.display = 'inline';
                } else {
                    passwordInput.type = 'password';
                    eyeSlashIcon.style.display = 'inline';
                    eyeIcon.style.display = 'none';
                }
            }
            updateToggleVisibility();
            passwordInput.addEventListener('input', updateToggleVisibility);
            togglePassword.addEventListener('click', togglePasswordVisibility);
        });
    </script>

</body>

</html>