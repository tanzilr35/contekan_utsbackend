<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Satuan extends CI_Controller
{
    // line 7 : membuat public fungsi construct
    public function __construct()
    {
         // line 10-15 : untuk memperbanyak deklarasi load
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }
    // line 17-22 : membuat fungsi index untuk menu satuan serta pemanggilan data
    public function index()
    {
        $data['title'] = "Satuan";
        $data['satuan'] = $this->admin->get('satuan');
        $this->template->load('templates/dashboard', 'satuan/data', $data);
    }
    // line 24-27 : membuat aturan validasi nama satuan yang dipanggil dari library
    private function _validasi()
    {
        $this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'required|trim');
    }
    // line 29-35 : membuat fungsi add untuk CRUD pada tabel satuan
    public function add()
    {
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Satuan";
            $this->template->load('templates/dashboard', 'satuan/add', $data);
        } else {
             //line 38-48 : menginputkan satuan barang
            $input = $this->input->post(null, true);
            $insert = $this->admin->insert('satuan', $input);
            if ($insert) {
                set_pesan('data berhasil disimpan');
                redirect('satuan');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('satuan/add');
            }
        }
    }
    // Line 50-70 membuat fungsi edit merupakan bagian CRUD dengan mendapatkan id pada data yang akan diedit
    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Satuan";
            $data['satuan'] = $this->admin->get('satuan', ['id_satuan' => $id]);
            $this->template->load('templates/dashboard', 'satuan/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $update = $this->admin->update('satuan', 'id_satuan', $id, $input);
            if ($update) {
                set_pesan('data berhasil disimpan');
                redirect('satuan');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('satuan/add');
            }
        }
    }
    // Line 74-84 : membuat fungsi delete merupakan bagian CRUD dengan mendapatkan id pada data yang akan dihapus
    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('satuan', 'id_satuan', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('satuan');
    }
}