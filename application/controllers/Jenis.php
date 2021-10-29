<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jenis extends CI_Controller
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
    // line 17-22 : membuat fungsi index untuk menu jenis serta pemanggilan data
    public function index()
    {
        $data['title'] = "Jenis";
        $data['jenis'] = $this->admin->get('jenis');
        $this->template->load('templates/dashboard', 'jenis/data', $data);
    }
    // line 24-27 : membuat aturan validasi nama jenis yang dipanggil dari library
    private function _validasi()
    {
        $this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'required|trim');
    }
    // line 29-35 : membuat fungsi add untuk CRUD pada tabel jenis
    public function add()
    {
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Jenis";
            $this->template->load('templates/dashboard', 'jenis/add', $data);
        } else {
            //line 38-48 : menginputkan jenis barang
            $input = $this->input->post(null, true);
            $insert = $this->admin->insert('jenis', $input);
            if ($insert) {
                set_pesan('data berhasil disimpan');
                redirect('jenis');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('jenis/add');
            }
        }
    }
    // Line 50-70 membuat fungsi edit merupakan bagian CRUD dengan mendapatkan id pada data yang akan diedit
    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Jenis";
            $data['jenis'] = $this->admin->get('jenis', ['id_jenis' => $id]);
            $this->template->load('templates/dashboard', 'jenis/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $update = $this->admin->update('jenis', 'id_jenis', $id, $input);
            if ($update) {
                set_pesan('data berhasil disimpan');
                redirect('jenis');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('jenis/add');
            }
        }
    }
    // Line 74-84 : membuat fungsi delete merupakan bagian CRUD dengan mendapatkan id pada data yang akan dihapus
    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('jenis', 'id_jenis', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('jenis');
    }
}