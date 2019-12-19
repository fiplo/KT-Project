<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthModel extends CI_Model {
        
    private $login = 'login';

    function get_user($username, $password) {
        $this->db->where('username', $username);
        $this->db->where('password', $password);
    
        $result = $this->db->get($this->login)->result();
                
        if($result) {
            return $result;
                                
        }
        return NULL;
    }
}
?>
