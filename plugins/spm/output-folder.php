<?php include(dirname(__FILE__) . '/header.php'); ?>

<?php
	$axp_md5 = $_REQUEST['axp'];
	$projectFile = '';
	$xmlFile = $spm->get_xml_file($axp_md5 , $projectFile);
?>

<div class="page-header row">
	<h1><img src="spm-logo-lg.png" style="height: 1em;"> Search Page Maker for AppGini</h1>
	<h1>
		<a href="index.php">Projects</a> &gt; 
		<a href="project.php?axp=<?php echo urlencode($axp_md5); ?>"><?php echo substr($projectFile, 0, -4); ?></a> &gt;
		Output folder
	</h1>
</div>

<?php
	echo $spm->show_select_output_folder(array(
		'next_page' => 'generate.php?axp=' . urlencode($_REQUEST['axp']),
		'extra_options' => array(
			'dont_write_to_hooks' => 'Only show me the hooks code without actually writing it to existing hook files.'
		)
	));
?>

<?php include(dirname(__FILE__) . "/footer.php"); ?>