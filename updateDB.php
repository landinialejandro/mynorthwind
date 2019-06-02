<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('orders', "create table if not exists `orders` (   `id` INT unsigned not null auto_increment , primary key (`id`), `orderNumber` VARCHAR(40) null , `customer` VARCHAR(40) null ) CHARSET utf8", $silent, array( "ALTER TABLE orders ADD `field1` VARCHAR(40)","ALTER TABLE `orders` CHANGE `field1` `id` VARCHAR(40) null ","ALTER TABLE `orders` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE orders ADD `field2` VARCHAR(40)","ALTER TABLE `orders` CHANGE `field2` `orderNumber` VARCHAR(40) null ","ALTER TABLE orders ADD `field3` VARCHAR(40)","ALTER TABLE `orders` CHANGE `field3` `customer` VARCHAR(40) null "));
		setupTable('contacts', "create table if not exists `contacts` (   `id` INT unsigned not null auto_increment , primary key (`id`), `fullName` VARCHAR(40) null ) CHARSET utf8", $silent, array( "ALTER TABLE contacts ADD `field1` VARCHAR(40)","ALTER TABLE `contacts` CHANGE `field1` `id` VARCHAR(40) null ","ALTER TABLE `contacts` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE contacts ADD `field2` VARCHAR(40)","ALTER TABLE `contacts` CHANGE `field2` `fullName` VARCHAR(40) null ","ALTER TABLE contacts ADD `field3` VARCHAR(40)","ALTER TABLE `contacts` CHANGE `field3` `address` VARCHAR(40) null ","ALTER TABLE `contacts` DROP `address`"));
		setupTable('addresses', "create table if not exists `addresses` (   `id` INT unsigned not null auto_increment , primary key (`id`), `address` VARCHAR(40) null ) CHARSET utf8", $silent, array( "ALTER TABLE addresses ADD `field1` VARCHAR(40)","ALTER TABLE `addresses` CHANGE `field1` `id` VARCHAR(40) null ","ALTER TABLE `addresses` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE addresses ADD `field2` VARCHAR(40)","ALTER TABLE `addresses` CHANGE `field2` `adress` VARCHAR(40) null ","ALTER TABLE `addresses` CHANGE `adress` `address` VARCHAR(40) null "));
		setupTable('companies', "create table if not exists `companies` (   `id` INT unsigned not null auto_increment , primary key (`id`), `name` VARCHAR(40) null ) CHARSET utf8", $silent, array( "ALTER TABLE companies ADD `field1` VARCHAR(40)","ALTER TABLE `companies` CHANGE `field1` `id` VARCHAR(40) null ","ALTER TABLE `companies` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE companies ADD `field2` VARCHAR(40)","ALTER TABLE `companies` CHANGE `field2` `name` VARCHAR(40) null "));


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@db_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@db_query("select count(1) from `$tableName`")){ // table already exists
			if($row = @db_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br>';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@db_query($alter)){
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
							}else{
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@db_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)){
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
					}else{
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@db_query($createSQL)){
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . db_error(db_link()) . '</div>';
				}else{
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>