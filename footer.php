
		<?php /* Inserted by Landini AdminLTE Template on 2020-04-01 05:26:01 */ ?>
		
	<?php
	//enable Landini Admin Template
	$currDir = dirname(__FILE__);
	if (is_file($currDir."/LAT/config_lat.php")){
		include_once "LAT/config_lat.php";
		if (getLteStatus()){
			$fn = basename(__FILE__, ".php"); 
			include_once("LAT/".$fn."_lat.php");
			return;
		}
	}else{
		echo "the config file not exist";
	}
	?>
	
		<?php /* End of Landini AdminLTE Template code */ ?>
			<!-- Add footer template above here -->
			<div class="clearfix"></div>
			<?php if(!$_REQUEST['Embedded']) { ?>
				<div style="height: 70px;" class="hidden-print"></div>
			<?php } ?>

			<?php if(!$_REQUEST['Embedded']) { ?>
				<!-- AppGini powered by notice -->
				<div style="height: 60px;" class="hidden-print"></div>
				<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
					<p class="navbar-text"><small>
						Powered by <a class="navbar-link" href="https://bigprof.com/appgini/" target="_blank">BigProf AppGini 5.82</a>
					</small></p>
				</nav>
			<?php } ?>

		</div> <!-- /div class="container" -->
		<?php if(!defined('APPGINI_SETUP') && is_file(dirname(__FILE__) . '/hooks/footer-extras.php')) { include(dirname(__FILE__).'/hooks/footer-extras.php'); } ?>
		<script src="<?php echo PREPEND_PATH; ?>resources/lightbox/js/lightbox.min.js"></script>
	</body>
</html>