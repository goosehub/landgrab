<script>
	let map = null;
	let world_key = <?php echo $world['id']; ?>;
	let tile_size = <?php echo $world['tile_size']; ?>;
	let account = <?php echo json_encode($account); ?>;
	let tiles = [];
	let map_update_interval_ms = <?php echo MAP_UPDATE_INTERVAL_MS; ?>;
	let account_update_interval_ms = <?php echo ACCOUNT_UPDATE_INTERVAL_MS; ?>;
	let attack_key_pressed = false;
	let keys = new Array();
	let resources = JSON.parse('<?php echo json_encode($this->resources); ?>');
</script>