<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_order extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();

  }

  public function service_list($type){
    $this->db->select('*');
    $this->db->from('packet_service_kleenly');

    if ($type == 'all') {
      # code...
    } else if ($type == 'active') {
      # code...
      $this->db->where('is_active', 1);
    } else if ($type == 'inactive') {
      # code...
      $this->db->where('is_active', 0);
    }
    // $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function bank_list(){
    $this->db->select('*');
    $this->db->from('master_list_bank');

    // $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function order_list($data){
    $this->db->select('*');
    $this->db->from('order_list_kleenly');
    $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function orderdetail_list($data){
    $this->db->select('*');
    $this->db->from('order_detail_kleenly');
    $this->db->where('order_id', $data['order_id']);
    $query = $this->db->get();

    echo $this->db->last_query();
    return $query->result_array();
  }

  public function status_order_list(){
    $this->db->select('*');
    $this->db->from('master_status_order');

    // $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function status_payment_list(){
    $this->db->select('*');
    $this->db->from('master_status_payment');

    // $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function insert_order($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('order_list_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_order_detail($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('order_detail_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_service($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('packet_service_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function update_service($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('service_id', $data['service_id']);
    $query = $this->db->update('packet_service_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function disable_service($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('service_id', $data['service_id']);
    $query = $this->db->update('packet_service_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

}
