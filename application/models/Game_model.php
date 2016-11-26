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
 // Get all lands recently updated
 function get_all_lands_in_world_recently_updated($world_key, $update_timespan)
 {
    $this->db->select('*');
    $this->db->from('land');
    $this->db->where('world_key', $world_key);
    $this->db->where('modified >', date('Y-m-d H:i:s', time() - $update_timespan));
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
 function update_land_data($land_id, $account_key, $land_name, $content, $land_type, $color)
 {
    $data = array(
        'account_key' => $account_key,
        'land_name' => $land_name,
        'content' => $content,
        'land_type' => $land_type,
        'color' => $color,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('id', $land_id);
    $this->db->update('land', $data);
    return true;
 }
 // Update land capitol status
 function update_land_capitol_status($land_key, $capitol)
 {
    $data = array(
        'capitol' => $capitol,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('id', $land_key);
    $this->db->update('land', $data);
    return true;
 }
 // Remove capitol from account
 function remove_capitol_from_account($account_key)
 {
    // Find capitol effect
    $this->db->select('id');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $this->db->where('capitol', 1);
    $query = $this->db->get();
    $capitol_land = $query->result_array();
    if ( isset($capitol_land[0]) ) {
        // Remove capitol effect
        $capitol_key = 10;
        $this->db->where('land_key', $capitol_land[0]['id']);
        $this->db->where('modify_effect_key', $capitol_key);
        $this->db->delete('land_modifier');
    }

    // Remove capitol flag
    $data = array(
        'capitol' => 0,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('account_key', $account_key);
    $this->db->update('land', $data);
    return true;
 }
 // Check if any immediate squares belong to current account
 function land_range_check($world_key, $account_key, $coord_array)
 {
    $this->db->select('id');
    $this->db->from('land');
    $this->db->where_in('coord_slug', $coord_array);
    $this->db->where('account_key', $account_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Get accounts in world
 function get_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $query = $this->db->get();
   return $query->result_array();
 }
 // Get active accounts in world
 function get_active_accounts_in_world($world_key)
 {
   $this->db->select('*');
   $this->db->from('account');
   $this->db->where('world_key', $world_key);
   $this->db->where('active_account', 1);
   $query = $this->db->get();
   return $query->result_array();
 }
 // Upgrade land type
 function upgrade_land_type($land_id, $land_type)
 {
    $data = array(
        'land_type' => $land_type,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('id', $land_id);
    $this->db->update('land', $data);
    return true;
 }
 // Mark account as active
 function update_account_active_state($account_id, $active_state)
 {
    // Update account
    $data = array(
        'active_account' => $active_state
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
 }
 // Get all lands of type by account
 function count_lands_of_type_by_account($account_key)
 {
   $this->db->select('count(land_type) as count');
   $this->db->select('land_type');
   $this->db->from('land');
   $this->db->where('account_key', $account_key);
   $this->db->group_by('land_type');
   $this->db->order_by('land_type', 'ASC');
   $query = $this->db->get();
   return $query->result_array();
}





 // Get all modify effects
 function get_effects_of_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT  *
        FROM modify_effect
        LEFT JOIN land_modifier
        ON modify_effect.id = land_modifier.modify_effect_key
        WHERE land_modifier.land_key = " . $land_key . ";
    ");
    $result = $query->result_array();
    return $result;
 }
 function get_sum_effects_of_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT 
        SUM(modify_effect.population) as population,
        SUM(modify_effect.gdp) as gdp,
        SUM(modify_effect.treasury) as treasury,
        SUM(modify_effect.military) as military,
        SUM(modify_effect.support) as support 
        FROM modify_effect 
            LEFT JOIN land_modifier 
            ON modify_effect.id = land_modifier.modify_effect_key 
        WHERE land_modifier.land_key = " . $land_key . ";
    ");
    $result = $query->result_array();
    if ( isset($result[0]) ) {
        return $result[0];
    }
    return [];
 }
 // Get count of modifiers
 function get_sum_modifiers_for_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT 
        modify_effect.id,
        modify_effect.name,
        COUNT(modify_effect.id) AS count
        FROM modify_effect 
            LEFT JOIN land_modifier 
            ON modify_effect.id = land_modifier.modify_effect_key 
        WHERE land_modifier.land_key = " . $land_key . "
        GROUP BY modify_effect.id;
    ");
    $result = $query->result_array();
    if ( isset($result[0]) ) {
        return $result;
    }
    return [];
 }
 // Get sum of effect values
 function get_sum_effects_for_account($account_key)
 {
    $account_key = mysqli_real_escape_string(get_mysqli(), $account_key);
    $query = $this->db->query("
        SELECT 
        SUM(modify_effect.population) as population,
        SUM(modify_effect.gdp) as gdp,
        SUM(modify_effect.treasury) as treasury,
        SUM(modify_effect.military) as military,
        SUM(modify_effect.support) as support 
        FROM modify_effect 
            LEFT JOIN land_modifier 
            ON modify_effect.id = land_modifier.modify_effect_key 
        WHERE land_modifier.land_key IN 
        (
            SELECT id
            FROM land
            WHERE account_key = " . $account_key . "
        )
    ");
    $result = $query->result_array();
    if ( isset($result[0]) ) {
        return $result[0];
    }
    return [];
 }
 // Get all modify of land
 function get_all_modify_effects()
 {
    $this->db->select('*');
    $this->db->from('modify_effect');
    $this->db->order_by('sort_order', 'asc');
    $this->db->where('sort_order !=', 0);
    $query = $this->db->get();
    return $query->result_array();
 }
 // Add modifier to land
 function add_modifier_to_land($land_key, $modify_effect_key)
 {
    $data = array(
    'land_key' => $land_key,
    'modify_effect_key' => $modify_effect_key
    );
    $this->db->insert('land_modifier', $data);
    return true;
 }
 // Remove all modifiers from land
 function remove_modifiers_from_land($land_key)
 {
    $this->db->where('land_key', $land_key);
    $this->db->delete('land_modifier');
 }
 // Remove all modifiers from land
 function remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys)
 {
    $this->db->where('land_key', $land_key);
    $this->db->where_in('modify_effect_key', $land_type_effect_keys);
    $this->db->delete('land_modifier');
 }

 function add_war_weariness_to_account($account_id, $war_weariness)
 {
    $this->db->where('id', $account_id);
    $this->db->set('war_weariness', 'war_weariness+' . $war_weariness, FALSE);
    $this->db->update('account');
 }

 function subtract_war_weariness_from_account($account_id, $war_weariness)
 {
    $this->db->where('id', $account_id);
    $this->db->set('war_weariness', 'war_weariness-' . $war_weariness, FALSE);
    $this->db->update('account');
 }
 function universal_decrease_war_weariness($war_weariness_decrease)
 {
    $this->db->where('war_weariness >', 0);
    $this->db->set('war_weariness', 'war_weariness-' . $war_weariness_decrease, FALSE);
    $this->db->update('account');
 }
 function truncate_modifiers()
 {
    $this->db->where('land_key >', 0);
    $this->db->delete('land_modifier');
 }

}

function get_mysqli() { 
    $db = (array)get_instance()->db;
    return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);
}