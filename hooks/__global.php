
<?php /* Inserted by Landini Admin Template on 2020-08-09 03:01:50 */ ?>
		<?php include(dirname(__FILE__) . "/../LAT/setup_lat.php");?>
<?php /* End of Landini Admin Template code */ ?>

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