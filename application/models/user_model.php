<?php

Class user_model extends CI_Model
{
 // Login
 function login($username, $password)
 {
    $this->db->select('id, username, password');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->where('password', MD5($password));
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
        'password' => $password,
        'email' => $email,
        'facebook_id' => $facebook_id,
        'profile_picture' => 'default.png'
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

}
?>