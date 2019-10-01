<?php include(dirname(__FILE__) . '/header.php'); 

$summary_reports = new summary_reports(array(
        'title' => 'Summary Reports',
        'name' => 'summary_reports',
        'logo' => 'summary_reports-logo-lg.png',
        'output_path' => $_REQUEST['path']
    ));
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports->get_xml_file($axp_md5, $projectFile);
?>

<div class="page-header row">
	<h1><img src="summary_reports-logo-lg.png" style="height: 1em;"> Summary Reports</h1>
	<h1>
		<a href="index.php">Projects</a> &gt; 
		<a href="project.php?axp=<?php echo urlencode($axp_md5); ?>"><?php echo substr($projectFile, 0, -4); ?></a> &gt;
		<a href="output-folder.php?axp=<?php echo urlencode($axp_md5); ?>">Output folder</a> &gt;
		Generating files
	</h1>
</div>

<?php
	function check_group_array_type($group_array){
		if(is_object($group_array)){
			return $array = json_decode(json_encode($group_array), True);
		}
		return $group_array; 	
	}
	
	$date_format = intval($xmlFile->dateFormat[0]); 
	$path = $_REQUEST['path'];
	if(!$summary_reports->is_appgini_app($path)){
		echo $summary_reports->error_message('Invalid application path!');
		include(dirname(__FILE__) . '/footer.php');
		exit;
	}
	$date_formats = array(
		"1" => "yyyy-mm-dd",
		"2" => "dd-mm-yyyy",
		"3" => "mm-dd-yyyy"
	);
	$initial_date_format = array(
		"1" => array("from" => "Y-m-01", "to" => "Y-m-d"),
		"2" => array("from" => "01-m-Y", "to" => "d-m-Y"),
		"3" => array("from" => "m-01-Y", "to" => "m-d-Y")
	);
	$initial_from = $initial_date_format[$date_format]["from"];
	$initial_to = $initial_date_format[$date_format]["to"];
	/* Copying SummaryReports Class */
	$source_class = dirname(__FILE__) . '/app-resources/SummaryReport.php';
	$dest_class = $path.'/hooks/SummaryReport.php';
	$summary_reports->copy_file($source_class, $dest_class, true);
	
	/* Copying Picker Resources */
	$first_src = dirname(__FILE__).'/app-resources/date_picker_dirs';
	$first_dst = $path.'/resources'; 
	$summary_reports->recurse_copy($first_src,$first_dst, true); 
	
	/* Copying SummaryReports Logo */
	$logo_source_file = dirname(__FILE__)."/summary_reports-logo-md.png";
	$logo_destination_file = $path.'/hooks/summary_reports-logo-md.png';
	$summary_reports->copy_file($logo_source_file, $logo_destination_file, true);
	
	/* Generating summary-reports.php File */
	$summary_reports_file = '<' . '?php
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");

	$x = new StdClass;
	$x->TableTitle = "Summary Reports";
	include_once("{$hooks_dir}/../header.php");
	$user_data = getMemberInfo();
	$user_group = strtolower($user_data["group"]);' .
	"\n?>\n\n";
	
	$summary_reports_file .= '<div class="page-header"><h1>Summary Reports</h1></div>';
	
	/* Iterating over the tables to generate reports files */
	$summary_reports_links_groups = array();
	for($i = 0; $i < count($xmlFile->table) ; $i++){
		/* acess report_details node and convert it into obj*/
		$json_node = $xmlFile->table[$i]->plugins->summary_reports->report_details;
		if( empty( $json_node ) ) continue;
		$table_caption = $xmlFile->table[$i]->caption;
		$node = json_decode($json_node);
		
	 	$table_fields =$xmlFile->table[$i]->field;
		$filterable_fields = array ();
		foreach ($table_fields as $field){
			if ( $field->notFiltered == "True") continue ;
			if ( $field->tableImage == "True") continue ;
			if ( $field->detailImage == "True") continue ;
			array_push ( $filterable_fields,(string) $field ->name );
			 
		}

		ob_start();
		?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="text-center text-bold" style="font-size: 1.5em; line-height: 2em;"><?php echo $table_caption; ?></div>
			</div>
			<div class="panel-body">
				<div class="panel-body-description">
					<div class="row">
						<?php
						$summary_reports_file .= ob_get_clean();
						 
						for($j = 0; $j < count($node); $j++){
							
							$table_name = $xmlFile->table[$i]->name;
							$report_title = $node[$j]->title;
							
							$generated_report = "summary-reports-{$table_name}-{$j}.php";
							$summary_reports->progress_log->add("Generating {$generated_report} ", 'text-info');
							/* Data will be written in report files */
							$generated_report_content = 
	'<' . '?php
	/* Include Requeried files */
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");
	include("{$hooks_dir}/SummaryReport.php");
	
	$x = new StdClass;
	$x->TableTitle = "' . html_attr($report_title) . '";
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
	}'."\n\n\t" . '$filterable_fields = ' .
	str_replace(
		array("\n  ", "\n"), 
		array("\n\t", "\n\t"), 
		var_export($filterable_fields, true)
	) . ";"; 
							
							$groups_classes = "all-groups";					
							if(count($node[$j]->group_array) == 0){
								array_push($summary_reports_links_groups, '*');
							}
							if(count($node[$j] -> group_array) > 0) {
								$group_array = check_group_array_type($node[$j] -> group_array);
								$summary_reports_links_groups = array_merge(
									$summary_reports_links_groups, $group_array);
								$generated_report_content .= "\t" . '$groups_array = ' .
									str_replace(
										array("\n  ", "\n"), 
										array("\n\t", "\n\t"), 
										var_export($group_array, true)
									) . ";";	
								
								$generated_report_content .= '
	$memberInfo = getMemberInfo();
	if(!in_array(strtolower($memberInfo["group"]), array_map("strtolower", $groups_array))){
		header("Location: ../index.php");
		exit;
	}' . "\n\n";
								$groups_classes = '';
								foreach($group_array as $group){
									$groups_classes .= " " . strtolower($group);
								}
						 
							}
							
							ob_start();
							?><div class ="col-xs-12 col-md-4 col-lg-4">
							<a href ="<?php echo $generated_report; ?>" class="btn btn-success <?php echo $groups_classes; ?> btn-block btn-lg vspacer-lg summary-reports" style="padding-top: 1em; padding-bottom: 1em;">
								<i class ="glyphicon glyphicon-th"></i> <?php echo $report_title ?>
							</a>
						</div>
							<?php
							$summary_reports_file .= ob_get_clean();
							 
							if(isset($node[$j]->date_field)){
								$generated_report_content .= '

	$from = makeSafe($_REQUEST["from"]);
	$to = makeSafe($_REQUEST["to"]);
	
	/* if period 1 not set while 2 is set, shift 2 to 1 */
	if(!isset($_REQUEST["period-one-from"]) && !isset($_REQUEST["period-one-to"])) {
		if(isset($_REQUEST["period-two-from"]))
			$_REQUEST["period-one-from"] = $_REQUEST["period-two-from"];
		if(isset($_REQUEST["period-two-to"]))
			$_REQUEST["period-one-to"] = $_REQUEST["period-two-to"];
		unset($_REQUEST["period-two-from"]);
		unset($_REQUEST["period-two-to"]);
		unset($_REQUEST["comparison-period-2"]);
		$_REQUEST["comparison-period-1"] = 1;
	}

	if(isset($_REQUEST["period-one-from"])){
		$period_one_from = makeSafe($_REQUEST["period-one-from"]);
	}
	if(isset($_REQUEST["period-one-to"])){
		$period_one_to = makeSafe($_REQUEST["period-one-to"]);
	}
	
	if(isset($_REQUEST["period-two-from"])){
		$period_two_from = makeSafe($_REQUEST["period-two-from"]);
	}
	if(isset($_REQUEST["period-two-to"])){
		$period_two_to = makeSafe($_REQUEST["period-two-to"]);
	}
	
	if(!isset($_REQUEST["apply"])){
		$from = date("'.$initial_to.'", strtotime("first day of this month"));
		$to = date("'.$initial_to.'", strtotime("this day"));
		$period_one_from = date("'.$initial_to.'", strtotime("first day of previous month"));
		$period_one_to = date("'.$initial_to.'", strtotime("this day last month"));
		$period_two_from = date("'.$initial_to.'", strtotime("first day of this month last year"));
		$period_two_to = date("'.$initial_to.'", strtotime("this day last year"));
		$comparison_period_one = "";
		$_REQUEST["comparison-period-1"] = "";
		$_REQUEST["comparison-period-2"] = "";
	
	}
	';
							}

							$generated_report_content .= "\n\t\$config_array = array(
		'title' => '" . addslashes($node[$j]->title) . "',
		'table' => '{$node[$j]->table}',
		'label' => '{$node[$j]->label}',
		'group_function' => '{$node[$j]->group_function}',
		'caption1' => '{$node[$j]->caption1}',
		'caption2' => '{$node[$j]->caption2}',
		'date_format' => '{$initial_to}',
		'date_separator' => '{$node[$j]->date_separator}',
		'jsmoment_date_format' => '{$date_formats[$date_format]}',
		'order_by' => \$order_by,
		'sorting_order' => \$sorting_order";
					
							if(isset($node[$j]->group_function_field)){
								$generated_report_content .= ",\n\t\t'group_function_field' =>'{$node[$j]->group_function_field}'";
							}
							
							if(isset($node[$j]->parent_table)){
								$generated_report_content .= ",\n\t\t'parent_table' =>'{$node[$j]->parent_table}'";
								$generated_report_content .=",\n\t\t'join_statment' =>'{$node[$j]->join_statment}'";
							}
							if(!isset($node[$j]->parent_table)){
								$generated_report_content .= ",\n\t\t'label_field_index' =>'{$node[$j]->label_field_index}'";
								$generated_report_content .= ",\n\t\t'filterable_fields' =>\$filterable_fields";
							}
							
							if(isset($node[$j]->date_field)){
								$generated_report_content .= ",\n\t\t'date_field' =>'{$node[$j]->date_field}'";
								$generated_report_content .= ",\n\t\t'start_date' =>\$from";
								$generated_report_content .= ",\n\t\t'end_date' =>\$to";
								$generated_report_content .=",\n\t\t'period-one-from'=>\$period_one_from";
								$generated_report_content .=",\n\t\t'period-one-to'=>\$period_one_to";
								$generated_report_content .=",\n\t\t'period-two-from'=>\$period_two_from";
								$generated_report_content .=",\n\t\t'period-two-to'=>\$period_two_to";
							}
							if(!isset($node[$j]->parent_table)&&isset($node[$j]->date_field)){
								$generated_report_content .= ",\n\t\t'date_field_index' =>'{$node[$j]->date_field_index}'";
							}
							if($node[$j]->look_up_table){
								
								$generated_report_content .= ",\n\t\t'look_up_table' =>'{$node[$j]->look_up_table}'";
						
							}
							if($node[$j]->look_up_value){
								$generated_report_content .= ",\n\t\t'look_up_value' =>'{$node[$j]->look_up_value}'";
								$label_field_index = intval( $node[$j]->label_field_index ) -1;
								$parent_caption_field = $xmlFile->table[$node[$j]->table_index]->
									field[ $label_field_index ]->parentCaptionField;
								$parent_caption_field2 = $xmlFile->table[$node[$j]->table_index]->
									field[ $label_field_index ]->parentCaptionField2;
								$parent_caption_separator = $xmlFile->table[$node[$j]->table_index]->
									field[ $label_field_index ]->parentCaptionSeparator;	
								$generated_report_content .= ",\n\t\t'parent_caption_field' =>'{$parent_caption_field}'";
								$generated_report_content .= ",\n\t\t'parent_caption_field2' =>'{$parent_caption_field2}'";
								$generated_report_content .= ",\n\t\t'parent_caption_separator' =>'{$parent_caption_separator}'";
							}
							$generated_report_content .= "\n\t)".";\n";
							$generated_report_content .= "\t".'$report = new SummaryReport($config_array);'."\n";
							$generated_report_content .="\t".'echo $report->render();'."\n\n";
							
							$generated_report_content .="\t".'include_once("{$hooks_dir}/../footer.php");'."\n";	
							$generated_report_link = $path.'/hooks/'.$generated_report;
							
							file_put_contents($generated_report_link, $generated_report_content);
							
							if(file_exists($path.'/hooks/'.$generated_report)){
								$summary_reports->progress_log->ok();
							}else{
								$summary_reports->progress_log->failed();
							}
							
						}
		ob_start(); ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
		<?php
		$summary_reports_file .= ob_get_clean();	 
	}
	
	/* Adding summary-reports.php links in homepage and navigation menu */
	$table_groups = explode(",",$xmlFile->groups);
	$table_group = $table_groups[0];
	$summary_reports_links_groups=array_unique($summary_reports_links_groups);
	
	if (in_array('*', $summary_reports_links_groups)) {
		$summary_reports_links_groups=array('*');
	}
 
	$summary_reports->add_link('links-home', array(
		'url' => 'hooks/summary-reports.php', 
		'title' => 'Summary Reports', 
		'groups'=>$summary_reports_links_groups,
		'table_group' => $table_group,
		'description' => '',
		'grid_column_classes' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
		'panel_classes' => '',
		'link_classes' => '',
		'icon' => 'hooks/summary_reports-logo-md.png',
	));
	$summary_reports->add_link('links-navmenu', array(
		'url' => 'hooks/summary-reports.php',
		'title' => 'Summary Reports',
		'groups'=>$summary_reports_links_groups,
		'icon' => 'hooks/summary_reports-logo-md.png',
	));
	
	
	
	ob_start();?> 
	<script>

		var user_group= [?php echo json_encode($user_group) ?]  ;

		$j(function(){ 
			$j( ".panel a" ).not('.'+user_group).not('.all-groups').parent().remove();
			$j('.panel').each(function(){
				if($j(this).find('a').length == 0){
					$j(this).remove();
				}
 
			}) 
		})

	</script>
	<?php
	$summary_reports_file .= ob_get_clean();	
	$summary_reports_file =  str_replace('[?php','<?php',$summary_reports_file);
	$summary_reports_file =  str_replace('?]','?>',$summary_reports_file);
		 
		
	
	$summary_reports_file .= "\n\n".'<?php include_once("$hooks_dir/../footer.php"); ?>';
	$summary_reports->progress_log->add('Generating summary-reports.php  ... ', 'text-info');
	if(file_put_contents($path.'/hooks/summary-reports.php', $summary_reports_file)){
		$summary_reports->progress_log->ok();
	}else{
		$summary_reports->progress_log->failed();
	}	
	echo $summary_reports->progress_log->show();
?>
<?php include(dirname(__FILE__) . '/footer.php'); ?>