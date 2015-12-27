<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class user_model extends CI_Model
{
 // Login
 function login($username, $password)
 {
    $this->db->select('*');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->where('password', password_verify($password, PASSWORD_BCRYPT));
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
        return $query->result_array();
    } else {
        return false;
    }
 }
 // Register
 function register($username, $password, $email, $facebook_id)
 {
    $this->db->select('username');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() == 1) {
        return false;
    } else {
        // Insert user into user
        $user_default = '';
        $data = array(
        'username' => $username,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'email' => $email,
        'facebook_id' => $facebook_id
        );
        $this->db->insert('user', $data);

        // Find user id
        $this->db->select_max('id');
        $this->db->from('user');
        $this->db->limit(1);
        $query = $this->db->get()->row();
        $user_id = $query->id;
        return $user_id;
    }
 }
 // Get all users
 function get_all_users()
 {
    $this->db->select('*');
    $this->db->from('user');
    $query = $this->db->get();
    return $query->result_array();
 }
 // Get user
 function get_user($user_id)
 {
    $this->db->select('*');
    $this->db->from('user');
    $this->db->where('id', $user_id);
    $this->db->limit(1);
    $query = $this->db->get();
    $result = $query->result_array();
    return $result[0];
 }

}
?>