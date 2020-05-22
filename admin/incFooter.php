
		<?php /* Inserted by Landini AdminLTE Template on 2020-05-13 07:25:10 */ ?>
		
		<?php
		//enable Landini Admin Template
		$currDir = dirname(__FILE__);
		if (is_file($currDir."/../LAT/config_lat.php")){
			include_once "../LAT/config_lat.php";
			if (getLteStatus()){
				define("PREPEND_PATH", "../");
				$ADMINAREA = true;
				include_once("../LAT/footer_lat.php");
				return;
			}
		}else{
			echo "the config file not exist";
		}
		?>
		
		<?php /* End of Landini AdminLTE Template code */ ?>

		</div><!-- /div class="container" -->
		</body>
	</html>
<?php exit; ?>