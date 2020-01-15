<?php
$lte_dir = dirname(__FILE__);
$imageFolder = $lte_dir . "/../images/";
include_once("{$lte_dir}/profile/mpi.php");

$mpi = new Mpi($memberInfo['username'], $imageFolder);
$usr_img = PREPEND_PATH . "images/" . $mpi->thumb;
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Navbar left Menu -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <!-- Sidebar toggle button-->
    <p class="navbar-text hidden-xs"><?php echo $LTE_globals['navbar-text']; ?></p>
  </ul>
  <!-- /Navbar left Menu -->

  <!-- Navbar right Menu -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" href="#"><i class="fa fa-globe"></i></a>
    </li>
    <!-- Control Sidebar Toggle Button -->
    <li class="nav-item">
      <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fas fa-th-large"></i></a>
    </li>
  </ul>
  <!-- /Navbar right Menu -->
</nav>