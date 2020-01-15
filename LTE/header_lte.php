<!DOCTYPE html>
<?php if (!defined('PREPEND_PATH')) define('PREPEND_PATH', ''); ?>
<?php if (!defined('datalist_db_encoding')) define('datalist_db_encoding', 'UTF-8'); ?>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
	<meta charset="<?php echo datalist_db_encoding; ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $LTE_globals['app-title-prefix']; ?><?php echo (isset($x->TableTitle) ? $x->TableTitle : ''); ?></title>
	<link id="browser_favicon" rel="shortcut icon" href="<?php echo PREPEND_PATH; ?>LTE/logo/favicon.ico">

	<!-- LTE adding -->
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>LTE/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>LTE/dist/css/adminlte.min.css">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>LTE/jsonedit/jsonedit.css">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>LTE/dist/css/glyphicons.css" mediad="screen">
	<!-- /LTE adding -->
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/lightbox/css/lightbox.css" media="screen">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/select2/select2.css" media="screen">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/timepicker/bootstrap-timepicker.min.css" media="screen">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/datepicker/css/datepicker.css" media="screen">
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/bootstrap-datetimepicker/bootstrap-datetimepicker.css" media="screen">
	<!-- add rtl css if configured-->
	<?php if ($LTE_globals['app-dir-RTL-enable']) { ?>
		<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>resources/initializr/css/rtl.css">
	<?php } ?>
	
	<link rel="stylesheet" href="<?php echo PREPEND_PATH; ?>LTE/myCustom.css" mediad="screen">

	<!--[if lt IE 9]>
		<script src="<?php echo PREPEND_PATH; ?>resources/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
	<![endif]-->

	<script src="<?php echo PREPEND_PATH; ?>LTE/plugins/jquery/jquery.min.js"></script>
	<script>
		var $j = jQuery.noConflict();
	</script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/plugins/fastclick/fastclick.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/plugins/prototype/prototype.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/dist/js/adminlte.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/jsonedit/jeditable.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/jsonedit/jquery.contextMenu.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/jsonedit/jsonedit.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/moment/moment-with-locales.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/jquery/js/jquery.mark.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/select2/select2.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/timepicker/bootstrap-timepicker.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/jscookie/js.cookie.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/datepicker/js/datepicker.packed.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>resources/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/dist/js/demo.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/dist/js/bootstrap.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>LTE/profile/mpi.js"></script>
	<script src="<?php echo PREPEND_PATH; ?>common.js.php"></script>

	<?php if (isset($x->TableName) && is_file(dirname(__FILE__) . "/hooks/{$x->TableName}-tv.js")) { ?>
		<script src="<?php echo PREPEND_PATH; ?>hooks/<?php echo $x->TableName; ?>-tv.js"></script>
	<?php } ?>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
	<div class="wrapper">
		<?php
			if (function_exists('handle_maintenance')) echo handle_maintenance(true);
			$memberInfo = getMemberInfo();
			if (!defined('APPGINI_SETUP') && is_file(dirname(__FILE__) . '/../hooks/header-extras.php')) {
				include(dirname(__FILE__) . '/../hooks/header-extras.php');
			}
			if (class_exists('Notification')) echo Notification::placeholder();
			if ($_REQUEST['Embedded']) {
			?>
				<!-- process notifications -->
				<div style="height: 65px; margin: 5px 0px -25px;">
					<?php if (function_exists('showNotifications')) echo showNotifications(); ?>
				</div>
				<!-- /.process notifications -->
			<?php return; ?>
		<?php } ?>

		<!-- Navbar -->
		<?php include('header_lte_main.php'); ?>
		<!-- /.Navbar -->

		<?php
		$call = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
		if (isset($_GET['loginFailed']) || isset($_GET['signIn']) || $call == "membership_passwordReset.php" || $call == "membership_signup.php") {
			?>
			<script>
				$j("body").removeClass();
				$j("body").addClass("skin-blue fixed layout-top-nav");
				$j(".sidebar-toggle").remove();
				$j(".logo").remove();
			</script>
		<?php
		} else {
			?>
			<!-- Main Sidebar Container -->
			<?php include('header_lte_leftSideMenu.php') ?>
			<!-- /.Main Sidebar Container -->
		<?php
		}
		?>
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
			</section>
			<!-- /.content HEADER -->
			<section class="content">
				<div class="container-fluid">
					<!-- process notifications -->
					<div style="height: 65px; margin: -25px 0 -25px;">
						<?php if (function_exists('showNotifications')) echo showNotifications(); ?>
					</div>