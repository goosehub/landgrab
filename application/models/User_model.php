  <?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  Class user_model extends CI_Model
  {
    function this_account($world_id)
    {
        if (!$this->session->userdata('user')) {
            return false;
        }
        $user_id = $this->session->userdata('user')['id'];
        $account = $this->get_account_by_user($user_id, $world_id);
        if (!$account) {
            return false;
        }
        $this->account_loaded($account['id']);
        return $account;
    }
    function get_account_by_user($user_key, $world_key)
    {
        $this->db->select('account.*, user.username, user.id as user_id');
        $this->db->from('account');
        $this->db->join('user', 'user.id = account.user_key', 'left');
        $this->db->where('account.user_key', $user_key);
        $this->db->where('account.world_key', $world_key);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return isset($result[0]) ? $result[0] : false;
    }
    function get_account_by_id($account_id)
    {
        $this->db->select('account.*, user.username');
        $this->db->from('account');
        $this->db->join('user', 'user.id = account.user_key', 'left');
        $this->db->where('account.id', $account_id);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return isset($result[0]) ? $result[0] : false;
    }
    function get_all_active_accounts()
    {
        $this->db->select('*');
        $this->db->from('account');
        $this->db->where('is_active', true);
        $query = $this->db->get();
        $result = $query->result_array();
        return isset($result[0]) ? $result[0] : false;
    }
    function get_all_users()
    {
        $this->db->select('id, username, created');
        $this->db->from('user');
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_user_by_id($user_id)
    {
        $this->db->select('id, username, created');
        $this->db->from('user');
        $this->db->where('id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result_array();
        return isset($result[0]) ? $result[0] : false;
    }
    function login($username, $password)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $result = $query->result_array();
            return isset($result[0]) ? $result[0] : false;
        } else {
            return false;
        }
    }
    function register($username, $password, $email, $facebook_id, $ip, $ip_frequency_register, $ab_test)
    {
        $this->db->select('username');
        $this->db->from('user');
        $this->db->where('ip', $ip);
        $this->db->where('created > NOW() - INTERVAL ' . $ip_frequency_register . ' MINUTE');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0 && !is_dev()) {
            return 'ip_fail';
        }

        $this->db->select('username');
        $this->db->from('user');
        $this->db->where('username', $username);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return false;
        } else {
            $data = array(
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'email' => $email,
                'facebook_id' => $facebook_id,
                'ip' => $ip,
                'ab_test' => $ab_test,
                'modified' => date('Y-m-d H:i:s', time()),
            );
            $this->db->insert('user', $data);

            $this->db->select_max('id');
            $this->db->from('user');
            $this->db->limit(1);
            $query = $this->db->get()->row();
            $user_id = $query->id;
            return $user_id;
        }
    }
    function create_player_account($user_key, $world_key, $color, $nation_name, $nation_flag, $leader_portrait)
    {
        $data = array(
            'world_key' => $world_key,
            'user_key' => $user_key,
            'color' => $color,
            'nation_name' => $nation_name,
            'nation_flag' => $nation_flag,
            'leader_portrait' => $leader_portrait,
            'power_structure' => DEFAULT_POWER_STRUCTURE,
            'tax_rate' => DEFAULT_TAX_RATE,
            'ideology' => DEFAULT_IDEOLOGY,
            'last_load' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'tutorial' => 0,
            'modified' => date('Y-m-d H:i:s', time()),
        );
        $this->db->insert('account', $data);

        $this->db->select_max('id');
        $this->db->from('account');
        $this->db->limit(1);
        $query = $this->db->get()->row();
        $account_id = $query->id;

        $this->create_supply_lookup($account_id);

        return $account_id;
    }
    function create_supply_lookup($account_id) {
        $supplies = $this->get_all_supply();
        foreach ($supplies as $supply) {
            $default = 0;
            if ($supply['slug'] === 'support') {
                $default = 100;
            }
            $data = array(
                'account_key' => $account_id,
                'supply_key' => $supply['id'],
                'amount' => $default,
            );
            $this->db->insert('supply_account_lookup', $data);
        }
    }
    function get_all_supply()
    {
        $this->db->select('*');
        $this->db->from('supply');
        $query = $this->db->get();
        return $query->result_array();
    }
    function update_password($user_id, $password)
    {
        $data = array(
            'password' => password_hash($password, PASSWORD_BCRYPT),
        );
        $this->db->where('id', $user_id);
        $this->db->update('user', $data);
        return true;
    }
    function update_account_info($account_id, $color, $nation_name, $nation_flag, $leader_portrait)
    {
        $this->update_account($account_id, $color, $nation_name, $nation_flag, $leader_portrait);
        $this->update_account_tile_colors($account_id, $color);
        $this->update_account_unit_colors($account_id, $color);
        return true;
    }
    function update_account($account_id, $color, $nation_name, $nation_flag, $leader_portrait)
    {
        $data = array(
            'color' => $color,
            'nation_name' => $nation_name,
            'nation_flag' => $nation_flag,
            'leader_portrait' => $leader_portrait
        );
        $this->db->where('id', $account_id);
        $this->db->update('account', $data);
    }
    function update_account_tile_colors($account_id, $color)
    {
        $data = array(
            'color' => $color,
            'modified' => date('Y-m-d H:i:s', time())
        );
        $this->db->where('account_key', $account_id);
        $this->db->update('tile', $data);
    }
    function update_account_unit_colors($account_id, $color)
    {
        $data = array(
            'unit_owner_color' => $color,
            'modified' => date('Y-m-d H:i:s', time())
        );
        $this->db->where('unit_owner_key', $account_id);
        $this->db->update('tile', $data);
    }
    function update_account_tutorial($account_id, $tutorial)
    {
        $data = array(
            'tutorial' => $tutorial
        );
        $this->db->where('id', $account_id);
        $this->db->update('account', $data);
        return true;
    }
    function account_loaded($account_id)
    {
        $data = array(
            'last_load' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $account_id);
        $this->db->update('account', $data);
        return true;
    }
    function record_ip_request($ip, $request)
    {
        $data = array(
            'ip' => $ip,
            'request' => $request
        );
        $this->db->insert('ip_request', $data);
    }
    function check_ip_request_since_timestamp($ip, $request, $timestamp)
    {
        $this->db->select('*');
        $this->db->from('ip_request');
        $this->db->where('ip', $ip);
        $this->db->where('request', $request);
        $this->db->where('timestamp >', $timestamp);
        $query = $this->db->get();
        return $query->result_array();
    }
    function record_slug_hit($marketing_slug)
    {
        if (!$marketing_slug) {
            return;
        }
        $data = array(
            'marketing_slug' => $marketing_slug
        );
        $this->db->insert('analytics', $data);
    }
}
?>