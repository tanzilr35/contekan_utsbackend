<?php
defined('BASEPATH') or exit('No direct script access allowed'); #untuk mencegah akses langsung ke file ini

//Gunakan CI_Controller karena berada di folder controllers
class Dashboard extends CI_Controller
{
    public function __construct() #untuk menjalankan fungsi diinginkan saat controller diakses dan mendeklarasikan variable yang akan sering digunakan
    {
        parent::__construct(); #parent berguna untuk memperbanyak deklarasi object yang diload, yang kemudian method constructnya dipanggil
        cek_login(); #buat mastiin klo user udh benar2 masuk/login #fungsi ceklogin itu dari fungsi_helper.php

        $this->load->model('Admin_model', 'admin'); #untuk meload model dari Admin_model.php --> diinisialisasi namanya jadi 'admin'
    }

    // Fungsi index dashboard disini akan menampilkan perubahan dlm bentuk grafik ketika kita nambahin stok barang
    public function index()
    {
        $data['title'] = "Dashboard";
        $data['barang'] = $this->admin->count('barang');
        $data['barang_masuk'] = $this->admin->count('barang_masuk');
        $data['barang_keluar'] = $this->admin->count('barang_keluar');
        $data['supplier'] = $this->admin->count('supplier');
        $data['user'] = $this->admin->count('user');
        $data['stok'] = $this->admin->sum('barang', 'stok');
        $data['barang_min'] = $this->admin->min('barang', 'stok', 30); #30 ini mksdnya untuk munculin stok barang yg ≤ 30, jd kalo ada stok brg yg > 30 maka tdk akan dimunculin
        $data['transaksi'] = [
            'barang_masuk' => $this->admin->getBarangMasuk(5),
            'barang_keluar' => $this->admin->getBarangKeluar(5)
        ];

        // Line Chart
        $bln = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $data['cbm'] = []; // chart barang masuk
        $data['cbk'] = []; // chart barang keluar

        foreach ($bln as $b) { // perulangan untuk ditampilkan cbm dan cbk secara realtime
            $data['cbm'][] = $this->admin->chartBarangMasuk($b); // fungsi ada di Admin_model.php
            $data['cbk'][] = $this->admin->chartBarangKeluar($b); // fungsi ada di Admin_model.php
        }

        // Akan meload ke menu dashboard
        $this->template->load('templates/dashboard', 'dashboard', $data);
    }
}
