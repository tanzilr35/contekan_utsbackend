<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Line 5-  = Kelas Profile sebagai menu profil admin atau staf gudang
class Profile extends CI_Controller
{
    protected $user;

    // Line 10-21 = Membuat fungsi construct untuk mendeklarasikan variabel/objek yang sering digunakan
    public function __construct()
    {
        // line 12-20 = Untuk memperbanyak deklarasi load
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');

        $userId = $this->session->userdata('login_session')['user'];
        $this->user = $this->admin->get('user', ['id_user' => $userId]);
    }

    // Line 21-26 = Membuat fungsi index untuk menu profile serta pemanggilan data
    public function index()
    {
        $data['title'] = "Profile";
        $data['user'] = $this->user;
        $this->template->load('templates/dashboard', 'profile/user', $data);
    }

    // Line 28-27 = Fungsi private validasi untuk membuat aturan saat mengubah data profil user
    private function _validasi()
    {
        $db = $this->admin->get('user', ['id_user' => $this->input->post('id_user', true)]);
        $username = $this->input->post('username', true);
        $email = $this->input->post('email', true);

        $uniq_username = $db['username'] == $username ? '' : '|is_unique[user.username]';
        $uniq_email = $db['email'] == $email ? '' : '|is_unique[user.email]';

        $this->form_validation->set_rules('username', 'Username', 'required|trim|alpha_numeric' . $uniq_username);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $uniq_email);
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('no_telp', 'Nomor Telepon', 'required|trim|numeric');
    }

    // Line 48-56 = Fungsi private config untuk konfigurasi ketentuan saat upload foto profil
    private function _config()
    {
        $config['upload_path']      = "./assets/img/avatar";
        $config['allowed_types']    = 'gif|jpg|jpeg|png';
        $config['encrypt_name']     = TRUE;
        $config['max_size']         = '2048';

        $this->load->library('upload', $config);
    }

    // Line 59-102 = Fungsi utama settings untuk mengedit data profil, yg memuat fungsi validasi dan config
    public function setting()
    {
        $this->_validasi();
        $this->_config();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Profile";
            $data['user'] = $this->user;
            $this->template->load('templates/dashboard', 'profile/setting', $data);
        } else {
            $input = $this->input->post(null, true);
            if (empty($_FILES['foto']['name'])) {
                $insert = $this->admin->update('user', 'id_user', $input['id_user'], $input);
                if ($insert) {
                    set_pesan('perubahan berhasil disimpan.');
                } else {
                    set_pesan('perubahan tidak disimpan.');
                }
                redirect('profile/setting');
            } else {
                if ($this->upload->do_upload('foto') == false) {
                    echo $this->upload->display_errors();
                    die;
                } else {
                    if (userdata('foto') != 'user.png') {
                        $old_image = FCPATH . 'assets/img/avatar/' . userdata('foto');
                        if (!unlink($old_image)) {
                            set_pesan('gagal hapus foto lama.');
                            redirect('profile/setting');
                        }
                    }

                    $input['foto'] = $this->upload->data('file_name');
                    $update = $this->admin->update('user', 'id_user', $input['id_user'], $input);
                    if ($update) {
                        set_pesan('perubahan berhasil disimpan.');
                    } else {
                        set_pesan('gagal menyimpan perubahan');
                    }
                    redirect('profile/setting');
                }
            }
        }
    }

    // Line 105-130 = Membuat fungsi ubahpassword
    public function ubahpassword()
    {
        // Line 108-110 = Aturan saat mengubah password
        $this->form_validation->set_rules('password_lama', 'Password Lama', 'required|trim');
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|trim|min_length[3]|differs[password_lama]');
        $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'matches[password_baru]');

        if ($this->form_validation->run() == false) {
            $data['title'] = "Ubah Password";
            $this->template->load('templates/dashboard', 'profile/ubahpassword', $data);
        } else {
            $input = $this->input->post(null, true);
            if (password_verify($input['password_lama'], userdata('password'))) {
                $new_pass = ['password' => password_hash($input['password_baru'], PASSWORD_DEFAULT)];
                $query = $this->admin->update('user', 'id_user', userdata('id_user'), $new_pass);

                if ($query) {
                    set_pesan('password berhasil diubah.');
                } else {
                    set_pesan('gagal ubah password', false);
                }
            } else {
                set_pesan('password lama salah.', false);
            }
            redirect('profile/ubahpassword');
        }
    }
}
