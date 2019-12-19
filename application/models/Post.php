<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Model{
    function __construct() {
        $this->table ='posts';
        $this->links ='post_file';
        $this->files ='files';

    }

    /*
     * Grazina skelbimo duomenis is duombazes
     */
    function getRows($params = array()) {
        $this->db->select('*');
        $this->db->from($this->table);

        if(array_key_exists("conditions", $params)){
            foreach($params['conditions'] as $key => $val){
                $this->db->where($key, $val);
            }
        }

        if(array_key_exists("returnType",$params) && $params['returnType'] == 'count') {
            $result = $this->db->count_all_results();
        } else {
            if(array_key_exists("id", $params) || $params['returnType'] == 'single') { 
                if(!empty($params['id'])) {
                    $this->db->where('id', $params['id']);
                }
                $query = $this->db->get();
                $result = $query->row_array();
            } else {
                $this->db->order_by('id', 'desc');
                if(array_key_exists("start",$params) && array_key_exists("limit",$params)) {
                    $this->db->limit($params['limit'], $params['start']);
                } elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)) {
                    $this->db->limit($params['limit']);
                }

                $query = $this->db->get();
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }

        return $result;
    }

    public function getAllPosts() {
        $this->db->select('*');
        $this->db->from($this->table);

        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function getAllThumbs() {
        /*
        $this->db->select($this->table+'.postname,'+$this->table+'.postdesc,'+$this->table+'.modified, ANY_VALUE('+$this->files+'.fullpath) as path');
        $this->db->from($this->table);
        $this->db->join($this->links, $this->links+'.post_id='+$this->table+'.id');
        $this->db->join($this->files, $this->links+'.file_id='+$this->files+'.id');
        $this->db->group_by($this->table+'.postname,'+$this->table+'.postdesc,'+$this->table+'.modified');
         */
        $query = $this->db->query('SELECT posts.postname, posts.postdesc, posts.modified, ANY_VALUE(files.fullpath) as path FROM posts JOIN post_file on post_file.post_id = posts.id JOIN files ON post_file.file_id = files.id GROUP BY posts.postname, posts.postdesc, posts.modified;');
        $result = $query->result_array();
        return $result;
    }

    public function getFiles($data) {
        $query = $this->db->query("SELECT posts.postname, posts.postdesc, posts.modified, ANY_VALUE(files.fullpath) as path FROM posts JOIN post_file on post_file.post_id = posts.id JOIN files ON post_file.file_id = files.id WHERE post_id = $data ;");

        $result=$query->result_array();
        
        return $result;
    }

    /*
     * Ikelia skelbimo informacija i duombaze
     */
    public function insert($data = array(), $files = array(), $user) {
        if(!empty($data)) {
            foreach($files as $file) {
                $this->db->insert($this->files, $file);
                $insertedFile = $this->db->insert_id();
                $this->db->insert($this->links, array($data['id'], $insertedFile));
            }
            // Sukurti datas, jeigu jos nera priskirtos
            if(!array_key_exists("created", $data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists("modified", $data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }

            // Ikeliame skelbimo informacija
            $insert = $this->db->insert($this->table, $data);
            $postid = $this->db->insert_id();
            $this->db->insert('user_post', array($user, $postid));

            // Grazinama busena
            return $insert?$this->db->insert_id():false;
        }
        return false;
    }
}
?>
