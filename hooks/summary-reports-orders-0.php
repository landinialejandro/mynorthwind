<?php
	/* Include Requeried files */
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");
	include("{$hooks_dir}/SummaryReport.php");
	
	$x = new StdClass;
	$x->TableTitle = "Orders count";
	include_once("{$hooks_dir}/../header.php");
	
	if($_REQUEST["row_numbers"] != 1) $_REQUEST["row_numbers"] = 0;
	if(!in_array($_REQUEST["label_align"], array("center", "left"))) $_REQUEST["label_align"] = "right";
	if(!in_array($_REQUEST["value_align"], array("center", "left"))) $_REQUEST["value_align"] = "right";

	$order_by = "label";
	if(isset($_REQUEST["order-by"])){
		$order_by = makeSafe($_REQUEST["order-by"]);
	}
	if(isset($_REQUEST["sorting-order"])){
		$sorting_order = makeSafe($_REQUEST["sorting-order"]);
	}

	$filterable_fields = array (
		0 => 'id',
		1 => 'orderNumber',
		2 => 'customer',
	);
	$config_array = array(
		'title' => 'Orders count',
		'table' => 'orders',
		'label' => 'customer',
		'group_function' => 'count',
		'caption1' => 'Customer',
		'caption2' => 'Count of Orders',
		'date_format' => 'm-d-Y',
		'date_separator' => '/',
		'jsmoment_date_format' => 'mm-dd-yyyy',
		'order_by' => $order_by,
		'sorting_order' => $sorting_order,
		'label_field_index' =>'3',
		'filterable_fields' =>$filterable_fields
	);
	$report = new SummaryReport($config_array);
	echo "<div class='row'>";
	echo $report->render();
	echo "</div>";

	include_once("{$hooks_dir}/../footer.php");
