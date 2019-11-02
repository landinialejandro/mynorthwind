<?php
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");

	include_once("{$hooks_dir}/../header.php");
	$user_data = getMemberInfo();
	$user_group = strtolower($user_data["group"]);
?>

<style>
		div[data-type="object"] {
			border: 1px solid #fcc;
			padding-bottom: 8px;
		}
		div[data-type="array"] {
			border: 1px solid #ccf;
			padding-bottom: 8px;
		}
		div[data-role="arrayitem"] > div  {
			margin: 2px;
		}
		[data-type="string"] {
			color: #4AA150;
		}
		pre {
			display: inline;
			margin: 2px;
			font-family: courier;
			white-space: pre-wrap;
		}
		[data-type="number"] {
			color: #D67B13;
		}
		[data-type="null"] {
			color: #919191;
		}
		[data-type="boolean"] {
			color: #FA6B8F;
		}
		div[data-role="value"] {
			margin-left: 20px;
		}
		div[data-role="prop"], div[data-role="arrayitem"] {
			/* border: 0px solid #ccf; */
			margin: 1px;
			padding: 1px;
			color: #4155A6;
		}
		span[data-role="key"] {
			min-width:100px;
		}
		.edit_box {
			display: inline-block;
			padding: 0px;
			margin: 0px;
		}
		.collapse_box {
			font-size: 6pt;
			color: #999;
			padding: 0px;
			margin: 0px;
			cursor: pointer;
		}
		.dimmed {
			opacity:0.4;filter:alpha(opacity=40);
			background: #999;
		}


      /* all context menus have this class */
      .context-menu {
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        
        background-color: #f2f2f2;
        border: 1px solid #999;
        
        list-style-type: none;
        margin: 0; 
        padding: 0;
      }
      .context-menu a {
        display: block;
        padding: 3px;
        text-decoration: none;
        color: #333;
      }
      .context-menu a:hover {
        background-color: #666;
        color: white;
      }
	/* Z-index of #mask must lower than #boxes .window */
	#mask {
		position:absolute;
		z-index:9000;
		background-color:#000;
		display:none;
		top: 0px;
		bottom: 0px;
		right: 0px;
		left:0px;
	}
	#boxes .window {
		position:absolute;
		width:440px;
		height:200px;
		display:none;
		z-index:9999;
		padding:20px;
	}
	#dialog a.close {
		color: #EDAF42;
		text-decoration: none;
		border: 1px solid #333;
		padding: 2px 10px;
		margin: 2px 0px;
		display: inline-block;
		border-radius: 3px;
		background: #666;
		float: right;
	}
	#dialog a.close:hover {
		background: A37931;
		color: #444;
	}
	
	#past_ws {
		height: 168px;
		overflow: auto;
	}
	/* Customize your modal window here, you can add background image too */
	#boxes #dialog {
		width:375px; 
		height:203px;
		background: #fff;
	}
	div[data-type="object"] > div.inline_add_box {
		border: 1px solid #fcc;
	}
	div[data-type="array"] > div.inline_add_box {
		border: 1px solid #ccf;
	}
	div.inline_add_box {
		margin: 5px 0px 0px -2px;
		border-radius: 3px;
		background: white;
		min-width: 20px;
		color: #AAA;
		text-align: center;
		padding: 0px;
		font-family: Verdana !important;
		font-size: 9px;
		float: right;
		position: absolute;
		border: 1px solid;
		min-height: 4px;
		max-height: 16px;
		overflow: hidden;
	}
	div.inline_add_box a {
		color: #7aa;
		font-size: 10px;
		cursor: pointer;
		text-decoration: none;
	}
	div.inline_add_box a:hover {
		color: #366;
	}
	div.add_box_content {
		display: none;
		padding: 0px;
		margin: 2px;
		padding-left: 5px;
		text-align: center;
		cursor: default;
	}
</style>

	<script>
		$j(function(){
       			
			$j('#json_editor').html('');

			json_editor('json_editor', $j('.jsoninput').val());

			// add the jquery editing magic
			apply_editlets();

			$j('.jsoninput').click(function(){
				$j(this).focus();
				$j(this).select();
			});
		});
		
		// stuff for the modal ws window
		function display_ws_modal() {
			var id = '#dialog';
			//transition effect     
			$j('#mask').fadeIn(500);    
			$j('#mask').fadeTo("slow",0.8);  
			
			//Get the window height and width
			var winH = $j(window).height();
			var winW = $j(window).width();
			       
			//Set the popup window to center
			$j(id).css('top',  winH/2-$j(id).height()/2);
			$j(id).css('left', winW/2-$j(id).width()/2);
			
			//transition effect
			$j(id).fadeIn(1000); 
		}

		// stuff for the right click menus
		function setup_menu() {
			$j('div[data-role="arrayitem"]').contextMenu('context-menu1', {
			    'remove item': {
				click: remove_item,
				klass: "menu-item-1" // a custom css class for this menu item (usable for styling)
			    },
			}, menu_options);
			$j('div[data-role="prop"]').contextMenu('context-menu2', {
			    'remove item': {
				click: remove_item,
				klass: "menu-item-1" // a custom css class for this menu item (usable for styling)
			    },
			}, menu_options);
		}
		function remove_item(element) {
		      console.log("# delete");
		      element.hide(500, function () {
			      $j(this).remove();
		      });
		}
		function create_item(element) {
		      console.log("# create");
		}
		var menu_options = {
			disable_native_context_menu: true,
			showMenu: function(element) {
				element.addClass('dimmed');
			},
			hideMenu: function(element) {
				element.removeClass('dimmed');
			},
		};


		// functions used for the web service
		function save_ws(input) {
			$json = glean_json(input, 0);   
			$j.post("jsonsave.php", { json: $json},
				function(data) {
					//alert("You can retrieve your json as a web service at this url: http://json.bubblemix.net/ws/" + data);
					var url = 'http://json.bubblemix.net/ws/' + data;
					$j('#past_ws hr').remove();
					var new_row = $j('<div>').html('<a href="' + url + '" target="_blank">' + url + '</a>').append('<hr />');
					$j('#past_ws').prepend(new_row);
					display_ws_modal();
				});
		}
		var easy_save_value = function(value, settings) { 
			$j(this).text(value);
		}
		var save_value = function(value, settings) { 
			console.log(this); console.log(value); // console.log(settings);

			if ($j(this).data('role') == 'value') {
				if (value == "null") {
					$j(this).attr("data-type", "null");
					$j(this).data('type','null');
					$j(this).text(value);
					$j(this).unbind('click');
				} else if (value == "true" || value == "false") {
					$j(this).attr("data-type", "boolean");
					$j(this).data('type','boolean');
					$j(this).text(value);
					$j(this).unbind();
					$j(this).editable(save_value,{ cssclass : 'edit_box', data : "{'true':'true','false':'false'}", type : 'select', onblur : 'submit' });
				} else {
					var num = parseFloat(value);
					console.log(num);
					if (isNaN(num)) {
						$j(this).attr("data-type", "string");
						$j(this).data('type','string');
						$j(this).text(value);
						$j(this).unbind();
						$j(this).editable(save_value, { cssclass : 'edit_box'});
					} else {
						$j(this).attr("data-type", "number");
						$j(this).data('type','number');
						$j(this).text(num);
						$j(this).unbind();
						$j(this).editable(save_value, { cssclass : 'edit_box'});
					}
				}
			} else {
				$j(this).text(value);
			}
		};
		// copy the workspace back into the textarea
		function extract_json(divid, indent){
			$j('.jsoninput').val(glean_json(divid,indent));
		}
		// convert the work area to a json string
		function glean_json(divid, indent)  {
			var base = $j('#' + divid);
			var rootnode = base.children('div[data-role="value"]:first');
			var jsObject = parse_node(rootnode);
			var json = JSON.stringify(jsObject, null, indent);
			return json;
		}
		// convert the work area to a js object
		function parse_node(node) {
			var type = node.data('type');
			if (type == 'object') {
				var newNode = new Object();
				var props = node.children('div[data-role="prop"]');
				props.each(function(index) {
					newNode[$j(this).children('[data-role="key"]').html()] = parse_node($j(this).children('[data-role="value"]'));
				});
				return newNode;
			} else if (type == 'array') {
				var newNode = new Array();
				var values = node.children('[data-role="arrayitem"]');
				values.each(function(index) {
					var value_node = $j(this).children('[data-role="value"]');
					newNode.push(parse_node(value_node));
				});
				return newNode;
			} else if (type == 'string') {
				return node.html();
			} else if (type == 'number') {
				var parsedNum = parseFloat(node.html());
				if(isNaN(parsedNum)) return 0;
				return parsedNum;
			} else if (type == 'boolean') {
				return (node.html() == "true") ;
			} else if (type == null || type == 'null' ) {
				return null;
			} else {
				return "(Unknown Type:" + type + " )";
			}
		}
		function remove_editlets() {
			$j("span.collapse_box").remove();
			$j("div.inline_add_box").remove();
			$j(".context-menu").remove();

		}
		function apply_editlets() {
			remove_editlets();
			// add collapse boxes for the arrays and objects
			var o_collapse_box = $j('<span class="collapse_box"><span>[-]</span><span style="display: none">[+] {...}</span></span>');
			var a_collapse_box = $j('<span class="collapse_box"><span>[-]</span><span style="display: none" data-role="counter">[+] []</span></span>');
			$j('div[data-type="object"]').before(o_collapse_box );
			$j('div[data-type="array"]').before(a_collapse_box );

			$j('.collapse_box').click(function(){
				var next = $j(this).next();
				next.toggle();
				$j(this).find('span').toggle();
				if ( next.data('type') == 'array' ) {
					$j(this).find('span[data-role="counter"]').html('[+] ['+ next.children('[data-role="arrayitem"]').length +']' );
				}
				event.stopPropagation();
			});
			
			// add the "new" buttons
			var add_more_box = $j('<div class="inline_add_box"><div class="add_box_content">add: <a data-task="add_value" href="#">text</a> | <a data-task="add_array" href="#">array</a> | <a data-task="add_object" href="#">object</a></div></div>');
			$j('div[data-type="object"]').append(add_more_box);
			$j('div[data-type="array"]').append(add_more_box);
			
			$j('div.inline_add_box a').click(function(e){
				var target = $j(e.target);
				var task = target.data('task');
				var add_box = target.parents(".inline_add_box");
				var collection = add_box.parent();				
				var type = collection.data('type');

				// TODO this code is a partial duplicate of code in make_node fix it!
				if (type == 'object') {
					var newObj = $j('<div data-role="prop"></div>').append( $j('<span data-role="key">').append("key")).append(': ');
				} else {
					var newObj = $j('<div data-role="arrayitem"></div>');
				}
				
				if (task == 'add_object') {
					var json = '{"id":"1"}';
					newObj.append(make_node(JSON.parse(json)));
				} else if (task == 'add_array') {
					var json = '["item1"]';
					newObj.append(make_node(JSON.parse(json)));
				} else {
					newObj.append($j('<pre data-role="value" data-type="string">').html("value"));
				}
				newObj.hide();
				add_box.before(newObj);
				newObj.show(500);
				apply_editlets();
				return false;
			});
			
			$j(".inline_add_box").hover(
				function () {
					$j(this).children().show(100);
				},
				function () {
					$j(this).children().hide(200);
				}
			);

			// make the fields editable in place
			$j('span[data-role="key"]').editable(easy_save_value,{ cssclass : 'edit_box'});
			$j('[data-type="string"]').editable(save_value, { cssclass : 'edit_box'});
			$j('[data-type="number"]').editable(save_value, { cssclass : 'edit_box'});
			$j('[data-type="null"]').editable(save_value, { cssclass : 'edit_box'});
			$j('[data-type="boolean"]').editable(save_value,{ cssclass : 'edit_box', data : "{'true':'true','false':'false'}", type : 'select', onblur : 'submit' });
			
			// make the right click menus
			setup_menu();

		}
		// parse the text area into the the workarea, setup the event handlers
		function load_from_box(jsonfile) {

			$j('#json_editor').html('');
			json_editor('json_editor', $j('#'+jsonfile).val());

			// add the jquery editing magic
			apply_editlets();
		}
		// convert a string into nodes
		function json_editor(divid, json_string){
			try {
			    var json = JSON.parse(json_string);
			} catch (err) {
			    var json = JSON.parse('{"error": "parse failed"}');
			}
			var base = $j('#' + divid);
			base.append(make_node(json));
		}
		// recursively make html nodes out of the json
		function make_node(node_in) {
			console.log(" ====> " + JSON.stringify(node_in));
			var type = Object.prototype.toString.apply(node_in);
			console.log("  - " + type);

			if (type === "[object Object]") {
				// TODO create the div for an object here
				var container = $j('<div data-role="value" data-type="object"></div>');
				for(var prop in node_in) {
					if(node_in.hasOwnProperty(prop)) {
                        var row = $j('<div data-role="prop">  </div>')
                                    .append( $j('<span data-role="key">')
                                        .append(prop))
                                    .append(': ')
                                    .append(make_node(node_in[prop]));
						container.append(row);
					}
				}
				return container;
			} else if (type === "[object Array]") {
				var container = $j('<div data-role="value" data-type="array"></div>');
				for (var i = 0, j = node_in.length; i < j; i++) {
					var row = $j('<div data-role="arrayitem"></div>').append(make_node(node_in[i]));
					container.append(row);
				}
				return container;
			} else if (type === "[object String]") {
				return $j('<pre data-role="value" data-type="string">').html(node_in);
			} else if (type === "[object Number]") {
				return $j('<pre data-role="value" data-type="number">').html(node_in);
			} else if (type === "[object global]" || type === "[object Null]") {
				return $j('<pre data-role="value" data-type="null">').html('null');
			} else if (type === "[object Boolean]") {
				return $j('<pre data-role="value" data-type="boolean">').html(node_in.toString());
			}
		}
	</script>
	<div class="row">
        <div class="row">
			<div	>
				a handy online json editor v0.1b
				<br />.. now with mock web service publishing (click [ws] in the tool bar)
				<br />.. now deleting nodes works (right click to delete)
				<br />.. now adding nodes works (hover the lil oval below a container)
				<h1>JSON Tinker</h1>
			</div	>
        </div>
        <div class="row">
            <div class=" col-lg-3">
                <div class="row">
                    <div>Globals:</div> 
                    <textarea id="jsonglobals" rows="20" cols="30" class="jsoninput col-lg-7" hidden>
						<?php 
						echo file_get_contents('config.json');
						?>
					</textarea>
                    <div class="col col-lg-4">tools:
                        <a href="#" class = "btn btn-primary" onclick="load_from_box('jsonglobals'); " title="import into workspace"><i class="fa fa-recycle"></i></a><br/>
                        <a href="#" class = "btn btn-default" onclick="save_ws('json_editor'); " title="save as a mock web service">ws</a>
                    </div>
                </div>
            </div>
            <div class="editarea col-lg-8">
                workspace:
                <div id="json_editor" data-role="myjson"></div>
            </div>
        
        </div>
    </div>
    <!-- END visible area//-->
	<div style="display:none"><div data-type="object"></div><div data-type="array"></div></div>
	<div id="boxes">
		<div id="dialog" class="window">
			<b>Your JSON can be fetched at this url:</b>
			<div id="past_ws">
			</div>
			<a href="#" class="close" onclick="javascript: $j('#mask, .window').fadeOut(500); ">Close</a>
		</div>
		<div id="mask"></div>
	</div>
	<?php include_once("$hooks_dir/../footer.php"); ?>