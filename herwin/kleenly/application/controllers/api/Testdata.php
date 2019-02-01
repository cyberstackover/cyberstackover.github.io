<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once APPPATH . 'libraries/REST_Controller.php';
// require APPPATH . '/libraries/jwt/JWT.php';
// require APPPATH . '/libraries/jwt/BeforeValidException.php';
// require APPPATH . '/libraries/jwt/ExpiredException.php';
// require APPPATH . '/libraries/jwt/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class Testdata extends MY_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('M_login');
    $this->load->model('M_user');
  }


  //method untuk not found 404
  public function notfound($pesan){

    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_NOT_FOUND);

  }

  //method untuk bad request 400
  public function badreq($pesan){
    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_BAD_REQUEST);
  }

  //method untuk melihat token pada user
  public function generatetoken_post(){
    // $this->load->model('M_login');

    $date = new DateTime();

    $username = $this->post('username',TRUE);
    $pass = $this->post('password',TRUE);

    $dataadmin = $this->M_login->is_valid($username);

    if ($dataadmin) {
      if (password_verify($pass,$dataadmin['password'])) {

        $payload = $dataadmin;
        $payload['iat'] = $date->getTimestamp(); //waktu di buat
        $payload['exp'] = $date->getTimestamp() + 3600; //satu jam

        $output['id_token'] = JWT::encode($payload,$this->secretkey);
        $this->response([
          'status'=>'Success',
          'Message'=>'Token will expired in one hour.',
          'Token'=>$output,
          ],HTTP_OK);
      }else {
        $this->viewtokenfail($username,$pass);
      }
    }else {
      $this->viewtokenfail($username,$pass);
    }
  }

  //method untuk jika view token diatas fail
  public function viewtokenfail($username,$pass){
    $this->response([
      'status'=>'Error',
      'message'=>'Username and Password Combination Is Missmatch'
      ],HTTP_BAD_REQUEST);
  }

//method untuk mengecek token setiap melakukan post, put, etc
  public function validasitoken_post(){
    // $this->load->model('M_login');
    // $jwt = $this->input->get_request_header('Authorization');
    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;
        unset($decode->password);
        unset($decode->iat);
        unset($decode->exp);
        $this->response([
          'status'=>'Success',
          'data'=>$decode,
          ],HTTP_OK);
      }

    } catch (Exception $e) {
      // exit('Token Expired');
    	$this->response([
      	'status'=>'Error',
      	'message'=>'Token Expired'
      	],HTTP_BAD_REQUEST);
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
