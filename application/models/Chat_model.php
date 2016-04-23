<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
    // echo '<br>' . $this->db->last_query() . '<br>';

Class chat_model extends CI_Model
{
  // Load chat
  function load_chat_by_limit($world_key, $limit)
  {
    $this->db->select('*');
    $this->db->from('chat');
    $this->db->where('world_key', $world_key);
    $this->db->limit($limit);
    $this->db->order_by('id', 'desc');
    $query = $this->db->get();
    $result = $query->result_array();
    return $result;
  }
  // Insert new chat
  function new_chat($user_key, $username, $message, $world_key)
  {
    $data = array(
    'user_key' => $user_key,
    'username' => $username,
    'message' => $message,
    'world_key' => $world_key
    );
    $this->db->insert('chat', $data);
  }
}
?>