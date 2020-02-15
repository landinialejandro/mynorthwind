<?php
	include(dirname(__FILE__) . '/header.php');

	// validate project name
	if (!isset($_REQUEST['axp']) || !preg_match('/^[a-f0-9]{32}$/i', $_REQUEST['axp'])){
		echo '<br>' . $tve_class->error_message('Project file not found.');
		exit;
	}
	
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $tve_class->get_xml_file($axp_md5, $projectFile);
//-----------------------------------------------------------------------------------------
?>

<script>
	var project = <?php echo json_encode($xmlFile); ?>;
	var axp_md5 = <?php echo json_encode($axp_md5); ?>; 
	
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
	
	$j(function(){
		var table = $j('#group-table').val();
		
		/* Triggring Add and Edit Modal Events */
	 	$j('#group-table').on('change',function(){
	
			var table=$j('#group-table').val();
			var selected_table_name=project["table"][selected_table]["name"];
			$j('#label').empty();
			fill_label_field(table);
		}); 
				
		$j('#report-title').keyup(function(){
			if($j(this).val() != '') $j('#title-validation').addClass('hidden');
		});
		
		$j('#label').change(function(){
			if($j(this).val() != '') $j('#label-validation').addClass('hidden');
		});
	})
</script>

<div class="page-header row">
	<h1><img src="tvedit.mid.png" style="height: 1em;"> Landini TV edit Enable for AppGini</h1>
	<h1>
		<a href="./index.php">Projects</a> &gt; <?php echo substr($projectFile, 0, -4); ?>
		<a href="output-folder.php?axp=<?php echo $axp_md5; ?>" class="pull-right btn btn-success btn-lg col-md-3 col-xs-12"><span class="glyphicon glyphicon-play"></span>  Enable TV edit</a>
		<div class="clearfix"></div>
	</h1>
</div>

<div class="row">
		<?php 
		echo $tve_class->show_tables(array(
			'axp' => $xmlFile,
			'click_handler' => 'showFields',
			'classes' => 'col-md-3 col-xs-12'
		)); 
		?>
		<div  class="col-md-3 col-xs-12">
			<h4><b>Available fields/options</b></h4>
			<div id="fields" class="list-group">
			</div>
		</div>
	<div class="col-md-6 col-xs-12">
		<h4><b>Fields to enable ( drag it )</b></h4>
		<div id="choosenFields" class="list-group" >
		</div>
	</div>

</div>


<style>
	.panel tr:first-child th, .panel tr:first-child td {
		border-top: none !important;
	}
	.panel-title{ font-weight: bold; }
</style>


<script>	

	$j( document ).ready( function(){

		// sort divs by id in $fields section
		$j.fn.sortDivs = function sortDivs() {
		    $j("> div", this[0]).sort(custom_sort).appendTo(this[0]);
		    function custom_sort(a, b){ return (parseInt($j(b).data("sort")) < parseInt($j(a).data("sort"))) ? 1 : -1; }
		}

		//add resize event
		$j(window).resize(function() {
  			$j("#tables-list").height( $j(window).height() - $j("#tables-list").offset().top -  $j("#bottom-links").height() - 70);
			$j("#choosenFields, #fields").height( $j("#tables-list").height() - $j("h4").first().height() - 20 );		
		});
		
		$j(window).resize();

	    $j( "#choosenFields" ).sortable({
	        connectWith: "#fields",
	        cursor: "move",
			stop: function (event, ui) {
	        	updateList()
			},
			receive: function (event, ui) {
	        	updateList()
			},
			remove: function (event, ui) {
	        	updateList()
			}
	    }).disableSelection();


	    $j( "#fields" ).sortable({
			cursor: "move",
			//stop ordering the fields
			beforeStop: function (event, ui) {
				if($j(ui.helper).parent().attr('id') === 'fields' && $j(ui.placeholder).parent().attr('id') === 'fields'){
				   return false; 
				}
			},
			tolerance: 'pointer',
			receive: function (event, ui) {
				$j("#fields").sortDivs();
			},
			connectWith: "#choosenFields",
	    }).disableSelection();


	});

	function updateList(){
			var ids='';
        	var tableNumber = $j("#choosenFields").data('table');

        	//update array 
        	$j("#choosenFields").find("div").each(function() {
   				 ids+=( $j(this).attr("data-sort") )+":";
			});

        	//one/many tables in project
			currentTable = ( (typeof tableNumber != 'undefined')?xmlFile.table[tableNumber]:xmlFile.table);
			if (! currentTable['plugins'] ){
				currentTable['plugins']=[];
			}
			if (! currentTable['plugins']['spm'] ){
				currentTable['plugins']['spm'] =[];
			}
			currentTable['plugins']['spm']['spm_fields'] =  ids;

			//update project file
			$j.ajax({
			  type: "POST",
			  url: "project-ajax.php",
			  data: {
			  	projFile: "<?php echo $projectFile; ?>",
			  	tableNumber: (tableNumber?tableNumber:0),
			  	data: (ids.length==0?":":ids)
			  },
			  success: function(response){
			  },
			});
	}

	var xmlFile = <?php echo json_encode($xmlFile); ?>;
	
	//sava fields' data types
	var tableData = [];

	function showFields(tableNum){
		$j("#fields, #choosenFields").html('');
		var field, type={} ,currentType,table;
		

		//check number of tables
		if ($j.isArray(xmlFile.table)){      				//>1 table
			table = xmlFile.table[tableNum];
			$j("#fields, #choosenFields").data('table',tableNum );
		}else{     											//1 table only
			table = xmlFile.table;
		}
		var chosenElements;
		if ( table.plugins && table.plugins.spm && table.plugins.spm.spm_fields ){
			chosenElements = new Array(table.plugins.spm.spm_fields.split(":").length);
		}

		//get data types ( only for the first time the table is clicked )
		if (!tableData[tableNum]){
			tableData[tableNum] = {};
			for (var i = 0 ; i< table.field.length ; i++){
				field = table.field[i];

				//checks if the field is filtered, not auto-filled, not youtube/googlemap(embed is empty), not img/any file (allowImageUpload)
				if ( (field.notFiltered == "False") && (field.autoFill=="False")  && ($j.isEmptyObject(field.embed))  && (field.allowImageUpload=="False")){
					currentType = parseInt (field.dataType);
					node = getType( currentType , field);
					tableData[tableNum][String(i)]=node;
				}
			}
		}

		//display data
		
		//convert ids string into array
		var spmDataArray = [];

		if( table.plugins && table.plugins.spm && table.plugins.spm.spm_fields ){
			var spmDataArray = table.plugins.spm.spm_fields.split(":");	
		}

		$j.each(tableData[tableNum], function( key, value ) {
			position = $j.inArray( key , spmDataArray );
			if ( position!== -1){
			  	chosenElements[position] = '<div class="list-group-item ui-state-default  item" data-sort='+key+'><span class="'+value.icon+'" ></span>     ' +value.caption +" ( "+value.name+" ) </div>";
			}else{
				$j("#fields").append('<div class="list-group-item ui-state-default  item" data-sort='+key+'><span class="'+value.icon+'" ></span>     ' +value.caption +" ( "+value.name+" ) </div>");	
			}
		});

		//fixed sections part
		i=9001;   //ORDER BY
		position = $j.inArray( String(i) , spmDataArray );
		if ( position !== -1){
			chosenElements[position] = '<div class="list-group-item ui-state-default  item" data-sort='+i+'><span class="glyphicon glyphicon-collapse-down" ></span>     Order by  ( section ) </div>';
		}else{
			$j("#fields").append('<div class="list-group-item ui-state-default  item" data-sort='+i+'><span class="glyphicon glyphicon-collapse-down" ></span>     Order by  ( section ) </div>');	
		}	
		i++;  //USER/GROUP/ALL
		position = $j.inArray( String(i) , spmDataArray );
		if ( position !== -1){
			chosenElements[position] = '<div class="list-group-item ui-state-default  item" data-sort='+i+'><span class="glyphicon glyphicon-user" ></span>     User/group/all  ( section ) </div>';
		}else{
			 $j("#fields").append('<div class="list-group-item ui-state-default  item" data-sort='+i+'><span class="glyphicon glyphicon-user" ></span>     User/group/all  ( section ) </div>');	
		}

		if ( chosenElements){
			$j("#choosenFields").html(chosenElements.join(" "));
		}
	}

	function getType( currentType , field ){
		var nodeData={};
		

		//lookup
		if (!  $j.isEmptyObject(field.parentTable) ){
			nodeData.name="drop down";
			nodeData.icon = "glyphicon glyphicon-align-justify";

		//options list
		}else if (!  $j.isEmptyObject(field.CSValueList)){
			nodeData.name="radio buttons / drop down";
			nodeData.icon = "glyphicon glyphicon-align-justify";
		
		//checkbox regardless the type
		}else if( field.checkBox == "True"){
			nodeData.name= "checkbox";
			nodeData.icon = "glyphicon glyphicon-check";
			
		}else if (currentType <9 ){  									//number
			nodeData.name= "number range";
			nodeData.icon = "glyphicon glyphicon-resize-horizontal";

		}else if (currentType == 9 || currentType == 13 ){		//date
			nodeData.name= "date range";
			nodeData.icon = "glyphicon glyphicon-calendar";

		}else if (currentType < 12 ){							//dateTime
			nodeData.name= "date/time range";
			nodeData.icon = "glyphicon glyphicon-calendar";

		}else if (currentType == 12 ){  						//time
			nodeData.name= "time range";
			nodeData.icon = "glyphicon glyphicon-time";

		}else{
			nodeData.name="text";
			nodeData.icon="glyphicon glyphicon-text-size";
		}

		
		nodeData.caption = field.caption;

		return nodeData;
	}


</script>

<?php
include(dirname(__FILE__) . '/footer.php'); ?>
