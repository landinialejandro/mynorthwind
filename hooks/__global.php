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
include(dirname(__FILE__) . "/../LAT/setup_lat.php");