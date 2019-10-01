<?php
	include(dirname(__FILE__) . '/header.php');

	// validate project name
	if (!isset($_REQUEST['axp']) || !preg_match('/^[a-f0-9]{32}$/i', $_REQUEST['axp'])){
		echo '<br>' . $summary_reports->error_message('Project file not found.');
		exit;
	}
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $summary_reports->get_xml_file($axp_md5, $projectFile);
//-----------------------------------------------------------------------------------------
?>

<script>
	var project = <?php echo json_encode($xmlFile); ?>;
	var axp_md5 = <?php echo json_encode($axp_md5); ?>; 
	
	function resetReportForm(){
		/* empty hidden inputs of report form */
		$j(
			"#table-index, #report-id, #first-caption," + 
			"#second-caption, #look-up-table, #look-up-value, #label-field-index" +
			"#date-field-index"
		).val('');
		
		/* hide any validation errors */
		$j('.validation-error').addClass('hidden');
		
		/* empty report title*/
		$j('#report-title').val('').focus();
		
		/* disable and mute 'Group Table' option */
		$j('#single-table').prop('checked', false);
		$j('#group-table').empty();
		$j('#group-table').prop('disabled', true);
		$j('#group-table-label').addClass( "text-muted" );
		
		/* empty 'Label Field' dropdown */
		$j('#label').empty();
		
		/* Select first value of 'how-to-summarize' */
		$j('#how-to-summarize option').eq(0).prop('selected', true);
		
		/* empty 'Summerized value' and mute */
		$j('#summarized-value').empty();
		$j('#summarized-value').prop('disabled', true);
		$j('#summarized-value-label').addClass("text-muted");	
		
		/* empty 'Date field' */
		$j('#date-field').empty();
		
		/* empty 'Groups' textarea */
		$j('#group-array').val('');
	}
	 
	function get_caption( table , field_name ){
		
		if( field_name === undefined ){
			for ( var key in project.table ) {
				if ( project.table[key].name === table ) {
					return project.table[key].caption; 
				}
			} 
		} 
		
		var table_fields = project.table[table].field;
		
		for (var key in table_fields ) {
			if ( table_fields[key].name == field_name ) {
				return table_fields[key].caption; 
			}
		}
	}
	
	
	function get_field_index(table_index, field_name ){
		var table_fields = project.table[table_index].field;
		
		for (var key in table_fields ) {
			if ( table_fields[key].name == field_name ) return key; 	
		}
		return false;
	}
	
	function is_lookup_field(table_index, field_name){
		
		var field_index = get_field_index(table_index, field_name );
		if( field_index === false ) return false ;
		
		if( typeof project.table[table_index].field[field_index].parentTable!="string" ){
			return false;
		}
		
		return true;
	}
	
	//get lookup table
	function get_lookup_table(table_index, field_name){
		var field_index = get_field_index(table_index, field_name);
		return project.table[table_index].field[field_index].parentTable;
		 
	}
	
	//get lookup value
	
	function get_lookup_value(table_index, field_name){
	
		var field_index = get_field_index(table_index, field_name);
		return project.table[table_index].field[field_index].parentCaptionField;
		 
	}
	
	function get_table_index(table_name){
		for ( var key in project.table ) {
				if ( project.table[key].name === table_name) {
					return parseInt( key ); 
				}
			}
	}
	
	function get_group_function_caption( group_function) {
		var aggregate_functions = {'Average':'avg','Count':'count','Sum':'sum','Maximum':'max','Minimum':'min'};
		
		for ( var key in aggregate_functions ) {
				if (aggregate_functions[key] == group_function) {
					return key ;
				} 
			}	
	}
	
	function fill_how_to_summarize_field(){
		
		var aggregate_functions = {
			'Count': 'count',
			'Sum': 'sum',
			'Average': 'avg',
			'Maximum': 'max',
			'Minimum':'min'
		};
		/* Fill How To Summrize Field*/
		$j.each( aggregate_functions, function( key , value ) { 	
			$j( '#how-to-summarize' ).append($j( "<option></option>" ).attr( "value" , value ).text( key )); 
		});
		
	}
	
	function get_table_ancestors( table , callback ){
		var table_ancestors	= {};
		if(window.AppGini === undefined) window.AppGini = {};
		AppGini.get_table_ancestors = AppGini.get_table_ancestors || {};
		
		if(AppGini.get_table_ancestors[table] != undefined){
			processTableAncestors(table, callback);
			return;
		}

		/* Send ajax request to update the node */
		$j.ajax({
			url: 'table-ancestors-ajax.php?axp=' + axp_md5 + '&table_name=' + table_name,
			success: function(data){
				AppGini.get_table_ancestors[table] = JSON.parse(data);
				processTableAncestors(table, callback);
			}
		});
	}
	
	function processTableAncestors(table, callback){
		$j('#group-table').empty();
		$j('#group-table').append($j("<option></option>").attr("value", '').text('')); 
		$j.each(AppGini.get_table_ancestors[table], function(index, value) {   
			$j('#group-table').append($j("<option></option>").attr("value", value).text(value)); 
			if(typeof(callback) == 'function'){ callback(); }
		});
	}

	function fill_date_summarized_value_fields( table ){
		var dates = {};
		var calculable_labels = {};
		var table_fields = [];
		var tables = project[ "table" ];
	
		/* Detects the type of the passed parmeter and retrives it's fields  */
		if( typeof table == 'string' ){	
			for ( var i = 0; i < tables.length ; i++ ){
				if( tables[i]["name"] == table ){
					table_fields = tables[i]["field"];
				}
			}
		}else{
			table_fields = tables[table]["field"];
		}
		
		/* Loop over table fields and categoriez them */
		
		for(var j = 0;j < table_fields.length ; j++)
		{	 
			var field_type = parseInt( table_fields[j]["dataType"] );
			
			if( field_type == 9 || field_type == 10 ){	
				dates[ table_fields[j]["caption"] ] = table_fields[j]["name"];
			}else if( ( field_type >= 1 && field_type <= 8) && typeof( table_fields[j].parentTable) == 'object' ){
				calculable_labels[ table_fields[j]["caption"] ] = table_fields[j]["name"];
			}
		}
		/* update summarized_value select */
		$j("<option></option>").appendTo('#summarized-value');
		$j.each( calculable_labels , function( key , value ) {
			$j( "<option></option>" ).attr( "value" , value ).text( key ).appendTo( '#summarized-value' ); 
		});
		
		$j( '#date-field' ).append( $j( "<option></option>" ).attr( "value" ,"" ).text( "Don't filter the report by date" ) );		
		/*  update date select  */
	 	$j.each( dates, function( key , value ) {   
			$j( '#date-field' ).append( $j( "<option></option>" ).attr( "value" , value ).text( key ) ); 
		}); 	
			
	}
		
	function fill_label_field(table){
		$j( '#label' ).empty();
		var labels = {};
		var table_fields = [];
		var tables = project[ "table" ];
	
		/* Detects the type of the passed parmeter and retrives it's fields  */
		if( typeof table == 'string' ){	
			for( var i = 0 ; i < tables.length ; i++ ){
				if( tables[i]["name"] == table ){
					table_fields = tables[i]["field"];
				}
			}
		}else{
			table_fields = tables[table]["field"];
		}
		
		/* Loop over table fields and categoriez them */
		
		for( var j = 0; j < table_fields.length ; j++ ){
			labels[ table_fields[j]["caption"] ] = table_fields[j]["name"];
		}

		/* update labels select */
		$j( '#label' ).append( $j("<option></option>" ).attr( "value" , '' ).text('')); 
		$j.each( labels , function( key , value ) { 
			$j( '#label' ).append( $j( "<option></option>" ).attr( "value" , value ).text( key ) ); 
		});			
	}
	
	function empty_table_fields(){
		$j( '#label' ).empty();
		$j( '#date-field' ).empty();
		$j( '#summarized-value' ).empty();
	}
	
	function table_has_no_reports(){
		$j("#table-reports").html(
			'<div class="alert alert-warning">' +
				'This table has no reports configured yet. ' +
				'<a href="#" onclick="$j(\'#add-report\').click(); return false;">Create one now!</a>' +
			'</div>'
		);
		
		return false;
	}
	
	function show_tables_reports ( id ){
		selected_table = id;
		table_name = project.table[selected_table].name;
		var table = project["table"][selected_table];
		
		$j("#previous-reports").val("");
		
		if( table.plugins === undefined ) return table_has_no_reports();
		
		var table_plugins = project["table"][selected_table]["plugins"];
		if( table_plugins.summary_reports === undefined )
			return table_has_no_reports();
		
		if( table_plugins.summary_reports.report_details === undefined )
			return table_has_no_reports();

		if( table_plugins.summary_reports.report_details.length <= 17 )
			return table_has_no_reports();
	 
		var report_details_json = table_plugins.summary_reports.report_details;
		var report_details = JSON.parse(report_details_json);
		$j( "#previous-reports" ).val( report_details_json );
		
		var table = '';

		/* loop over reports */
		for( var i = 0; i < report_details.length ; i++ ) {
			/* Get the values and push them in array*/
			var report_config_values = {};	
			var title = report_details[i].title;
			var table_index = selected_table;
			/* group table caption, label field caption */
			var gtc = '', lfc = '';
			/* grouping function */
			var gf = get_group_function_caption(report_details[i].group_function);
			/* selected table caption */
			var stc = project.table[selected_table].caption;
			
			gtc = project.table[table_index].caption;
			if( report_details[i].parent_table !== undefined ){
				gtc = get_caption(report_details[i].parent_table);
				table_index = get_table_index(report_details[i].parent_table);
			}
			
			lfc = get_caption(table_index, report_details[i].label);
			
			report_config_values["How / what to summarize?"] = (
				gf !== 'Count' ?
					gf + ' of ' + stc + ' ' + 
					'<i class="glyphicon glyphicon-chevron-right"></i> ' +
					get_caption(selected_table, report_details[i].group_function_field)
				:
					'Count of records of ' + stc + ' table'
			);
			
			report_config_values["Grouped by"] = gtc + ' <i class="glyphicon glyphicon-chevron-right"></i>  ' + lfc;

			report_config_values["Date field used to filter the report"] = 'No filtering by dates';
			if(report_details[i].date_field !== undefined){
				report_config_values["Date field used to filter the report"] = get_caption(selected_table,report_details[i].date_field);
			}

			report_config_values["Which groups can access this report?"] = '<span class="text-danger">All groups</span>';
			if(report_details[i].group_array !== undefined){
				if(report_details[i].group_array.length)
					report_config_values["Which groups can access this report?"] = report_details[i].group_array.join(',');
			}

			/* loop over table config and draw it */	
			var table_conf_values = '';
			for (var key in report_config_values) {
				table_conf_values += '' +
					'<tr>' +
						'<th class="text-right" style="width: 50%;">' + key + '</th>' +
						'<td>' + report_config_values[key] + '</td>' +
					'</tr>';	 
			}				

			table +=	'<div class="panel panel-success">' +
							'<div class="panel-heading">' +
								'<h3 class="panel-title">' +
									'<i class="glyphicon glyphicon-list-alt"></i> ' + title + 
									'<div class="btn-group pull-right">' +
										'<button title="Edit report" type="button" class="btn btn-default btn-sm edit-report" id="edit-table' + i + '" data-toggle="modal" data-target="#report-modal" data-id="' + i + '"><span class="glyphicon glyphicon-pencil text-primary"></span> <span class="text-primary">Edit</span></button>' +
										'<button title="Delete report" type="button" class="btn btn-default btn-sm delete-report" id="delete-table' + i + '" data-id="' + i + '"><span class="glyphicon glyphicon-trash text-danger"></span> <span class="text-danger">Delete</span></button>' +
									'</div>' +
									'<div class="clearfix"></div>' +
								'</h3>' +
							'</div>' +
							'<div class="panel-body">' +
								'<table class="table" style="margin-bottom: 0;">' +
									table_conf_values +
								'</table>' +
							'</div>' +
						'</div>' +							
						'';
		}
		 	
		$j("#table-reports").html(table);

	}
	
	function check_table_has_summarized_value (){
		if($j('#summarized-value option').length == 1 &&  $j('#how-to-summarize').val() != 'count' ){
			$j('#summarized-value-validation' ).removeClass( "hidden" );
		}else{
			$j('#summarized-value-validation' ).addClass( "hidden" );
		}
	}
	
	function getSelectdReportDetails(reportId){
		var report_details_json = project.table[selected_table].plugins.summary_reports.report_details;
		var all_reports_details = JSON.parse(report_details_json);
		return all_reports_details[reportId];
	}
	
	$j(function(){
		var table = $j('#group-table').val();
		fill_how_to_summarize_field();
		
		/* Triggring Add and Edit Modal Events */
	 	$j('#group-table').on('change',function(){
	
			var table=$j('#group-table').val();
			var selected_table_name=project["table"][selected_table]["name"];
			$j('#label').empty();
			fill_label_field(table);
		}); 
		
		/* Change How to summarize event*/
		$j('#how-to-summarize').on('change',function(){
			var how_to_summarize=$j('#how-to-summarize').val();
			
			if(how_to_summarize =='count'){
				$j('#summarized-value').prop('disabled', true);
				$j('#summarized-value-label').addClass( "text-muted" );
				$j('#summarized-value option').eq(0).prop('selected', true);
			}else{
				$j('#summarized-value').prop('disabled', false);
				$j('#summarized-value-label').removeClass( "text-muted" );
			}
			check_table_has_summarized_value();
				
		});
		
		/* If user check making a repotrt on single table , Disable Report of field */
		$j('#single-table').on('click', function(){
			if($j('#single-table').prop('checked')) {
				/* Fill group table field */
				$j( '#label' ).empty();
				$j('#group-table').prop('disabled', false);
				$j('#group-table-label').removeClass( "text-muted" );
				get_table_ancestors(table_name, function(){
					var reportId = $j('#report-id').val();
					if(!reportId) return;
					var selected_report_details = getSelectdReportDetails(reportId);
					$j("#group-table option[value=" + selected_report_details.parent_table + "]").prop("selected", true);
					fill_label_field(get_table_index(selected_report_details.parent_table));
					$j("#label option[value=" + selected_report_details.label + "]").prop("selected",  true);
				});
				var table = $j('#group-table').val();
				return;
			}
			
			$j('#group-table').empty();
			$j('#group-table').prop('disabled', true);
			$j('#group-table-label').addClass("text-muted");
			$j( '#label' ).empty();
			//empty_table_fields();
			//fill_date_summarized_value_fields(table_name);
			fill_label_field(table_name);		 	
		});
	
		$j('#add-report').on('click',function(){
			var table_caption = project.table[selected_table].caption;
			table_name = project.table[selected_table].name;
			
			resetReportForm();
			
			$j("#table-index").val(selected_table);
			/* Add report window title  */
			$j("#modal-title").html('Create a new <span class="text-info">' + table_caption + '</span> report');
			
			/* Fill the rest of fields */
			fill_date_summarized_value_fields(table_name);
			fill_label_field(table_name);
			check_table_has_summarized_value();
		});
				
		$j("#table-reports").on('click',".edit-report",function(){
			
			resetReportForm();		
			var report_id= $j(this).data('id');
			var selected_report_details = getSelectdReportDetails(report_id);
			$j("#table-index").val(selected_table);
			
			$j("#report-id").val(report_id);
			
			/* Set report window title  */
			$j("#modal-title").html('Edit <span class="text-info">' + selected_report_details.title + '</span> report');

			/* update report title */
			$j("#report-title").val(selected_report_details.title);
		
			fill_date_summarized_value_fields(selected_table);
			$j("#how-to-summarize option[value="+selected_report_details.group_function+"]").prop("selected", true);
		
			if(selected_report_details.group_function == 'count'){
				$j('#summarized-value').prop('disabled', true);
				$j('#summarized-value-label').addClass( "text-muted" );
				$j('#summarized-value option').eq(0).prop('selected', true);
			}else{
				$j('#summarized-value').prop('disabled', false);
				$j('#summarized-value-label').removeClass( "text-muted" );
				if(selected_report_details.group_function_field != '')
					$j("#summarized-value option[value=" + selected_report_details.group_function_field + "]").prop('selected', true);
			}
			
			
			/* Populate How to Summarized value and date fields */
			
			
			$j("#date-field option[value="+selected_report_details["date_field"]+"]").attr("selected", "selected");
			
			$j("#group-array").val(selected_report_details.group_array);
			
			if(typeof selected_report_details.group_array!=='undefined'){
				var group_array="";
				for (var key in selected_report_details.group_array){
				 group_array+=selected_report_details.group_array[key]+"\n";
				$j("#group-array").val(group_array);
				}
			}
			 if(typeof selected_report_details.parent_table == 'undefined') {
				 $j('#single-table').prop('checked',false);
				$j('#group-table').prop('disabled', true);
				$j('#group-table-label').addClass( "text-muted" );
				fill_label_field(selected_table);
				$j("#label option[value="+selected_report_details["label"]+"]").attr("selected", "selected");
				
			}else{
				$j('#single-table').prop('checked',true);
				$j('#group-table').prop('disabled',false);
				$j('#group-table-label').removeClass( "text-muted" );
				get_table_ancestors(selected_table,function(){
					$j("#group-table option[value="+selected_report_details["parent_table"]+"]").prop("selected",true);
				});	
				fill_label_field(get_table_index(selected_report_details["parent_table"]));
				$j("#label option[value="+selected_report_details["label"]+"]").attr("selected", "selected");
				
			}
		

		
		})
	
		$j("#table-reports").on('click',".delete-report",function(){		 
			if(!confirm('Are you sure you want to delete this report?')) return;
			
			var node_index = $j(this).data('id');
			var table_index = selected_table;
			
			$j(this).addClass("disabled");
		 	$j.ajax({
				type: "POST",
				url: 'delete_node_ajax.php',
				data: { 
					axp: axp_md5, 
					table_name: table_name,
					node_index: node_index,
					table_index: table_index
				},
				success: function(data){
					project.table[table_index].plugins = project.table[selected_table].plugins || {};
					project.table[table_index].plugins.summary_reports = project.table[table_index].plugins.summary_reports || {};
					project.table[table_index].plugins.summary_reports.report_details = data;
					if(table_index == selected_table){
						show_tables_reports(table_index); 
					} 
				}
			});
		}); 
		
		$j('#report-title').keyup(function(){
			if($j(this).val() != '') $j('#title-validation').addClass('hidden');
		});
		
		$j('#label').change(function(){
			if($j(this).val() != '') $j('#label-validation').addClass('hidden');
		});

		$j('#how-to-summarize, #summarized-value').change(function(){
			if($j('#summarized-value').val() != '' || $j('#how-to-summarize').val() == 'count')
				$j('#what-to-summarize-required').addClass('hidden');
		});
		
		$j('#report-values').submit(function(e){
			e.preventDefault();
			$j('#save-report').click();
		})

		$j("#save-report").click(function(){
			/* Validating report title required */
			if($j("#report-title").val() == '') {
				$j('#title-validation').removeClass('hidden');
				$j("#report-title").focus();
				return;
			}
			
			if(!$j("#summarized-value-validation").hasClass("hidden")){
				$j('#summarized-value').focus();
				return;
			}
			
			/* #how-to-summarize is not 'Count' and no #summarized-value */
			if($j('#summarized-value').val() == '' && $j('#how-to-summarize').val() != 'count'){
				$j('#what-to-summarize-required').removeClass('hidden');
				$j('#summarized-value').focus();
				return;
			}
			
			/* Don't allow empty label field */
			if($j('#label').val() == ''){
				$j('#label-validation').removeClass('hidden');
				$j('#label').focus();
				return;
			}
			
			if($j("#group-table").val() == undefined){
				var label_field=$j("#label").val();
				if(is_lookup_field($j("#table-index").val(), label_field) == false){
					$j("#look-up-table, #look-up-value").val(''); 
				}else{
					//get lookup table
					var lookup_table = get_lookup_table($j("#table-index").val(), label_field);
					$j("#look-up-table").val(lookup_table);
					
					//get lookup value
					var lookup_value = get_lookup_value($j("#table-index").val(), label_field);
					$j("#look-up-value").val(lookup_value);
				}
			}
		 
			$j('#report-modal').modal('hide');
 
			if($j("#group-table").val() != undefined){
				$j("#first-caption").val(
					get_caption(
						get_table_index(
							$j("#group-table").val()
						),
						$j("#label").val()
					)
				);
			}else{
				$j("#first-caption").val(
					get_caption(
						$j("#table-index").val(), 
						$j("#label").val()
					)
				);
			}
			
			$j('#second-caption').val(
				get_group_function_caption($j("#how-to-summarize").val()) + 
				" of " +
				get_caption(table_name)
			);

			$j('#label-field-index').val(
				parseInt(
					get_field_index(
						selected_table, 
						$j('#label').val()
					)
				) + 1
			);

			$j('#date-field-index').val(
				parseInt(
					get_field_index(
						selected_table, 
						$j('#date-field').val()
					)
				) + 1
			);
	
			$j.ajax({
				type: "POST",
				url: 'update_node_ajax.php?axp=' + axp_md5 + '&table_name=' + table_name,
				data: $j("#report-values").serialize(),
				success: function(data){
					project.table[selected_table].plugins = project.table[selected_table].plugins || {};
					project.table[selected_table].plugins.summary_reports = project.table[selected_table].plugins.summary_reports || {};
					project.table[selected_table].plugins.summary_reports.report_details = data;
					show_tables_reports(selected_table); 
				}
			});

			/* Empty new report modal */
			document.getElementById("report-values").reset();
			$j("#report-id").val('');
		}); 
 
		$j('#report-modal').on('hidden.bs.modal', function () {
			document.getElementById("report-values").reset();
			$j("#report-id").val('');
			$j('#summarized-value').prop('disabled', false);
			$j('#summarized-value-label').removeClass( "text-muted" );
		}).on('shown.bs.modal', function() {
			$j('#report-title').focus();
		});
		
	})
</script>

<div class="page-header row">
	<h1><img src="summary_reports-logo-lg.png" style="height: 1em;"> Summary Reports for AppGini</h1>
	<h1>
		<a href="./index.php">Projects</a> &gt; <?php echo substr($projectFile, 0, -4); ?>
		<a href="output-folder.php?axp=<?php echo $axp_md5; ?>" class="pull-right btn btn-success btn-lg col-md-3 col-xs-12">Specify output folder <span class="glyphicon glyphicon-chevron-right"></span></a>
		<div class="clearfix"></div>
	</h1>
</div>

<div class="row">
	<div class="col-md-4"> 

	<?php 
		echo $summary_reports->show_tables(array(
			'axp' => $xmlFile,
			'click_handler' => 'show_tables_reports',
			'select_first_table' => true
		))	;
		$tables = $xmlFile->table;
	?>
	</div>
	<div class="col-md-8">
		<!-- Modal -->
		<?php
			$modal_label_classes = 'col-xs-offset-1 col-xs-10 col-sm-3 col-sm-offset-1';
			$modal_input_classes = 'col-xs-offset-1 col-xs-10 col-sm-7 col-sm-offset-0';
			$separator = '<div class="row"><div class="col-xs-offset-1 col-xs-10"><hr></div></div>';
		?>
		<div id="report-modal" class="modal fade" role="dialog">
			<div class="modal-dialog">

			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal-title"></h4>
					</div>
					<div class="modal-body">
						<form id="report-values" class="form-horizontal">

							<input type="hidden" id="table-index" name="table-index" value="">
							<input type="hidden" id="report-id" name="report-id" value="">
							<input type="hidden" id="previous-reports" name="previous-reports" value="">
							<input type="hidden" id="first-caption" name="first-caption" value="">
							<input type="hidden" id="second-caption" name="second-caption" value="">
							<input type="hidden" id="look-up-table" name="look-up-table" value="">
							<input type="hidden" id="look-up-value" name="look-up-value" value="">
							<input type="hidden" id="label-field-index" name="label-field-index" value="">
							<input type="hidden" id="date-field-index" name="date-field-index" value="">
				  
							<div class="form-group">
								<label for="report-title" class="control-label <?php echo $modal_label_classes; ?>">	
									Report Title
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<input maxlength="40" type="text" class="form-control" name="report-title" id="report-title" value="" required>
									<div class="text-danger hidden validation-error" id="title-validation">Report title required</div>
								</div>
							</div>
							<div class="form-group" id="how-to-summarize-group">
								<label for="how-to-summarize" id="how-to-summarize-label" class="control-label <?php echo $modal_label_classes; ?>">
									How to summarize?
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<select class="form-control" id="how-to-summarize" name="how-to-summarize" ></select>
								</div>
							</div>
							<div class="form-group" id="summarized-value-group">
								<label for="summarized-value" id="summarized-value-label" class="control-label <?php echo $modal_label_classes; ?>">
									What field to summarize?
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<select class="form-control" id="summarized-value" name="summarized-value" ></select>
									<div class="help-block hidden" id="summarized-value-validation">
										<span class="text-danger text-bold">
											This table has no fields to summarize. You can only use count.
										</span>
									</div>
									<div class="text-danger hidden validation-error" id="what-to-summarize-required">A field must be selected</div>
								</div>
							</div>
							<?php echo $separator; ?>
							<div class="form-group">
								<div class="<?php echo $modal_label_classes;?>"></div>
								<div class="<?php echo $modal_input_classes; ?> checkbox">
									<label>
										<input type="checkbox" value="0" id="single-table">
										Group data by a field from another table
									</label>
								</div>
							</div>
							<div class="form-group">
								<label for="group-table" id="group-table-label" class="control-label <?php echo $modal_label_classes; ?>">
									Group Table
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<select class="form-control" id="group-table" name="group-table"></select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="label" class="control-label <?php echo $modal_label_classes; ?>">
									Label Field
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<select class="form-control" id="label" name="label"></select>
									<div class="text-danger hidden validation-error" id="label-validation">Label field required</div>
								</div>
							</div>
							<?php echo $separator; ?>
							<div class="form-group">
								<label for="date-field" class="control-label <?php echo $modal_label_classes; ?>">
									Date field used to filter the report
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									<select class="form-control" id="date-field" name="date-field"></select>
								</div>
							</div>
							<div class="form-group">
								<label for="group-array" class="control-label <?php echo $modal_label_classes; ?>">
									Groups that can access this report
								</label>
								<div class="<?php echo $modal_input_classes; ?>">
									 <textarea class="form-control" rows="5" id="group-array" name="group-array"></textarea>
									 <span id="helpBlock" class="help-block">Enter each group in a separate line or leave it blank for all groups</span>
								</div>
							</div>						
					
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success" id="save-report" >Save</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<button type="button" class="btn btn-success" id="add-report" data-toggle="modal" data-target="#report-modal"><i class="glyphicon glyphicon-plus"></i> Add Report</button>
		
		<div id="table-reports" class="table-reponsive vspacer-lg"></div>
	</div>
</div>


<style>
	.panel tr:first-child th, .panel tr:first-child td {
		border-top: none !important;
	}
	.panel-title{ font-weight: bold; }
</style>

<?php
include(dirname(__FILE__) . '/footer.php'); ?>
