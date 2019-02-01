<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once APPPATH . 'libraries/REST_Controller.php';
// require APPPATH . '/libraries/jwt/JWT.php';
// require APPPATH . '/libraries/jwt/BeforeValidException.php';
// require APPPATH . '/libraries/jwt/ExpiredException.php';
// require APPPATH . '/libraries/jwt/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class Masterdata extends MY_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('M_login');
    $this->load->model('M_user');
    $this->load->model('M_master');
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

  function createservice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'service_code'=>$this->post('service_code'),
          'service_name'=>$this->post('service_name'),
          'service_description'=>$this->post('service_description'),
          'service_duration'=>$this->post('service_duration'),
          'service_tax'=>$this->post('service_tax'),
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('service_code','service_code','trim|max_length[16]');
        $this->form_validation->set_rules('service_name','service_name','trim|max_length[255]');
        $this->form_validation->set_rules('service_description','service_description','trim|max_length[999]');
        $this->form_validation->set_rules('service_duration','service_duration','trim|max_length[10]');
        $this->form_validation->set_rules('service_tax','service_tax','trim|max_length[7]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->insert_service($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Service Create Succesfully.',
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Service Create Unsuccesfully.'
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

  function servicelist_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->service_list($type);
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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


  function banklist_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->bank_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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

  function statusorderlist_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->status_order_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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

  function statuspaymentlist_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->status_payment_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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


  function updateservice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'service_id'=>$this->post('service_id'),
          'service_code'=>$this->post('service_code'),
          'service_name'=>$this->post('service_name'),
          'service_description'=>$this->post('service_description'),
          'service_duration'=>$this->post('service_duration'),
          'service_tax'=>$this->post('service_tax'),
          'modified_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('service_id','service_id','trim|min_length[1]');
        $this->form_validation->set_rules('service_code','service_code','trim|max_length[16]');
        $this->form_validation->set_rules('service_name','service_name','trim|max_length[255]');
        $this->form_validation->set_rules('service_description','service_description','trim|max_length[999]');
        $this->form_validation->set_rules('service_duration','service_duration','trim|max_length[10]');
        $this->form_validation->set_rules('service_tax','service_tax','trim|max_length[7]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->update_service($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Service Update Succesfully.',
              'status'=> true
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Service Update Unsuccesfully.'
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

  function disableservice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'service_id'=>$this->post('service_id'),
          'is_active'=>0,
          'modified_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('service_id','service_id','trim|min_length[1]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->disable_service($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Service Disable Succesfully.',
              'status'=> true
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Service Disable Unsuccesfully.'
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


  function createnotification_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'notif_title'=>$this->post('notif_title'),
          'notif_description'=>$this->post('notif_description'),
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('notif_description','notif_description','trim|max_length[255]');
        $this->form_validation->set_rules('notif_description','notif_description','trim|max_length[999]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->insert_notification($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Create Notification Succesfully.',
              'status'=> true,
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Create Notification Unsuccesfully.'
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

  function notificationlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->notification_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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


  function updatenotification_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

         $data = [
          'notif_id'=>$this->post('notif_id'),
          'notif_title'=>$this->post('notif_title'),
          'notif_description'=>$this->post('notif_description'),
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('notif_description','notif_description','trim|max_length[255]');
        $this->form_validation->set_rules('notif_description','notif_description','trim|max_length[999]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->update_notification($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Notification Update Succesfully.',
              'status'=> true,
              'data_update' => $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Notification Update Unsuccesfully.'
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

  function disablenotification_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'notif_id'=>$this->post('notif_id'),
          'is_active'=>0,
          'modified_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('notif_id','notif_id','trim|min_length[1]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->disable_notification($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Notification Disable Succesfully.',
              'status'=> true,
              'data_update' => $data,
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Notification Disable Unsuccesfully.'
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

  function createpromotion_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'promo_title'=>$this->post('promo_title'),
          'promo_description'=>$this->post('promo_description'),
          'start_date'=>$this->post('start_date'),
          'end_date'=>$this->post('end_date'),
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('promo_title','promo_title','trim|max_length[255]');
        $this->form_validation->set_rules('promo_description','promo_description','trim|max_length[999]');

        $this->form_validation->set_rules('start_date','start_date','trim|max_length[10]');
        $this->form_validation->set_rules('end_date','end_date','trim|max_length[10]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->insert_promotion($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Create promotion Succesfully.',
              'status'=> true,
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Create promotion Unsuccesfully.'
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

  function promotionlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->promotion_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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


  function updatepromotion_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'promo_id'=>$this->post('promo_id'),
          'promo_title'=>$this->post('promo_title'),
          'promo_description'=>$this->post('promo_description'),
          'start_date'=>$this->post('start_date'),
          'end_date'=>$this->post('end_date'),
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('promo_id','promo_id','trim|min_length[1]');
        $this->form_validation->set_rules('promo_title','promo_title','trim|max_length[255]');
        $this->form_validation->set_rules('promo_description','promo_description','trim|max_length[999]');

        $this->form_validation->set_rules('start_date','start_date','trim|max_length[10]');
        $this->form_validation->set_rules('end_date','end_date','trim|max_length[10]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->update_promotion($data);
          if ($simpan == true){
            $this->response([
              'message'=>'promotion Update Succesfully.',
              'status'=> true,
              'data_update' => $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'promotion Update Unsuccesfully.'
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

  function disablepromotion_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'promo_id'=>$this->post('promo_id'),
          'is_active'=>0,
          'modified_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('promo_id','promo_id','trim|min_length[1]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->disable_promotion($data);
          if ($simpan == true){
            $this->response([
              'message'=>'promotion Disable Succesfully.',
              'status'=> true,
              'data_update' => $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'promotion Disable Unsuccesfully.'
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

  function changeprice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $time=strtotime($this->post('start_date'));
        $month=date("n",$time);
        $year=date("Y",$time);

        $data = [
          'service_id'=>$this->post('service_id'),
          'old_price'=>$this->post('old_price'),
          'new_price'=>$this->post('new_price'),
          'note'=>$this->post('note'),
          'start_date'=>$this->post('start_date'),
          'n_year'=>$month,
          'n_month'=>$year,
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('service_id','service_id','trim|min_length[1]');
        $this->form_validation->set_rules('old_price','old_price','trim|min_length[1]');
        $this->form_validation->set_rules('note','note','trim|max_length[999]');
        $this->form_validation->set_rules('new_price','new_price','trim|min_length[1]');
        $this->form_validation->set_rules('start_date','start_date','trim|max_length[10]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->insert_changeprice($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Change Price Succesfully.',
              'status'=> true,
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Change Price Unsuccesfully.'
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

  function changepricelist_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_master->changeprice_list();
          if ($viewservice == true){
            $this->response([
              'status'=>'Succes',
              'message'=>'Succes',
              'data'=> $viewservice
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


  function updatechangeprice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

         $data = [
          'service_id'=>$this->post('service_id'),
          'change_id'=>$this->post('change_id'),
          'old_price'=>$this->post('old_price'),
          'new_price'=>$this->post('new_price'),
          'note'=>$this->post('note'),
          'start_date'=>$this->post('start_date'),
          'n_year'=>$month,
          'n_month'=>$year,
          'is_active'=>1,
          'create_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('service_id','service_id','trim|min_length[1]');
        $this->form_validation->set_rules('change_id','change_id','trim|min_length[1]');
        $this->form_validation->set_rules('old_price','old_price','trim|min_length[1]');
        $this->form_validation->set_rules('note','note','trim|max_length[999]');
        $this->form_validation->set_rules('new_price','new_price','trim|min_length[1]');
        $this->form_validation->set_rules('start_date','start_date','trim|max_length[10]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->update_changeprice($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Change Price Update Succesfully.',
              'status'=> true,
              'data_update' => $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Change Price Update Unsuccesfully.'
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

  function disablechangeprice_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'change_id'=>$this->post('change_id'),
          'is_active'=>0,
          'modified_by'=>$decode->id_agen
        ];

        $this->form_validation->set_rules('change_id','change_id','trim|min_length[1]');
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_master->disable_changeprice($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Change Price Disable Succesfully.',
              'status'=> true,
              'data_update' => $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Change Price Disable Unsuccesfully.'
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

  
}
