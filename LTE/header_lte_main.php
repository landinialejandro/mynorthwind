<?php
$hooks_dir = dirname(__FILE__)."/../hooks";
include_once ("{$hooks_dir}/mpi.php");
$mpiFolder = dirname(__FILE__)."/../images/";

$mpi = new Mpi($memberInfo['username'],$mpiFolder);
$usr_img = PREPEND_PATH . "images/" . $mpi->thumb ;
?>

<!-- Header Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Logo -->
  <a href="<?php echo PREPEND_PATH; ?>index.php" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><i class="<?php echo $LTE_globals['logo-mini']; ?>"></i></b>&nbsp;<?php echo $LTE_globals['logo-mini-text']; ?></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><i class="<?php echo $LTE_globals['logo-mini']; ?>"></i>&nbsp;<?php echo $LTE_globals['logo-mini-text']; ?></b></span>
  </a>
  <!-- Sidebar toggle button-->
  <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
    <span class="sr-only">Toggle navigation</span>
  </a>
  <p class="navbar-text hidden-xs" ><?php echo $LTE_globals['navbar-text']; ?></p>

  <!-- Navbar right Menu -->
      <ul class="navbar-nav ml-auto">
        <!-- User Account Menu -->
          <li class="nav-item dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="nav-link" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="<?php echo $usr_img; ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?php echo getLoggedMemberID(); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                  <img src="<?php echo PREPEND_PATH; ?>images/<?php echo $mpi->image; ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
                  <p>
                      <?php echo "user: ". getLoggedMemberID(); ?> 
                      <br> 
                      <?php echo "Group: ". $memberInfo['group']; ?>
                      
                      <small>Member since <?php echo $memberInfo['signupDate']; ?></small>
                  </p>
              </li>
              <?php 
              $call = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
              if(!$_GET['signIn'] && !$_GET['loginFailed'] && $call != "membership_passwordReset.php" && $call != "membership_signup.php" ){ ?>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-6 text-center">
                    <a href="#" class="btn btn-app"><i class="fa fa-shopping-cart"></i> My Orders</a>
                  </div>
                      <?php if(getLoggedAdmin()){ ?>
                      <div class="col-xs-6 text-center">
                          <a href="<?php echo PREPEND_PATH; ?>admin/pageHome.php" class="btn btn-app" title="<?php echo html_attr($Translation['admin area']); ?>"><i class="fa fa-cogs"></i>&nbsp;<?php echo $Translation['admin area']; ?></a>
                      </div>
                      <?php } ?>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="col-xs-6 text-center">
                  <a href="<?php echo PREPEND_PATH; ?>LTE/membership_profile.php" class="btn btn-app"><i class="fa fa-user"></i>&nbsp;Profile</a>
                </div>
                <div class="col-xs-6 text-center">
                  <!--<a href="#" class="btn btn-app">Sign out</a>-->
                  
                      <?php if(getLoggedMemberID() == $adminConfig['anonymousMember']){ ?>
                              <a href="<?php echo PREPEND_PATH; ?>index.php?signIn=1" class="btn btn-app"><?php echo $Translation['sign in']; ?></a>
                                      <?php echo $Translation['not signed in']; ?>
                      <?php }else{ ?>
                                      <a class="btn btn-app" href="<?php echo PREPEND_PATH; ?>index.php?signOut=1">
                                          <i class="glyphicon glyphicon-log-out"></i> 
                                          <?php echo $Translation['sign out']; ?>
                                      </a>
                              <script>
                                      /* periodically check if user is still signed in */
                                      setInterval(function(){
                                              $j.ajax({
                                                      url: '<?php echo PREPEND_PATH; ?>ajax_check_login.php',
                                                      success: function(username){
                                                              if(!username.length) window.location = '<?php echo PREPEND_PATH; ?>index.php?signIn=1';
                                                      }
                                              });
                                      }, 60000);
                              </script>
                      <?php } ?>
                </div>
              </li>
              <?php } ?>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
      </ul>
  <!-- /Navbar right Menu -->
</nav>
