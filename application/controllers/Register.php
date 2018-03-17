<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_login');
	}

	public function index()
	{
		$this->load->view('login/register');
	}

  private function hash_password($password){
    return password_hash($password,PASSWORD_DEFAULT);
  }

  public function proses_register(){

    $this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('email','Email','required');
    $this->form_validation->set_rules('password','Password','required');
    $this->form_validation->set_rules('confirm_password','Confirm Password','required|matches[password]');

    if($this->form_validation->run() == TRUE){
      $username = $this->input->post('username',TRUE);
			$email    = $this->input->post('email',TRUE);
      $password = $this->input->post('password',TRUE);
  
      if($this->M_login->cek_username('user',$username)){
				$this->session->set_flashdata('msg','Username Telah Digunakan');
				redirect(base_url('login/register'));

      }else{
        $data = array(
              'username' => $username,
							'email' 	 => $email,
              'password' => $this->hash_password($password)
        );

        $dataUpload = array(
              'id'     => '',
              'username_user' => $username,
              'nama_file' => 'nopic2.png',
              'ukuran_file' => '6.33'
        );

        $this->M_login->insert('user',$data);
        $this->M_login->insert('tb_upload_gambar_user',$dataUpload);

				$this->session->set_flashdata('msg_terdaftar','Anda Berhasil Register');
        redirect(base_url('login/register'));
      }
    }else {
      $this->load->view('login/register');
    }
  }
}

?>
