
		<?php /* Inserted by Landini AdminLTE Template on 2020-03-25 02:58:57 */ ?>
		
		<?php
		//enable Landini Admin Template
		//TODO: verificar si exite el archivo primero antes de incluirlo
		include_once "../LAT/config_lat.php";
		if (getLteStatus()){
			//define("PREPEND_PATH", "../");
			$fn = basename(__FILE__, ".php"); 
			$ADMINAREA = true;
			include_once("../LAT/footer_lat.php");
			return;
		}
		?>
		
		<?php /* End of Landini AdminLTE Template code */ ?>
		</div><!-- /div class="container" -->
		</body>
	</html>
<?php exit; ?>