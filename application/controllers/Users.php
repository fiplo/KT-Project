<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    function __construct() {
        parent::__construct();

        //Formos validacija ir vartotojo modelis
        $this->output->enable_profiler(TRUE);
        $this->load->library('form_validation');
        $this->load->model('post');
        $this->load->model('user');

        //Vartotojo busena
        $this->isUserLoggedIn = $this->session->userdata('isUserLoggedIn');
    }

    public function index(){
        $data['data'] = $this->post->getAllThumbs();
        $this->load->view('posts/index', $data);

    }

    public function publish() {
        $data = $postData = array();
        if($this->isUserLoggedIn){
            $publisher = $this->session->userdata('userId');
            

            // Priimamas registracijos prasymas
            if ($this->input->post('postSubmit')){
                $this->form_validation->set_rules('postname', 'Name of Post', 'required');
                $this->form_validation->set_rules('postdesc', 'Description of Post', 'required');

                $config['upload_path']      = './uploads/';
                $config['allowed_types']    = 'gif|jpg|png';

                $filelist = array();

                $this->load->library('upload', $config);
                foreach($this->input->post('userfile') as $userfile){ 
                    if ($this->upload->do_upload($userfile)) {
                        array_push($filelist, $this->upload->data('full_path')); 
                        $data['success_msg'] = "File Uploaded!";
                    }
                }
                 
                $postData = array (
                    'postname' => strip_tags($this->input->post('postname')),
                    'postdesc' => strip_tags($this->input->post('postdesc')) 
                );

                if($this->form_validation->run() == true){
                    $insert = $this->post->insert($postData, $filelist, $publisher);
                    if($insert){
                        $this->session->set_userdata('success_msg', 'Jusu skelbimas ikeltas!');
                        redirect('http://project.link/');
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

    public function openPost($id) {
        $data['post'] = $this->post->getFiles($id);
        $this->load->view('posts/index', $data);
    }

    



    public function account() {
        $data = array();
        if($this->isUserLoggedIn){
            $con = array(
                'id' => $this->session->userdata('userId')
            );
            $data['user'] = $this->user->getRows($con);
            $data['data'] = $this->post->getFiles($this->session->userdata('userId'));

            //Perduodame vartotojo informacija puslapiui
            $this->load->view('elements/header', $data);
            $this->load->view('users/account', $data);
            $this->load->view('posts/index', $data);
            $this->load->view('elements/footer');
        } else {
            redirect('users/login');
        }
    }

    public function login(){
        $data = array();

        // Gauname zinutes is sesijos
        if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }

        // Priimamas prisijungimo prasymas
        if($this->input->post('loginSubmit')){
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'password', 'required');

            if($this->form_validation->run() == true) {
                $con = array(
                    'returnType' => 'single',
                    'conditions' => array(
                        'email' => $this->input->post('email'),
                        'password' => $this->input->post('password'),
                        'status' => 1
                    )
                );

                $checkLogin = $this->user->getRows($con);
                if($checkLogin){
                    $this->session->set_userdata('isUserLoggedIn', TRUE);
                    $this->session->set_userdata('userId', $checkLogin['id']);
                    redirect('users/account/');
                } else {
                    $data['error_msg'] = 'Neteisingas el. pastas arba slaptazodis, bandykite is naujo.';
                }
            } else {
                $data['error_msg'] = 'Prasome uzpildyti reikiamus laukus.';
            }
        }

        // Ikeliame puslapi
        $this->load->view('elements/header', $data);
        $this->load->view('users/login', $data);
        $this->load->view('elements/footer');
    }

    public function registration(){
        $data = $userData = array();

        // Priimamas registracijos prasymas
        if ($this->input->post('signupSubmit')){
            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');;
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('conf_password', 'confirm password', 'required|matches[password]');
            
            $userData = array (
                'first_name' => strip_tags($this->input->post('first_name')),
                'last_name' => strip_tags($this->input->post('last_name')),
                'email' => strip_tags($this->input->post('email')),
                'password' => strip_tags($this->input->post('password')),
                'gender' => strip_tags($this->input->post('gender')),
                'phone' => strip_tags($this->input->post('phone'))
            );

            if($this->form_validation->run() == true){
                $insert = $this->user->insert($userData);
                if($insert){
                    $this->session->set_userdata('success_msg', 'Jusu vartotojo registracija baigta. Prasome prisijungti prie savo vartotojo.');
                    redirect('users/login');
                } else {
                    $data['error_msg'] = 'Istiko problemos, bandykit dar karta veliau.';
                }
            } else {
                $data['error_msg'] = 'Prasome uzpildyti reikiamus laukus.';
            }
        }

        // Ikelta informacija
        $data['user'] = $userData;

        // Ikeliamas puslapis
        $this->load->view('elements/header', $data);
        $this->load->view('users/registration', $data);
        $this->load->view('elements/footer');
    }

    public function logout() {
        $this->session->unset_userdata('isUserLoggedIn');
        $this->session->unset_userdata('userId');
        $this->session->sess_destroy();
        redirect('users/login/');
    }

    // Patikrinimas ar egzistuoja vartotojas su duotu el. pastu
    public function email_check($str) {
        $con = array(
            'returnType' => 'count',
            'conditions' => array(
                'email' => $str
            )
        );

        $checkEmail = $this->user->getRows($con);
        if ($checkEmail > 0) {
            $this->form_validation->set_message('email_check', 'Duotas el. pastas jau naudojamas vartotojo');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}

?>
