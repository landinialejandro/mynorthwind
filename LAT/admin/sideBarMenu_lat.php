<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo PREPEND_PATH; ?>index.php" class="brand-link">
        <i class="fa fa-globe brand-image img-circle elevation-3"></i>
        <span class="brand-text font-weight-light"><?php echo $LAT_globals['app-brand-text']; ?></span>
    </a>

    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo PREPEND_PATH; ?>images/<?php echo $mpi->thumb; ?>" class="img-circle elevation-2 user-image" alt="User Image">
            </div>
            <div class="info">
                <a href="<?php echo PREPEND_PATH; ?>LAT/membership_profile.php" class="d-block"><?php echo getLoggedMemberID(); ?></a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?php echo PREPEND_PATH; ?>index.php?signOut=1" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-info"></i>
                        <p><?php echo $Translation["sign out"]; ?></p>
                    </a>
                </li>
                <div class="pb-3 mb-3" style="border-bottom: 1px solid #4f5962;"></div>

            <!-- /.sidebar-menu -->
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>