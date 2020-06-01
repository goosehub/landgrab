<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class world_model extends CI_Model
{
	function create_new_world($account_key, $slug, $is_private)
	{
		$data = array(
			'slug' => $slug,
			'is_private' => $is_private,
			'creator_account_key' => $account_key,
			'tile_size' => TILE_SIZE,
		);
		$this->db->insert('world', $data);
		return $this->db->insert_id();
	}
}
?>