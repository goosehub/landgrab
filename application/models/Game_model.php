<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Use this where needed for debugging
// echo '<br>' . $this->db->last_query() . '<br>';

Class game_model extends CI_Model
{
 // Get world by id
 function get_world($world_id)
 {
    $this->db->select('*');
    $this->db->from('world');
    $this->db->where('id', $world_id);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get world by slug
 function get_world_by_slug_or_id($slug)
 {
    $this->db->select('*');
    $this->db->from('world');
    $this->db->where('slug', $slug);
    $this->db->or_where('id', $slug);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get all lands
 function get_all_lands_in_world($world_key)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get single land
 function get_single_land($world_key, $coord_slug)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Update land data
 function update_land_data($world_key, $claimed, $coord_slug, $account_key, $land_name, $price, $content, $lease_price, $lease_duration, $primary_color)
 {
    $data = array(
        'claimed' => $claimed,
        'account_key' => $account_key,
        'land_name' => $land_name,
        'price' => $price,
        'content' => $content,
        'default_content' => $content,
        'lease_price' => $lease_price,
        'lease_duration' => $lease_duration,
        'primary_color' => $primary_color
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Update land content
 function update_land_content($world_key, $coord_slug, $content, $last_lease_end)
 {
    $data = array(
        'content' => $content,
        'last_lease_end' => $last_lease_end
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;    
 }
 // Update default land content
 function update_land_default_content($world_key, $coord_slug, $default_content)
 {
    $data = array(
        'default_content' => $default_content
    );
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;    
 }
 // Update cash in account
 function update_account_cash_by_account_id($account_id, $cash)
 {
    // Seller add cash
    $data = array(
        'cash' => $cash
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
 }
 // Get projected tax
 function get_sum_and_count_of_account_land($account_id)
 {
    $this->db->select('SUM(price) as sum, COUNT(*) as count');
    $this->db->from('land');
    $this->db->where('account_key', $account_id);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : 0;
 }
 // New Auction
 function new_auction($coord_slug, $world_key, $seller_account_key)
 {
    $data = array(
    'coord_slug' => $coord_slug,
    'world_key' => $world_key,
    'complete' => 0,
    'current_bid' => 500,
    'seller_account_key' => $seller_account_key,
    'current_bid_account_key' => $seller_account_key,
    'last_bid_timestamp' => date('Y-m-d H:i:s', time())
    );
    $this->db->insert('auction', $data);
 }
 // Get active auctions
 function get_active_auctions($world_key)
 {
    $this->db->select('*');
    $this->db->from('auction');
    $this->db->where('world_key', $world_key);
    $this->db->where('complete', 0);
    $this->db->limit(8);
    $this->db->order_by('id', 'desc');
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result : [];
 }
 // Get active auctions
 function check_if_land_is_active_auction($coord_slug, $world_key)
 {
    $this->db->select('*');
    $this->db->from('auction');
    $this->db->where('coord_slug', $coord_slug);
    $this->db->where('world_key', $world_key);
    $this->db->where('complete', 0);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result : false;
 }
 // Get auction info
 function get_auction_info($auction_id)
 {
    $this->db->select('*');
    $this->db->from('auction');
    $this->db->where('id', $auction_id);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : [];
 }
 // Apply new auction bid
 function apply_new_auction_bid($auction_id, $new_bid, $current_bid_account_key)
 {
    // Seller add cash
    $data = array(
        'current_bid' => $new_bid,
        'current_bid_account_key' => $current_bid_account_key,
        'last_bid_timestamp' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('id', $auction_id);
    $this->db->update('auction', $data);
    return true;
}
 // Apply new auction bid
 function set_auction_as_complete($auction_id)
 {
    // Seller add cash
    $data = array(
        'complete' => 1
    );
    $this->db->where('id', $auction_id);
    $this->db->update('auction', $data);
    return true;
}

}
?>