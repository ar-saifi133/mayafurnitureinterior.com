<?php

if(!isset($_SESSION)){session_start();}
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';

$db = new dbClass();
$auth = new AdminManager();
$auth->checkSession();
$websiteTitle = 'Maya Furniture';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

    <head>      
        <meta charset="utf-8" />
        <title><?php echo $websiteTitle ?$websiteTitle: 'Admin'; ?> | Home</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">        
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="images/favicon.png">    
         <!-- App css -->
         <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
         <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
         <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
         <link href="libs/toastr/css/toastr.min.css" rel="stylesheet" type="text/css" />
         <link href="css/app.min.css" rel="stylesheet" type="text/css" />

         <style>
.modern-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  position: relative;
  overflow: hidden;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  margin: 20px 0;
  border: 1px solid #f1f1f1;
}

.modern-heading {
  font-size: 3.5rem;
  font-weight: 800;
  background: linear-gradient(90deg, #283361, #CDB091);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: float 3s ease-in-out infinite;
  letter-spacing: 1px;
}

.modern-subtext {
  color: #6c757d;
  font-size: 1.1rem;
  margin-top: 10px;
  opacity: 0;
  animation: fadeInUp 1s ease-in-out 1.5s forwards;
}

/* gradient animation */
@keyframes shine {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* float animation */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

/* fade-in */
@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(15px); }
  100% { opacity: 1; transform: translateY(0); }
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
                <div class="modern-center text-center my-5">
                    <h1 class="modern-heading">Welcome Admin </h1>
                    <p class="modern-subtext">Your control panel is ready to manage everything.</p>
                </div>
            </div>
            <?php include 'include/footer.php'; ?>
        </div>
    </div>
    <!-- end page-wrapper -->

    <!-- Javascript  -->
    <!-- vendor js -->
    

       
        <script src="js/jquery-3.6.0.js"></script>
        <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="libs/simplebar/simplebar.min.js"></script>
        <!-- <script src="js/pages/form-validation.js"></script> -->
        <script src="libs/toastr/js/toastr.min.js"></script>
        <script src="js/toastr-init.js"></script>
        <script src="js/app.js"></script>
</body>
<!--end body-->

</html>