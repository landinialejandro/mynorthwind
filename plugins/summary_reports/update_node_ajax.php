<?php
	include(dirname(__FILE__).'/summary_reports.php');
	
	/*
		$_REQUEST includes the following:
		axp: md5 hash of project
		report-title
		table_name: source table (the table containing the report) 
		table-index: index of source table
		group-table
		previous-reports: json-encoded list of other reports for this table
		label: field name used as label field
		label-field-index: index of label field
		first-caption: label of group-by column
		second-caption: label of value column
		how-to-summarize: grouping function
		group-array: names of groups allowed to access report, one per line
		look-up-table: in csae label field is a lookup field, this is its parent table
		look-up-value: parentCaption1 fieldname of look-up-table
		date-field
		date-field-index
		report-id: index of current report
	*/
	
	$summary_reports = new summary_reports(
	array(
		  'title' => 'Summary Reports',
		  'name' => 'summary_reports', 
		  'logo' => 'summary_reports-logo-lg.png' 
	));
	
	/* grant access to the groups 'Admins' only */
	if (!$summary_reports->is_admin() ){
		echo  'Access denied';
		exit;
	}
	
	$axp_md5 =  $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports -> get_xml_file( $axp_md5 , $projectFile );
	
	$node = new stdClass();
	$node -> title = strip_tags( $_REQUEST['report-title'] );
	$node -> table = makeSafe( $_REQUEST['table_name'] );
	$node -> table_index = intval( $_REQUEST['table-index'] ) ;
	$table_index = intval( $_REQUEST['table-index'] );
	
	$group_table = $_REQUEST['group-table'];
	if(in_array($group_table, $summary_reports -> get_table_names ($xmlFile))){
		$node -> parent_table = $group_table;	
	}

	if( isset( $_REQUEST['previous-reports'] ) ){
		$previous_reports = $_REQUEST['previous-reports'];	
	}
	$all_reports = json_decode( $previous_reports );
	if($all_reports === null) $all_reports = array();
	
	$table_fields = $summary_reports -> get_table_fields ( $node -> table );
	if($group_table){
		$table_fields = $summary_reports -> get_table_fields ( $group_table );
	}
	$node -> label = $table_fielpds[0]; 
	if(in_array($_REQUEST['label'] , $table_fields )) $node -> label = $_REQUEST['label']; 

	$node -> caption1 = $_REQUEST['first-caption'];
	$node -> caption2 = makeSafe( $_REQUEST['second-caption'] );
	$node -> group_function = makeSafe( $_REQUEST['how-to-summarize'] );
	$node -> group_function_field = makeSafe( $_REQUEST['summarized-value'] );

	//get it from users
	if( isset( $_REQUEST['group-array'] ) ){	
		$node -> group_array = makeSafe( $_REQUEST['group-array']  );
		$node -> group_array = str_replace( array('\r', '\n'), '%GS%', $node -> group_array );
		$node -> group_array = explode( '%GS%' , $node->group_array );
		for( $i=0 ; $i<count( $node->group_array );$i++ ){
			if( strlen( $node->group_array[$i] ) === 0){
				unset( $node -> group_array[$i] );
			}
		}
	 
	}
	if( isset( $_REQUEST['look-up-table'] ) ){
		$node -> look_up_table = makeSafe( $_REQUEST['look-up-table'] );
	}
	 
	if( isset( $_REQUEST['look-up-value'] ) ){
		$node -> look_up_value = makeSafe( $_REQUEST['look-up-value'] );
	}

	if( isset( $_REQUEST['label-field-index'] ) ){
		$node -> label_field_index = makeSafe( $_REQUEST['label-field-index'] );
	}

	if( isset( $_REQUEST['date-field']) && $_REQUEST['date-field'] != ''){
		$node -> date_field = makeSafe( $_REQUEST['date-field'] );
		if( isset( $_REQUEST['date-field-index'] ) ){	
			$node -> date_field_index = makeSafe( $_REQUEST['date-field-index'] );
		} 
	}
	/* some URL Parameters*/
	
	$node -> join_statment = '';
	$all_lookup_fields = '';
	$date_separators = array('1'=>'-','2'=>' ','3'=>'.','4'=>'/','5'=>',');	
	 
	$date_separator_index = (string)$xmlFile -> dateSeparator;
	$node -> date_separator = $date_separators[ $date_separator_index ];
	
	$path = array ();
	if( isset( $node -> parent_table ) ){
		$path = $summary_reports -> find_path( $node -> table, $node -> parent_table );
	}
 
	for($i=0; $i<count($path) - 1; $i++){ 
		$node->join_statment .= ' join ' . get_join_statment($path[$i], $path[$i+1], $summary_reports, $xmlFile);
	}
 
	$node->join_statment = $path[0] . $node->join_statment;

	if( $_REQUEST['report-id']!=='' ){
		 $report_id = intval( makeSafe( $_REQUEST['report-id'] ) );
		 $all_reports[$report_id] = $node;
		 	
	}else{
		array_push( $all_reports , $node );
	}

	$json_nodes = json_encode( $all_reports );
	 
	/* update node */

	$nodeData=array(
		'projectName'=> $projectFile,
		'tableIndex'=> $table_index,
		'nodeName'=> 'report_details',
		'pluginName'=> 'summary_reports',
		'data'=> $json_nodes
		) ;
	 
	$summary_reports -> update_project_plugin_node( $nodeData );
	echo $json_nodes;


	/**
		@param $table1 string, name of table
		@param $table2 string, name of table
		@param $plugin plugin
		@param $project_xml xml object representing the project
		@return string of join statement of table1 and table2
	*/	 
	function get_join_statment($table1, $table2, &$plugin, &$project_xml){	
		$join_statment = '';
		$lookup_fields = $plugin->get_fk_fields();
		$table2_lookups = $lookup_fields[$table2];
		$joined_table = $table2;
		$table2_children = array();
		
		foreach($table2_lookups as $key => $value){
			$table2_children[] = $value;
		}
		
		/* if table2 is parent of table1, swap tables */
		if( in_array( $table1 , $table2_children ) ){
			$temp = $table1;
			$table1 = $table2;
			$table2 = $temp;
		}
		
		$table1_lookups = $lookup_fields[$table1];
		
		foreach($table1_lookups as $key1 => $value1){
			if($value1 == $table2){
				$table2_pk = $plugin->get_pk_field_name($table2);
				$join_statment .= "`{$joined_table}` on `{$table1}`.`{$key1}` = `{$table2}`.`{$table2_pk}`";
			}
		}

		return $join_statment;
	}  
  
	
	
	
	
	
	
	
	
	
	