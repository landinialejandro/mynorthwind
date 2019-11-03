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
<script>
	$j(function() {

		$j('#json_editor').html('');

		json_editor('json_editor', $j('.jsoninput').val());

		// add the jquery editing magic
		apply_editlets();

		$j('.jsoninput').click(function() {
			$j(this).focus();
			$j(this).select();
		});
	});
</script>
<div class="json-edit">
	<div class="callout callout-success">
		<h2>Editing Application varibles and icon group</h2>
		<br>
		<p>
			.. Delete nodes (right click to delete)<br>
			.. Add nodes (hover the lil oval below a container)
		</p>
	</div>
	<div class="box">
		<div class="box-header">
				<a class="btn btn-app">
					<i class="fa fa-repeat" onclick="load_from_box('jsonglobals'); " title="reset to last changues"></i> Reset
				</a>
				<a class="btn btn-app" onclick="save_ws('json_editor'); " title="save changues to server">
					<i class="fa fa-save"></i> Save
				</a>
		</div>
		<div class="box-body">
			<div>
				<textarea id="jsonglobals" rows="20" cols="30" class="jsoninput" hidden>
							<?php
							echo file_get_contents('config.json');
							?>
				</textarea>
			</div>
			<div >
				<div id="json_editor" data-role="myjson"></div>
			</div>
		</div>

	</div>
</div>
<!-- END visible area//-->
<div style="display:none">
	<div data-type="object"></div>
	<div data-type="array"></div>
</div>
<?php include_once("$hooks_dir/../footer.php"); ?>