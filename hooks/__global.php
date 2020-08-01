<?php
// For help on using hooks, please refer to https://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks

function login_ok($memberInfo, &$args)
{

	return '';
}

function login_failed($attempt, &$args)
{
}

function member_activity($memberInfo, $activity, &$args)
{
	switch ($activity) {
		case 'pending':
			break;

		case 'automatic':
			break;

		case 'profile':
			break;

		case 'password':
			break;
	}
}

function sendmail_handler(&$pm)
{
}

function activate_LAT($fn)
{
	if (!empty($fn)) {
		if (LAT_enable()) {
			$rootDir = dirname(__FILE__) . "/..";
			include_once("$rootDir/LAT/" . $fn . "_lat.php");
			return true;
		}
	}
	return false;
}

function LAT_enable()
{
	$rootDir = dirname(__FILE__) . "/..";
	if (is_file("$rootDir/LAT/config_lat.php")) {
		include_once "$rootDir/LAT/config_lat.php";
		if (get_LTA_Status()) {
			return true;
		}
	}
	return false;
}

//hacer un hash al file y verificar si cambió.