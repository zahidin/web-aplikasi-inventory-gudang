<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_login');
	}
	public function index()
	{
		    $data['token_generate'] = $this->token_generate();
			$this->session->set_userdata($data);
			$this->load->view('login/login',$data);
	}

	public function token_generate(){
		return $tokens = md5(uniqid(rand(), true));
	}

	public function register(){
		$this->load->view('login/register');
	}

	public function proses_login(){
		$this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('password','Password','required');

		if($this->form_validation->run() == TRUE){
			$username = $this->input->post('username',TRUE);
			$password = $this->input->post('password',TRUE);

		if($this->session->userdata('token_generate') === $this->input->post('token'))
		{
			$cek =  $this->M_login->cek_user('user',$username);
			if( $cek->num_rows() != 1){
				$this->session->set_flashdata('msg','Username Belum Terdaftar Silahkan Register Dahulu');
				redirect(base_url());
			}else {

				$isi = $cek->row();
				if(password_verify($password,$isi->password) === TRUE){
					$data_session = array(
									'id' => $isi->id,
									'name' => $username,
									'email' => $isi->email,
									'status' => 'login',
									'role' => $isi->role,
									'last_login' => $isi->last_login
					);

					$this->session->set_userdata($data_session);

					$this->M_login->edit_user(['username' => $username],['last_login' => date('d-m-Y G:i')]);

						if($isi->role == 1){
							redirect(base_url('admin'));
						}else {
							redirect(base_url('user'));
						}

				}else {
					$this->session->set_flashdata('msg','Username Dan Password Salah');
					redirect(base_url());
				}
			}
		}else {
			redirect(base_url());
		}
	}
}
}
