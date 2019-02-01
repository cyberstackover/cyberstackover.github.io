<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();

  }

  public function agen_list(){
    $this->db->select('*');
    $this->db->from('member_kleenly');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function customer_list($data){
    $this->db->select('*');
    $this->db->from('customer_kleenly');
    $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function insert_agen($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('member_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_customer($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('customer_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }
}
