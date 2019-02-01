<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master extends CI_Model{

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

  public function notification_list(){
    $this->db->select('*');
    $this->db->from('notification_kleenly');

    $this->db->where('is_active', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function promotion_list(){
    $this->db->select('*');
    $this->db->from('promotion_kleenly');

    $this->db->where('is_active', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function changeprice_list(){
    $this->db->select('*');
    $this->db->from('price_list_change_kleenly');

    $this->db->where('is_active', 1);
    $query = $this->db->get();
    return $query->result_array();
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

  public function insert_notification($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('notification_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_promotion($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('promotion_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_changeprice($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('price_list_change_kleenly',$data);
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

  public function update_notification($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('notif_id', $data['notif_id']);
    $query = $this->db->update('notification_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function update_promotion($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('promo_id', $data['promo_id']);
    $query = $this->db->update('promotion_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function update_changeprice($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('change_id', $data['change_id']);
    $query = $this->db->update('price_list_change_kleenly',$data);
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

  public function disable_notification($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('notif_id', $data['notif_id']);
    $query = $this->db->update('notification_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function disable_promotion($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('promo_id', $data['promo_id']);
    $query = $this->db->update('promotion_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function disable_changeprice($data){
    $this->db->set('modified_date', 'NOW()', FALSE);
    $this->db->where('change_id', $data['change_id']);
    $query = $this->db->update('price_list_change_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

}
