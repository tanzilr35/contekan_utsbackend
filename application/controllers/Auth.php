<?php
defined('BASEPATH') or exit('No direct script access allowed'); #untuk mencegah akses langsung ke file ini

//Gunakan CI_Controller karena berada di folder controllers
class Auth extends CI_Controller #nama class harus diawali dengan huruf besar dan sesuai dengan nama file controller
{
    public function __construct() #untuk menjalankan fungsi diinginkan saat controller diakses dan mendeklarasikan variable yang akan sering digunakan
    {
        parent::__construct(); #parent berguna untuk memperbanyak deklarasi object yang diload, yang kemudian method constructnya dipanggil
        $this->load->library('form_validation'); #untuk meload form_validation dari library
        $this->load->model('Auth_model', 'auth'); #untuk meload model dari Auth_model.php --> diinisialisasi namanya jadi 'auth'
        $this->load->model('Admin_model', 'admin'); #untuk meload model dari Admin_model.php --> diinisialisasi namanya jadi 'admin'
    }
    
    private function _has_login() #method _has_login ini bagian dari fungsi index di line 26
    //Private function berguna agar si _has_login tidak dapat dieksekusi diluar objek tsb
    {
        //Mengecek jika data user sudah terkonfirm atau blm
        if ($this->session->has_userdata('login_session')) { #fungsi has_userdata ada di libraries/Session/session.php di line 849
            redirect('dashboard'); #jika user ternyata sudah dikonfirm oleh admin & sdh masuk database, maka user akan diarahkan menuju halaman dashboard
        }
    }

    public function index() #method index auth untuk login
    {
        $this->_has_login(); #memanggil private fungsi _has_login di line 15

        // required artinya wajib // form_validation berasal dari line 19
        // trim disini berfungsi untuk menghapus semua spasi dari teks
        $this->form_validation->set_rules('username', 'Username', 'required|trim'); #aturan saat kita input username dengan id=”username” = wajid diisi, jika tidak di isi maka akan tampil alert “Username field is required”
        $this->form_validation->set_rules('password', 'Password', 'required|trim'); #aturan saat kita input password dengan id=”password” = wajid diisi, jika tidak di isi maka akan penampilka notifikasi “Password field is required”

        if ($this->form_validation->run() == false) { //jika validasi gagal akan di load ke halaman login
            $data['title'] = 'Login Aplikasi'; // Judul pada tab websitenya
            $this->template->load('templates/auth', 'auth/login', $data); //untuk menghubungkan ke views/auth/login.php
        } else { //jika berhasil maka:
            $input = $this->input->post(null, true);

            // akan dicek usernamenya apa sudah benar atau benar
            $cek_username = $this->auth->cek_username($input['username']); //function cek_username ada di models/Auth_model.php
            if ($cek_username > 0) {  // jika ada username yang valid tdk ada yg sama akan diinputkan ke tabel user database, jd usernamenya blm ada yg pakai
                $password = $this->auth->get_password($input['username']); //function get_password ada di models/Auth_model.php
                if (password_verify($input['password'], $password)) { //verifikasi password setelah input/ngetik pass
                    $user_db = $this->auth->userdata($input['username']); // maka akan dimasukkan ke database user
                    if ($user_db['is_active'] != 1) { //jika user aktif tidak sama dengan 1 = jika data user blm di aktifin
                        set_pesan('akun anda belum aktif/dinonaktifkan. Silahkan hubungi admin.', false);
                        redirect('login');
                    } else { //jika true, maka admin akan mengaktifkan user dan bisa mengubah role-nya
                        $userdata = [
                            'user'  => $user_db['id_user'],
                            'role'  => $user_db['role'],
                            'timestamp' => time() // untuk menampilkan waktu dibuatnya user pada database
                        ];
                        $this->session->set_userdata('login_session', $userdata); //jika user sudah aktif barulah bisa masuk ke dasboard
                        redirect('dashboard');
                    }
                } else { //misal kalo passnya salah, maka akan dikembalikan ke auth/halaman login lg
                    set_pesan('password salah', false);
                    redirect('auth');
                }
            } else { //misal jika belum daftar
                set_pesan('username belum terdaftar', false);
                redirect('auth');
            }
        }
    }

    public function logout() // merupakan pendeklarasian function logout dalam class
    {
        $this->session->unset_userdata('login_session'); // function unset_userdata ada di libraries/Session/Session.php di line 810

        set_pesan('anda telah berhasil logout');
        redirect('auth');
    }

    public function register()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]|alpha_numeric');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]|trim');
        $this->form_validation->set_rules('password2', 'Konfirmasi Password', 'matches[password]|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('no_telp', 'Nomor Telepon', 'required|trim');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Buat Akun';
            $this->template->load('templates/auth', 'auth/register', $data);
        } else {
            $input = $this->input->post(null, true);
            unset($input['password2']);
            // password hash berfungsi untuk menggenerate password yang kuat (contoh: "Suggest strong password ...")
            $input['password']      = password_hash($input['password'], PASSWORD_DEFAULT);
            $input['role']          = 'gudang'; //otomatis jadi role gudang, bisa diubah melalui admin manager
            $input['foto']          = 'user.png';
            $input['is_active']     = 0; //untuk mengaktivasi user
            $input['created_at']    = time();

            $query = $this->admin->insert('user', $input); // data user akan diinput ke database
            if ($query) {
                set_pesan('daftar berhasil. Selanjutnya silahkan hubungi admin untuk mengaktifkan akun anda.');
                redirect('login');
            } else {
                set_pesan('gagal menyimpan ke database', false);
                redirect('register');
            }
        }
    }
}
