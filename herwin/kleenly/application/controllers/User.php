<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';
use \Firebase\JWT\JWT;

class User extends Restdata{

  public function __construct(){
    parent::__construct();
    $this->load->database();
    $this->load->model('M_login');
    $this->load->model('M_user');
  }

  //method untuk melakukan penambahan admin (post)
  function registeragen_post(){
    $data = [
      'code_agen'=>$this->post('code_agen'),
      'username'=>$this->post('username'),
      'password'=>password_hash($this->post('password'),PASSWORD_DEFAULT),
      'nama_agen'=>$this->post('nama_agen'),
      'contact_num'=>$this->post('contact_num'),
      'email_agen'=>$this->post('email_agen'),
      'address'=>$this->post('address'),
      'is_active'=>1,
      'register_by'=>$this->post('register_by')
    ];

    $this->form_validation->set_rules('username','username','trim|max_length[50]');
    $this->form_validation->set_rules('password','Password','trim|min_length[8]');

    if ($this->form_validation->run()==false) {
      $this->badreq($this->validation_errors());
    }else {
      // $simpan = $this->db->insert('member_kleenly', $data);
      $simpan = $this->M_user->insert_agen($data);
      if ($simpan == true){
        $this->response([
          'message'=>'Agen Create Succesfully.',
          'Inserted_id'=> $this->db->insert_id(),
          'Inserted'=> $data
        ],HTTP_OK);

      }else {

        $this->response([
          'message'=>'Agen Create Unsuccesfully.'
        ],HTTP_BAD_REQUEST);

      }
    }
  }

  function registercustomer_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'customer_code'=>$this->post('customer_code'),
          'customer_name'=>$this->post('customer_name'),
          'customer_num'=>$this->post('customer_num'),
          'customer_address'=>$this->post('customer_address'),
          'create_by'=>$decode->nama_agen
        ];

        $this->form_validation->set_rules('customer_code','customer_code','trim|max_length[255]');
        $this->form_validation->set_rules('customer_name','customer_name','trim|max_length[255]');
        $this->form_validation->set_rules('customer_num','customer_num','trim|max_length[255]');
        $this->form_validation->set_rules('customer_address','customer_address','trim|max_length[255]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_user->insert_customer($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Customer Create Succesfully.',
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Customer Create Unsuccesfully.'
            ],HTTP_BAD_REQUEST);

          }
        }
      }

    } catch (Exception $e) {
      // exit('Token Expired');
      $this->response([
        'status'=>'Error',
        'message'=>'Token Expired'
        ],HTTP_BAD_REQUEST);
    }
    
  }

  function agenlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewagen = $this->M_user->agen_list();
          if ($viewagen == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewagen
            ],HTTP_OK);

          }else {

            $this->response([
              'status'=>'Error',
              'message'=>'Something terrible happened. Please, try again later.'
            ],HTTP_BAD_REQUEST);

          }
      }

    } catch (Exception $e) {
      // exit('Token Expired');
      $this->response([
        'status'=>'Error',
        'message'=>'Token Expired'
        ],HTTP_BAD_REQUEST);
    }
    
  }

  function customerlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewcustomer = $this->M_user->customer_list($data);
          if ($viewcustomer == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewcustomer
            ],HTTP_OK);

          }else {

            $this->response([
              'status'=>'Error',
              'message'=>'Something terrible happened. Please, try again later.'
            ],HTTP_BAD_REQUEST);

          }
      }

    } catch (Exception $e) {
      // exit('Token Expired');
      $this->response([
        'status'=>'Error',
        'message'=>'Token Expired'
        ],HTTP_BAD_REQUEST);
    }
    
  }

}
