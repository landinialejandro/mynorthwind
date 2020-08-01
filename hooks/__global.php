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
		if (get_LTA_Status()) {
			$rootDir = dirname(__FILE__) . "/..";
			include_once("$rootDir/LAT/" . $fn . "_lat.php");
			return true;
		}
	}
	return false;
}

//change to FALSE if you want back to appgini default
function get_LTA_Status($LAT_enable = true)
{
	if (!function_exists('getMemberInfo')) {
		$LAT_enable = false;
	}
	return $LAT_enable;
}

//hacer un hash al file y verificar si cambió.