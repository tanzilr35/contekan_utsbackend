<?php
defined('BASEPATH') or exit('No direct script access allowed'); // untuk mencegah akses langsung ke file controller

class Barang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login(); // fungsi cek_login didefinisikan dari fungsi_helper.php di folder helpers
                      // untuk memastikan kalau user benar-benar sudah login
        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Barang";
        $data['barang'] = $this->admin->getBarang();
        $this->template->load('templates/dashboard', 'barang/data', $data);
    }
     //membuat aturan validasi nama barang, jenis dan satuan yang dipanggil dari library
    private function _validasi() // bagian dari fungsi add
    {
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('jenis_id', 'Jenis Barang', 'required');
        $this->form_validation->set_rules('satuan_id', 'Satuan Barang', 'required');
    }

    public function add() // CRUD fungsi tambah
    {
        $this->_validasi();

        if ($this->form_validation->run() == false) { // jika validasi salah maka akan diarahkan kembali di line 49
            $data['title'] = "Barang";
            $data['jenis'] = $this->admin->get('jenis'); 
            $data['satuan'] = $this->admin->get('satuan');

            // Mengenerate ID Barang
            $kode_terakhir = $this->admin->getMax('barang', 'id_barang');
            $kode_tambah = substr($kode_terakhir, -6, 6); // jadi -6 itu posisi dari kanan yaitu "0" setelah B, krn di database charnya di Id_barang di kasih 7 doang
                                                          // jadi 6 itu posisi dari kiri yaitu "1", dan akan bertambah seiring bertambahnya data barang yang diinputkan
            $kode_tambah++; // angka terakhir pada data  barang yang bertambah seiring bertambahnya data barang.
            $number = str_pad($kode_tambah, 6, '0', STR_PAD_LEFT); // id tambahan dimulai dari kiri // "6" adalah panjang // '0' adalah spasi
            // jadi 0 itu adalah penambahan spasi di bagian karakter yg kosong,
            // misal ada kata "APA" karena panjangnya 6, maka kata "APA" ini akan ditambahkan spasi "0" dari arah kiri, menjadi = "000APA"
            $data['id_barang'] = 'B' . $number; // karakter awal id barang = B000001
            
            // sidebar --> menu barang --> tambah
            $this->template->load('templates/dashboard', 'barang/add', $data); // jadi user akan mengload ke form add
            $input = $this->input->post(null, true); // setelah di input akan dipost // return array
            $insert = $this->admin->insert('barang', $input); // untuk penambahan data

            if ($insert) {
                set_pesan('data berhasil disimpan'); // pesan yang muncul ketika barang berhasil diinput
                redirect('barang'); // jika barang berhasil ditambahkan maka akan diarahkan ke tabel data barang
            } else {
                set_pesan('gagal menyimpan data');
                redirect('barang/add'); // jika data gagal diinputkan akan diarahkan kembali ke halaman tambah barang
            }
        }
    }

    public function edit($getId) // CRUD fungsi edit yang menginisialisasi parameter getId
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) { // jika validasi salah maka akan diarahkan kembali di line 73
            $data['title'] = "Barang";
            $data['jenis'] = $this->admin->get('jenis');
            $data['satuan'] = $this->admin->get('satuan');
            $data['barang'] = $this->admin->get('barang', ['id_barang' => $id]);
            $this->template->load('templates/dashboard', 'barang/edit', $data); // jadi user akan mengload ke form edit
        } else { // jika validasi true, maka
            $input = $this->input->post(null, true); // akan dipost
            $update = $this->admin->update('barang', 'id_barang', $id, $input); // akan berhasil terupdate data baru

            if ($update) {
                set_pesan('data berhasil disimpan');
                redirect('barang'); //diarahkan ke halaman barang
            } else {
                set_pesan('gagal menyimpan data');
                redirect('barang/edit/' . $id); // diarahkan kembali ke halaman edit
            }
        }
    }

    public function delete($getId) // CRUD fungsi hapus yang menginisialisasi parameter getId
    {
        $id = encode_php_tags($getId); // encode_php_tags adalah fungsi keamanan yang mengubah tag PHP menjadi entitas
        if ($this->admin->delete('barang', 'id_barang', $id)) { // penghapusan data barang diambil berdasarkan id barang
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('barang'); //diarahkan ke halaman barang
    }

    public function getstok($getId) // fungsi penambahan stok barang, yang akan dihubungkan ke views/template/dashboard di line 357
    {
        $id = encode_php_tags($getId);
        $query = $this->admin->cekStok($id); // mengakses fungsi cekStok yang ada di models/Admin_model.php
        output_json($query); // mengeluarkan data ke grafik
    }
}
