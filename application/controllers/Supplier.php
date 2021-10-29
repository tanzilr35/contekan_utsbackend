<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends CI_Controller
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
    // line 17-22 : membuat fungsi index untuk menu supplier serta pemanggilan data
    public function index()
    {
        $data['title'] = "Supplier";
        $data['supplier'] = $this->admin->get('supplier');
        $this->template->load('templates/dashboard', 'supplier/data', $data);
    }
    // line 24-27 : membuat aturan validasi nama supllier, no_telp dan alamat yang dipanggil dari library
    private function _validasi()
    {
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required|trim');
        $this->form_validation->set_rules('no_telp', 'Nomor Telepon', 'required|trim|numeric');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
    }
    // line 31-36 : membuat fungsi add untuk CRUD pada tabel supplier
    public function add()
    {
        $this->_validasi();
        if ($this->form_validation->run() == false) {
            $data['title'] = "Supplier";
            $this->template->load('templates/dashboard', 'supplier/add', $data);
        } else {
            //line 38-48 : menginputkan supplier
            $input = $this->input->post(null, true);
            $save = $this->admin->insert('supplier', $input);
            if ($save) {
                set_pesan('data berhasil disimpan.');
                redirect('supplier');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('supplier/add');
            }
        }
    }

    // Line 52-73 membuat fungsi edit merupakan bagian CRUD dengan mendapatkan id pada data yang akan diedit
    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Supplier";
            $data['supplier'] = $this->admin->get('supplier', ['id_supplier' => $id]);
            $this->template->load('templates/dashboard', 'supplier/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $update = $this->admin->update('supplier', 'id_supplier', $id, $input);

            if ($update) {
                set_pesan('data berhasil diedit.');
                redirect('supplier');
            } else {
                set_pesan('data gagal diedit.');
                redirect('supplier/edit/' . $id);
            }
        }
    }
    // Line 75-85 : membuat fungsi delete merupakan bagian CRUD dengan mendapatkan id pada data yang akan dihapus
    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('supplier', 'id_supplier', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('supplier');
    }
}
