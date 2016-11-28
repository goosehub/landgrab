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
  function new_chat($user_key, $username, $color, $message, $world_key)
  {
    $data = array(
    'user_key' => $user_key,
    'username' => $username,
    'color' => $color,
    'message' => $message,
    'world_key' => $world_key
    );
    $this->db->insert('chat', $data);
  }
  // chat spam check
  function recent_chats($user_key, $chat_limit_length)
  {
    $query = $this->db->query("
        SELECT COUNT(id) as recent_chats
        FROM `chat`
        WHERE `user_key` = '" . $user_key . "'
        AND `timestamp` > (now() - INTERVAL " . $chat_limit_length . " SECOND);
    ");
    $result = $query->result_array();
    return isset($result[0]['recent_chats']) ? $result[0]['recent_chats'] : false;
  }
}
?>