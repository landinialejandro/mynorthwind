<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5) {
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)) {
			$silent=true;
		}

		// set up tables
		setupTable('orders', "create table if not exists `orders` (   `id` INT unsigned not null auto_increment , primary key (`id`), `orderNumber` TINYBLOB null , `customer` VARCHAR(40) null ) CHARSET utf8", $silent);
		setupTable('contacts', "create table if not exists `contacts` (   `id` INT unsigned not null auto_increment , primary key (`id`), `fullName` VARCHAR(40) null , `type` TINYBLOB null ) CHARSET utf8", $silent);
		setupTable('addresses', "create table if not exists `addresses` (   `id` INT unsigned not null auto_increment , primary key (`id`), `address` VARCHAR(40) null ) CHARSET utf8", $silent);
		setupTable('companies', "create table if not exists `companies` (   `id` INT unsigned not null auto_increment , primary key (`id`), `name` VARCHAR(40) null , `type` INT unsigned null ) CHARSET utf8", $silent);
		setupIndexes('companies', array('type'));
		setupTable('logins', "create table if not exists `logins` (   `id` INT unsigned not null auto_increment , primary key (`id`), `ip` VARCHAR(40) null ) CHARSET utf8", $silent);
		setupTable('compnayTypes', "create table if not exists `compnayTypes` (   `id` INT unsigned not null auto_increment , primary key (`id`), `type` VARCHAR(40) null ) CHARSET utf8", $silent);
		setupTable('details', "create table if not exists `details` (   `id` INT unsigned not null auto_increment , primary key (`id`), `order` INT unsigned null , `quantity` VARCHAR(40) null , `item` VARCHAR(40) null , `vaule` VARCHAR(40) null ) CHARSET utf8", $silent);
		setupIndexes('details', array('order'));
		setupTable('todoList', "create table if not exists `todoList` (   `id` INT unsigned not null auto_increment , primary key (`id`), `task` VARCHAR(40) not null , `taskReady` INT null default '0' , `reminder` DATE null , `reminder_time` TIME null , `prority` VARCHAR(40) null default 'Low' , `notes` TINYTEXT null , `order` INT null ) CHARSET utf8", $silent);


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')) {
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields) {
		if(!is_array($arrFields)) {
			return false;
		}

		foreach($arrFields as $fieldName) {
			if(!$res=@db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")) {
				continue;
			}
			if(!$row=@db_fetch_assoc($res)) {
				continue;
			}
			if($row['Key']=='') {
				@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter='') {
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)) {
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)) {
				$oldTableName=$matches[1];
			}
		}

		if($res=@db_query("select count(1) from `$tableName`")) { // table already exists
			if($row = @db_fetch_array($res)) {
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)) {
					echo '<br>';
					foreach($arrAlter as $alter) {
						if($alter!='') {
							echo "$alter ... ";
							if(!@db_query($alter)) {
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

			if($oldTableName!='') { // if we have a table rename query
				if($ro=@db_query("select count(1) from `$oldTableName`")) { // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)) {
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
				if(!@db_query($createSQL)) {
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
		if(!$silent) {
			echo $out;
		}
	}
?>