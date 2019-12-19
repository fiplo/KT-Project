<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends CI_Controller {
    function __construct() {
        parent::__construct();

        //Formos validacija ir vartotojo modelis
        $this->load->library('form_validation');
        $this->load->model('post');

        //Vartotojo busena
        $this->isUserLoggedIn = $this->session->userdata('isUserLoggedIn');
    }

    public function index(){
        $data = $this->post->getAllThumbs();
        redirect('posts/index', $data);
    }

    public function publishPost() {
        $data = array();
        if($this->isUserLoggedIn){
            $publisher = $this->session->userdata('userId');
            
            $data = $postData = array();

            // Priimamas registracijos prasymas
            if ($this->input->post('postSubmit')){
                $this->form_validation->set_rules('postname', 'Name of Post', 'required');
                $this->form_validation->set_rules('postdesc', 'Description of Post', 'required');

                $config['upload_path']      = './uploads/';
                $config['allowed_types']    = 'gif|jpg|png';
                $config['max_size']         = '4096';

                $filelist;

                $this->load->library('upload', $config);

                foreach($this->input->post('userfile') as $userfile){
                    if ($this->upload->do_upload($userfile)) {
                        array_push($filelist, $this->upload->data('full_path')); 
                    }
                }
                
                $postData = array (
                    'postname' => strip_tags($this->input->post('postname')),
                    'postdesc' => strip_tags($this->input->post('postdesc')), 
                );

                if($this->form_validation->run() == true){
                    $insert = $this->post->insert($userData, $filelist, $publisher);
                    if($insert){
                        $this->session->set_userdata('success_msg', 'Jusu skelbimas ikeltas!');
                        redirect('posts/index');
                    } else {
                        $data['error_msg'] = 'Istiko problemos, bandykit dar karta veliau.';
                    }
                } else {
                    $data['error_msg'] = 'Prasome uzpildyti reikiamus laukus.';
                }
            }

            $data['post'] = $postData;

            //Perduodame vartotojo informacija puslapiui
            $this->load->view('elements/header', $data);
            $this->load->view('posts/publish', $data);
            $this->load->view('elements/footer');
        } else {
            redirect('users/login');
        }
    }

    
}

?>
