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
 // Update account budget
 function update_account_budget($account_id, $government, $tax_rate, $military_budget, $entitlements_budget)
 {
    // Update account
    $data = array(
    'government' => $government,
    'tax_rate' => $tax_rate,
    'military_budget' => $military_budget,
    'entitlements_budget' => $entitlements_budget
    );
    $this->db->where('id', $account_id);
    $this->db->update('account', $data);
    return true;
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
 function remove_capitol_from_account($account_key, $capitol_key, $embassy_key, $sanctions_key)
 {
    // Find capitol square
    $this->db->select('id');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $this->db->where('capitol', 1);
    $query = $this->db->get();
    $capitol_land = $query->result_array();

    if ( isset($capitol_land[0]) ) {
        // Remove capitol effect
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
 // Update embassey and sanctions effects
 function update_embassey_and_sanction_effects_to_new_land($land_key, $account_key, $embassy_key, $sanctions_key)
 {
    // Find current capitol square
    $this->db->select('id');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $this->db->where('capitol', 1);
    $query = $this->db->get();
    $capitol_land = $query->result_array();
    $old_land_key = $capitol_land[0]['id'];

    // This data will be used for all 4 updates
    $data = array(
        'land_key' => $land_key,
    );

    // Update embassy effect to new land
    $this->db->where('land_key', $old_land_key);
    $this->db->where('modify_effect_key', $embassy_key);    
    $this->db->update('land_modifier', $data);

    // Update sanctions effect to new land
    $this->db->where('land_key', $old_land_key);
    $this->db->where('modify_effect_key', $sanctions_key);
    $this->db->update('land_modifier', $data);

    // Update embassy listing
    $this->db->where('land_key', $old_land_key);
    $this->db->update('embassy', $data);

    // Update sanctions listing
    $this->db->where('land_key', $old_land_key);
    $this->db->update('sanctions', $data);

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
 // Count lands of account
 function count_lands_of_account($account_key)
 {
   $this->db->select('count(land_type) as count');
   $this->db->from('land');
   $this->db->where('account_key', $account_key);
   $query = $this->db->get();
   $result = $query->result_array();
   return $result[0];
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
   $result = $query->result_array();
   return $result;
}

 // Update all lands
 function update_all_lands_in_world($world_key, $account_key, $land_name, $content, $land_type, $color, $capitol)
 {
    $data = array(
        'account_key' => $account_key,
        'land_name' => $land_name,
        'content' => $content,
        'land_type' => $land_type,
        'color' => $color,
        'capitol' => $capitol,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('world_key', $world_key);
    $this->db->update('land', $data);
    return true;
 }
 // Update world with last winner
 function update_world_with_last_winner($world_id, $last_winner_account_key, $last_winner_land_count)
 {
    $data = array(
        'last_winner_account_key' => $last_winner_account_key,
        'last_winner_land_count' => $last_winner_land_count,
        'modified' => date('Y-m-d H:i:s', time())
    );
    $this->db->where('id', $world_id);
    $this->db->update('world', $data);
    return true;
 }
 function world_set_weariness($world_key, $weariness_decrease)
 {
    $this->db->set('weariness', $weariness_decrease);
    $this->db->where('world_key', $world_key);
    $this->db->update('account');
 }
 // Get count of land by account
 function get_count_of_account_land($account_key)
 {
    $this->db->select('COUNT(*) as count');
    $this->db->from('land');
    $this->db->where('account_key', $account_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]['count']) ? $result[0]['count'] : 0;
 }
 // Get all modify effects
 function get_sum_effects_of_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT 
        SUM(modify_effect.population) as population,
        SUM(modify_effect.culture) as culture,
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
        SUM(modify_effect.culture) as culture,
        SUM(modify_effect.gdp) as gdp,
        SUM(modify_effect.treasury) as treasury,
        SUM(modify_effect.military) as military,
        SUM(modify_effect.support) as support, 
        (
            SELECT COUNT(*) as land_count
            FROM land
            WHERE account_key = " . $account_key . "
        ) as land_count
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
 // Get embassys of land
 function get_embassys_of_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT e.`account_key`, a.`nation_name`, u.`username`
        FROM `embassy` as e
            LEFT JOIN
                `account` as a on account_key = a.`id`
                LEFT JOIN
                    `user` as u on user_key = u.`id`
        WHERE e.`land_key` = '" . $land_key . "';");
    $result = $query->result_array();
    return $result;
 }
 // Get sanctions of land
 function get_sanctions_of_land($land_key)
 {
    $land_key = mysqli_real_escape_string(get_mysqli(), $land_key);
    $query = $this->db->query("
        SELECT e.`account_key`, a.`nation_name`, u.`username`
        FROM `sanctions` as e
            LEFT JOIN
                `account` as a on account_key = a.`id`
                LEFT JOIN
                    `user` as u on user_key = u.`id`
        WHERE e.`land_key` = '" . $land_key . "';");
    $result = $query->result_array();
    return $result;
 }
 function get_embassy_by_player_and_land($account_key, $land_key)
 {
    $this->db->select('*');
    $this->db->from('embassy');
    $this->db->where('account_key', $account_key);
    $this->db->where('land_key', $land_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 function get_sanctions_by_player_and_land($account_key, $land_key)
 {
    $this->db->select('*');
    $this->db->from('sanctions');
    $this->db->where('account_key', $account_key);
    $this->db->where('land_key', $land_key);
    $query = $this->db->get();
    $result = $query->result_array();
    return isset($result[0]) ? $result[0] : false;
 }
 // Remove player embassy
 function remove_player_embassy($account_key, $land_key, $embassy_key)
 {
    // Remove embassy
    $data = array(
    'account_key' => $account_key,
    'land_key' => $land_key,
    );
    $this->db->delete('embassy', $data);

    // Remove effect with limit 1
    $data = array(
    'land_key' => $land_key,
    'modify_effect_key' => $embassy_key,
    );
    $this->db->delete('land_modifier', $data, 1);
 }
 // Remove player sanctions
 function remove_player_sanctions($account_key, $land_key, $sanctions_key)
 {
    // Remove sanctions
    $data = array(
    'account_key' => $account_key,
    'land_key' => $land_key,
    );
    $this->db->delete('sanctions', $data);

    // Remove effect with limit 1
    $data = array(
    'land_key' => $land_key,
    'modify_effect_key' => $sanctions_key,
    );
    $this->db->delete('land_modifier', $data, 1);
 }
 // Add player embassy
 function add_player_embassy($account_key, $land_key, $world_key, $embassy_key)
 {
    // Insert embassy
    $data = array(
    'account_key' => $account_key,
    'land_key' => $land_key,
    'world_key' => $world_key,
    );
    $this->db->insert('embassy', $data);

    // Insert land effect
    $data = array(
    'land_key' => $land_key,
    'modify_effect_key' => $embassy_key,
    );
    $this->db->insert('land_modifier', $data);
    return true;
 }
 // Add player sanctions
 function add_player_sanctions($account_key, $land_key, $world_key, $sanctions_key)
 {
    // Insert sanctions
    $data = array(
    'account_key' => $account_key,
    'land_key' => $land_key,
    'world_key' => $world_key,
    );
    $this->db->insert('sanctions', $data);

    // Insert land effect
    $data = array(
    'land_key' => $land_key,
    'modify_effect_key' => $sanctions_key,
    );
    $this->db->insert('land_modifier', $data);
    return true;
 }
 // Get effect of embassy
 function get_embassy_effect()
 {
    $this->db->select('*');
    $this->db->from('modify_effect');
    $this->db->where('is_embassy', 1);
    $query = $this->db->get();
    $result = $query->result_array();
    return $result[0];
 }
 // Get effect of sanctions
 function get_sanctions_effect()
 {
    $this->db->select('*');
    $this->db->from('modify_effect');
    $this->db->where('is_sanctions', 1);
    $query = $this->db->get();
    $result = $query->result_array();
    return $result[0];
 }
 // Remove all embassies of world for reset
 function remove_all_embassy_of_land($land_key)
 {
    $data = array(
    'land_key' => $land_key,
    );
    $this->db->delete('embassy', $data);
 }
 // Remove all embassies of world for reset
 function remove_all_sanctions_of_land($land_key)
 {
    $data = array(
    'land_key' => $land_key,
    );
    $this->db->delete('sanctions', $data);
 }
 // Remove all embassies of world for reset
 function remove_all_embassy_of_world($world_key)
 {
    $data = array(
    'world_key' => $world_key,
    );
    $this->db->delete('embassy', $data);
 }
 // Remove all embassies of world for reset
 function remove_all_sanctions_of_world($world_key)
 {
    $data = array(
    'world_key' => $world_key,
    );
    $this->db->delete('sanctions', $data);
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
 function remove_modifier_from_land($land_key, $modify_effect_key, $limit)
 {
    $this->db->where('land_key', $land_key);
    $this->db->where('modify_effect_key', $modify_effect_key);
    $this->db->limit($limit);
    $this->db->delete('land_modifier');
 }
 // Remove all modifiers from land
 function remove_modifiers_from_land($land_key)
 {
    $this->db->where('land_key', $land_key);
    $this->db->delete('land_modifier');

    $this->db->where('land_key', $land_key);
    $this->db->delete('embassy');

    $this->db->where('land_key', $land_key);
    $this->db->delete('sanctions');
 }
 // Remove all modifiers from land
 function remove_land_type_modifiers_from_land($land_key, $land_type_effect_keys)
 {
    $this->db->where('land_key', $land_key);
    $this->db->where_in('modify_effect_key', $land_type_effect_keys);
    $this->db->delete('land_modifier');
 }

 function add_weariness_to_account($account_id, $weariness)
 {
    $this->db->where('id', $account_id);
    $this->db->set('weariness', 'weariness+' . $weariness, FALSE);
    $this->db->update('account');
 }

 function subtract_weariness_from_account($account_id, $weariness)
 {
    $this->db->where('id', $account_id);
    $this->db->set('weariness', 'weariness-' . $weariness, FALSE);
    $this->db->update('account');
 }

 function set_weariness_from_account($account_id, $weariness)
 {
    $this->db->where('id', $account_id);
    $this->db->set('weariness', $weariness);
    $this->db->update('account');
 }
 function universal_decrease_weariness($weariness_decrease)
 {
    $this->db->where('weariness >=', $weariness_decrease);
    $this->db->set('weariness', 'weariness-' . $weariness_decrease, FALSE);
    $this->db->update('account');

    $this->db->where('weariness <=', $weariness_decrease);
    $this->db->set('weariness', 0);
    $this->db->update('account');
 }
 function truncate_modifiers()
 {
    $this->db->where('land_key >', 0);
    $this->db->delete('land_modifier');
 }

}