<!-- leftbar-tab-menu -->
<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
        <a href="home.php" class="logo">
            <span class="large-logo">
                  <img src="../img/logomaya.png" alt="Maya Furniture" style="width: 150px;">
            </span>
            <span class="small-logo">
                <img src="../img/logomaya.png" alt="Maya Furniture" style="width: 50px;">
            </span>
        </a>
    </div>
    <!--end brand-->

    <!--start startbar-menu-->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">

                    <!-- Content Management -->
                    <!-- <li class="menu-label pt-0 mt-0"> <span>-----</span>  <span>Content Management</span></li> -->

                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="iconoir-home-simple menu-icon"></i>
                            <span>Dashboards</span>
                        </a>
                    </li>





                        <li class="nav-item">
                        <a class="nav-link" href="contact_inquiries.php">
                            <i class="fas fa-envelope menu-icon"></i>
                            <span>Contact Inquiries</span>
                        </a>
                        </li>






     

                </ul>
            </div>
        </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->
</div><!--end startbar-->
<div class="startbar-overlay d-print-none"></div>
<!-- end leftbar-tab-menu -->

<script>
function downloadBackup() {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'database_backup.php';
    form.style.display = 'none';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'backup';
    input.value = '1';

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>