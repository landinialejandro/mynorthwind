<?php
$latDir = dirname(__FILE__)."/..";
$imageFolder = $latDir . "/../images/";
include_once("{$latDir}/profile/mpi.php");

$mpi = new Mpi($memberInfo['username'], $imageFolder);
$usr_img = PREPEND_PATH . "images/" . $mpi->thumb;
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Navbar left Menu -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <!-- Sidebar toggle button-->
    <span class="navbar-text hidden-xs"><?php echo $LAT_globals['navbar-text']; ?></span>
  </ul>
  <!-- /Navbar left Menu -->

  <!-- Navbar right Menu -->
  <ul class="navbar-nav ml-auto">
    <?php
    if (getLoggedAdmin()) { ?>
      <li class="status">
        <a href="<?php echo PREPEND_PATH; ?>admin/pageHome.php" class="nav-link" title="<?php echo html_attr($Translation['admin area']); ?>"><i class="fas fa-cogs"></i>&nbsp;Older&nbsp;<?php echo $Translation['admin area']; ?></a>
    </li>
    <?php } 
    if (!$inLogin){
    ?>
      <!-- Control Sidebar Toggle Button -->
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fas fa-th-large"></i></a>
      </li>
    <?php } ?>
  </ul>
  <!-- /Navbar right Menu -->
</nav>
<!-- /Navbar -->