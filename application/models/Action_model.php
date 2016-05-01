<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class action_model extends CI_Model
{
// Insert new action log
 function new_action_record($active_account_key, $passive_account_key, $action, $world_key, $coord_slug, $name_at_action, $details)
 {
    $data = array(
    'active_account_key' => $active_account_key,
    'passive_account_key' => $passive_account_key,
    'action' => $action,
    'world_key' => $world_key,
    'coord_slug' => $coord_slug,
    'name_at_action' => $name_at_action,
    'details' => $details
    );
    $this->db->insert('action_log', $data);
 }

}