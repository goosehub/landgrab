<script>
	let map = null;
	let world_key = <?php echo $world['id']; ?>;
	let tile_size = <?php echo $world['tile_size']; ?>;
	let account = <?php echo json_encode($account); ?>;
	let tiles = [];
	let map_update_interval_ms = <?php echo MAP_UPDATE_INTERVAL_MS; ?>;
</script>