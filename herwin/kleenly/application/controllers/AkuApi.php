<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';

class AkuApi extends Restdata{

    public function __construct(){
      parent::__construct();
      $this->load->model('Mymodel');
      $this->cektoken();
    }

    function index_get()
    {
      echo 'GET Request NOT Acceptable <br>'.current_url();
    }

    function index_post()
    {
      echo 'POST Request NOT Acceptable<br>'.current_url();
    }

    function index_put()
    {
      echo 'PUT Request Not Acceptable <br>'.current_url();
    }

    function index_delete()
    {
      echo 'DELETE Request Not Acceptable <br>'.current_url();
    }

    function produk_get(){
      $id = $this->uri->segment(3);
      if ($id == '') {
        $pro = $this->db->get('produk_anggi')->result();
      } else {
        $this->db->where('id_produk', $id);
        $pro = $this->db->get('produk_anggi')->result();
      }
      $this->response($pro, 200);
    }
    
    function produk_post(){
      $data = ['id_produk'=>$this->post('id'),
               'nama'=>$this->post('nama'),
               'deksripsi'=>$this->post('deskripsi'),
               'hrg'=>$this->post('harga'),
               'gambar'=>$this->post('file')];
      $simpan = $this->db->insert('produk_anggi', $data);
      if ($simpan) {
        $this->response([
          'status'=>'Success',
          'Inserted'=>$data],HTTP_OK);
      }
    }
}
