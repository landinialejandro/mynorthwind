<?php
// This script and data application were generated by AppGini 5.84
// Download AppGini for free from https://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/details.php");
	include("$currDir/details_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('details');
	if(!$perm[0]) {
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "details";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = array(
		"`details`.`id`" => "id",
		"IF(    CHAR_LENGTH(`orders1`.`id`), CONCAT_WS('',   `orders1`.`id`), '') /* Order */" => "order",
		"`details`.`quantity`" => "quantity",
		"`details`.`item`" => "item",
		"`details`.`vaule`" => "vaule",
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(
		1 => '`details`.`id`',
		2 => '`orders1`.`id`',
		3 => 3,
		4 => 4,
		5 => 5,
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = array(
		"`details`.`id`" => "id",
		"IF(    CHAR_LENGTH(`orders1`.`id`), CONCAT_WS('',   `orders1`.`id`), '') /* Order */" => "order",
		"`details`.`quantity`" => "quantity",
		"`details`.`item`" => "item",
		"`details`.`vaule`" => "vaule",
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters = array(
		"`details`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`orders1`.`id`), CONCAT_WS('',   `orders1`.`id`), '') /* Order */" => "Order",
		"`details`.`quantity`" => "Quantity",
		"`details`.`item`" => "Item",
		"`details`.`vaule`" => "Vaule",
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS = array(
		"`details`.`id`" => "id",
		"IF(    CHAR_LENGTH(`orders1`.`id`), CONCAT_WS('',   `orders1`.`id`), '') /* Order */" => "order",
		"`details`.`quantity`" => "quantity",
		"`details`.`item`" => "item",
		"`details`.`vaule`" => "vaule",
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array('order' => 'Order', );

	$x->QueryFrom = "`details` LEFT JOIN `orders` as orders1 ON `orders1`.`id`=`details`.`order` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "details_view.php";
	$x->RedirectAfterInsert = "details_view.php?SelectedID=#ID#";
	$x->TableTitle = "Details";
	$x->TableIcon = "resources/table_icons/installer_box.png";
	$x->PrimaryKey = "`details`.`id`";

	$x->ColWidth   = array(  150, 150, 150, 150);
	$x->ColCaption = array("Order", "Quantity", "Item", "Vaule");
	$x->ColFieldName = array('order', 'quantity', 'item', 'vaule');
	$x->ColNumber  = array(2, 3, 4, 5);

	// template paths below are based on the app main directory
	$x->Template = 'templates/details_templateTV.html';
	$x->SelectedTemplate = 'templates/details_templateTVS.html';
	$x->TemplateDV = 'templates/details_templateDV.html';
	$x->TemplateDVP = 'templates/details_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HighlightColor = '#FFF0C2';
	$x->HasCalculatedFields = false;

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))) { $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])) { // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `details`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='details' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])) { // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `details`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='details' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3) { // view all
		// no further action
	}elseif($perm[2]==0) { // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`details`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: details_init
	$render=TRUE;
	if(function_exists('details_init')) {
		$args=array();
		$render=details_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: details_header
	$headerCode='';
	if(function_exists('details_header')) {
		$args=array();
		$headerCode=details_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode) {
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: details_footer
	$footerCode='';
	if(function_exists('details_footer')) {
		$args=array();
		$footerCode=details_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode) {
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>