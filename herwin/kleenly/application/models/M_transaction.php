<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_transaction extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();

  }

  

  public function topup_list($data){
    $this->db->select('*');
    $this->db->from('top_up_history_kleenly');

    $this->db->where('create_by', $data->id_agen);
    $query = $this->db->get();
    // echo $this->db->last_query();
    return $query->result_array();
  }

  public function transaction_list($data){
    $this->db->select('*');
    $this->db->from('transaction_history_kleenly');

    $this->db->where('create_by', $data->id_agen);

    $query = $this->db->get();
    // echo $this->db->last_query();
    return $query->result_array();
  }

  public function date_list($start, $end){
    
    $query = $this->db->query("select * from 
    (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
     (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
     (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
     (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
     (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
     (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
    where selected_date between '".$start."' and '".$end."'");

    return $query->result_array();

  }

  public function get_ommset($date_selected, $id_agen){
    $this->db->select('SUM(amount_tax) AS omset, SUM(commissions) AS komisi, date(create_date) AS tanggal');
    $this->db->from('transaction_history_kleenly');
    $this->db->where('create_by', $id_agen);
    $this->db->where('date(create_date)', $date_selected);

    $query = $this->db->get();
    // echo $this->db->last_query();
    return $query->row_array();
  }

  public function get_transaction($id_agen){
    $this->db->select('SUM(amount_tax) AS omset, SUM(commissions) AS komisi, COUNT(amount_tax) as nnota');
    $this->db->from('transaction_history_kleenly');
    $this->db->where('create_by', $id_agen);
    $query = $this->db->get();
    // echo $this->db->last_query();
    return $query->row_array();
  }

  public function insert_topup($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('top_up_history_kleenly',$data);
    if($query == true){
      return true;
    }else{
      return false;
    }
  }

  public function insert_transaction($data){
    $this->db->set('create_date', 'NOW()', FALSE);
    $query = $this->db->insert('transaction_history_kleenly',$data);
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
