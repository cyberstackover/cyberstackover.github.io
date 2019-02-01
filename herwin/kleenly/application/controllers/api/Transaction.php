<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once APPPATH . 'libraries/REST_Controller.php';
// require APPPATH . '/libraries/jwt/JWT.php';
// require APPPATH . '/libraries/jwt/BeforeValidException.php';
// require APPPATH . '/libraries/jwt/ExpiredException.php';
// require APPPATH . '/libraries/jwt/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class Transaction extends MY_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('M_login');
    $this->load->model('M_user');
    $this->load->model('M_master');
    $this->load->model('M_transaction');
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

  function topuplist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_transaction->topup_list($data);
          // print_r($viewservice);
          // exit;
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


  function transactionlist_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = $decode;

          $viewservice = $this->M_transaction->transaction_list($data);

          // print_r($viewservice);
          // exit;
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

  function topup_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'bank_id'=>$this->post('bank_id'),
          'rek_number'=>$this->post('rek_number'),
          'card_number'=>$this->post('card_number'),
          'account_name'=>$this->post('account_name'),
          'agent_id'=>$this->post('agent_id'),
          'topup_amount'=>$this->post('topup_amount'),
          'create_by'=> (int) $decode->id_agen
        ];

        $this->form_validation->set_rules('bank_id','bank_id','trim|max_length[255]');
        $this->form_validation->set_rules('rek_number','rek_number','trim|max_length[255]');
        $this->form_validation->set_rules('card_number','card_number','trim|max_length[255]');
        $this->form_validation->set_rules('account_name','account_name','trim|max_length[255]');
        $this->form_validation->set_rules('agent_id','agent_id','trim|max_length[255]');
        $this->form_validation->set_rules('topup_amount','topup_amount','trim|max_length[255]');
        
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_transaction->insert_topup($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Top Up Succesfully.',
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Top Up Unsuccesfully.'
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

  function transaction_post(){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        $data = [
          'order_id'=>$this->post('order_id'),
          'amount_tax'=>$this->post('amount_tax'),
          'commissions'=>$this->post('commissions'),
          'create_by'=> (int) $decode->id_agen
        ];

        $this->form_validation->set_rules('order_id','order_id','trim|max_length[255]');
        $this->form_validation->set_rules('amount_tax','amount_tax','trim|max_length[255]');
        $this->form_validation->set_rules('commissions','commissions','trim|max_length[255]');        
        
        if ($this->form_validation->run()==false) {
          $this->badreq($this->validation_errors());
        }else {
          // $simpan = $this->db->insert('member_kleenly', $data);
          $simpan = $this->M_transaction->insert_transaction($data);
          if ($simpan == true){
            $this->response([
              'message'=>'Transaction Succesfully.',
              'Inserted_id'=> $this->db->insert_id(),
              'Inserted'=> $data
            ],HTTP_OK);

          }else {

            $this->response([
              'message'=>'Transaction Unsuccesfully.'
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

  function omset_post($type){

    $jwt = $this->post('Authorization',TRUE);

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      // print_r($decode);
      //melakukan pengecekan database, jika username tersedia di database maka return true
      if ($this->M_login->is_valid_num($decode->username)>0) {
        // return true;

        // $data = [
        //   'order_id'=>$this->post('order_id'),
        //   'amount_tax'=>$this->post('amount_tax'),
        //   'commissions'=>$this->post('commissions'),
        //   'create_by'=> (int) $decode->id_agen
        // ];
		// $data = $decode;

		if ($type == 'weekly') {
			# code...

			// echo $date = date("Y-m-d",strtotime('monday this week')).' To '.date("Y-m-d",strtotime("sunday this week"));

			$start_date = date("Y-m-d",strtotime('monday this week'));
			$end_date = date("Y-m-d",strtotime("sunday this week"));

		} else if ($type == 'monthly') {
			# code...

			// echo $date = date("Y-m-01").' To '.date("Y-m-t"); 

			$start_date = date("Y-m-01");
			$end_date = date("Y-m-t");
		}

		$tanggal = array();

		$omsetv = array();

		$komisiv = array();

		$daterangetransaction = $this->M_transaction->date_list($start_date, $end_date);

		// echo $this->db->last_query();

		// print_r($daterangetransaction);

		// exit;

		for ($i=0; $i < count($daterangetransaction) ; $i++) { 
			# code...
			$getomset = $this->M_transaction->get_ommset($daterangetransaction[$i]['selected_date'], (int) $decode->id_agen);

			if ($getomset) {
				# code...
				$tanggal[] = $daterangetransaction[$i]['selected_date'];

				$omsetv[] = (int)$getomset['omset'];

				$komisiv[] = (int)$getomset['komisi'];
			} else {
				# code...
				$tanggal[] = $daterangetransaction[$i]['selected_date'];

				$omsetv[] = 0;

				$komisiv[] = 0;
			}
			
		}

		$ntransaction = $this->M_transaction->get_transaction((int) $decode->id_agen);

		if ($ntransaction) {
			# code...
			$nnota = (int)$ntransaction['nnota'];
			$omset = (int)$ntransaction['omset'];
			$komisi = (int)$ntransaction['komisi'];
			$ratarataomset = $omset / $nnota;
		} else {
			# code...
			$nnota = 0;
			$omset = 0;
			$komisi = 0;
			$ratarataomset = 0;
		}
		

	    // $viewservice = $this->M_transaction->transaction_list($data);

	      // print_r($viewservice);
	      // exit;
	      if ($daterangetransaction){
	        $this->response([
	          'status'=>'Succes',
	          'message'=>'Succes',
	          'nNota'=> $nnota,
	          'omsetv'=> $omset,
	          'average'=> $ratarataomset,
	          'komisi'=> $komisi,
	          'date'=> $tanggal,
	          'dateomset'=> $omsetv,
	          'datekomisi'=> $komisiv
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
