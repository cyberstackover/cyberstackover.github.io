<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once APPPATH . 'libraries/REST_Controller.php';
// require APPPATH . '/libraries/jwt/JWT.php';
// require APPPATH . '/libraries/jwt/BeforeValidException.php';
// require APPPATH . '/libraries/jwt/ExpiredException.php';
// require APPPATH . '/libraries/jwt/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class Orderdata extends MY_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('M_login');
    $this->load->model('M_user');
    $this->load->model('M_order');
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

  function orderlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewcustomer = $this->M_order->order_list($data);
          for ($i=0; $i < count($viewcustomer) ; $i++) { 
          	# code...
          	$viewdetail = $this->M_order->orderdetail_list($viewcustomer[$i]);
          	
          	$viewcustomer[$i]['detail_order'] = $viewdetail;
          	$viewcustomer[$i]['count_detail_order'] = count($viewdetail);
          	// $viewcustomer[$i]['count_detail_order'] = $this->db->last_query();

          	// exit;

          }
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

  function order_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {


        // return true;


      	if ($type == 'create' || $type == 'Create') {
      		# code...

		        $data = [
		          'order_code'=>$this->post('order_code'),
		          'agent_id'=>$this->post('agent_id'),
		          'customer_id'=>$this->post('customer_id'),
		          'completion_date'=>$this->post('completion_date'),
		          'note_order'=>$this->post('note_order'),
		          'payment_method'=>$this->post('payment_method'),
		          'total_tax'=>$this->post('total_tax'),
		          'amount_paid'=>$this->post('amount_paid'),
		          'payment_status'=>$this->post('payment_status'),
		          'payment_note'=>$this->post('payment_note'),
		          'commissions'=>$this->post('commissions'),
		          'order_status'=>$this->post('order_status'),
		          'create_by'=> (int) $decode->id_agen
		        ];

		        $this->form_validation->set_rules('order_code','order_code','trim|max_length[255]');
		        $this->form_validation->set_rules('agent_id','agent_id','trim|max_length[255]');
		        $this->form_validation->set_rules('customer_id','customer_id','trim|max_length[255]');
		        $this->form_validation->set_rules('completion_date','completion_date','trim|max_length[255]');
		        $this->form_validation->set_rules('note_order','note_order','trim|max_length[255]');
		        $this->form_validation->set_rules('payment_method','payment_method','trim|max_length[255]');
		        $this->form_validation->set_rules('total_tax','total_tax','trim|max_length[255]');
		        $this->form_validation->set_rules('amount_paid','amount_paid','trim|max_length[255]');
		        $this->form_validation->set_rules('payment_status','payment_status','trim|max_length[255]');
		        $this->form_validation->set_rules('payment_note','payment_note','trim|max_length[255]');
		        $this->form_validation->set_rules('commissions','commissions','trim|max_length[255]');
		        $this->form_validation->set_rules('order_status','order_status','trim|max_length[255]');
		        
		        
		        if ($this->form_validation->run()==false) {
		          $this->badreq($this->validation_errors());
		        }else {
		          // $simpan = $this->db->insert('member_kleenly', $data);
		          $simpan = $this->M_order->insert_order($data);
		          if ($simpan == true){
		            $this->response([
		              'message'=>'Order create Succesfully.',
		              'Inserted_id'=> $this->db->insert_id(),
		              'Inserted'=> $data
		            ],HTTP_OK);

		          }else {

		            $this->response([
		              'message'=>'Order create  Unsuccesfully.'
		            ],HTTP_BAD_REQUEST);

		          }
		        }

      	} else if ($type == 'update' || $type == 'Update') {
      		# code...
      	} else if ($type == 'delete' || $type == 'Delete') {
      		# code...
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

  function orderdetail_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {


        // return true;


      	if ($type == 'create' || $type == 'Create') {
      		# code...

		        $data = [
		          'order_id'=>$this->post('order_id'),
		          'service_id'=>$this->post('service_id'),
		          'order_quantity'=>$this->post('order_quantity'),
		          'item_quantity'=>$this->post('item_quantity'),
		          'note_service'=>$this->post('note_service'),
		          'create_by'=> (int) $decode->id_agen
		        ];

		        $this->form_validation->set_rules('order_id','order_id','trim|max_length[255]');
		        $this->form_validation->set_rules('service_id','service_id','trim|max_length[255]');
		        $this->form_validation->set_rules('order_quantity','order_quantity','trim|max_length[255]');
		        $this->form_validation->set_rules('item_quantity','item_quantity','trim|max_length[255]');
		        $this->form_validation->set_rules('note_service','note_service','trim|max_length[255]');
		        
		        
		        if ($this->form_validation->run()==false) {
		          $this->badreq($this->validation_errors());
		        }else {
		          // $simpan = $this->db->insert('member_kleenly', $data);
		          $simpan = $this->M_order->insert_order_detail($data);
		          if ($simpan == true){
		            $this->response([
		              'message'=>'Order create Succesfully.',
		              'Inserted_id'=> $this->db->insert_id(),
		              'Inserted'=> $data
		            ],HTTP_OK);

		          }else {

		            $this->response([
		              'message'=>'Order create  Unsuccesfully.'
		            ],HTTP_BAD_REQUEST);

		          }
		        }
		        
      	} else if ($type == 'update' || $type == 'Update') {
      		# code...
      	} else if ($type == 'delete' || $type == 'Delete') {
      		# code...
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
