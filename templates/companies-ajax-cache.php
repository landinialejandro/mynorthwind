<?php
	$rdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('nl2br', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'companies';

		/* data for selected record, or defaults if none is selected */
		var data = {
			type: <?php echo json_encode(array('id' => $rdata['type'], 'value' => $rdata['type'], 'text' => $jdata['type'])); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for type */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'type' && d.id == data.type.id)
				return { results: [ data.type ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

