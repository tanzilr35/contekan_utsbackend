<?php

function cek_login()
{
    //Tetapkan codeigniter (ci) sebagai objek jd variabel
    //Agar kelas2 yg diinstansi dgn controller method dapat mengakses sumber daya asli CI, maka tgl gunain get_instance
    $ci = get_instance(); #jd si $ci ini pengganti $this
    if (!$ci->session->has_userdata('login_session')) { #untuk mengecek user apakah sdh login/data user sdh terdaftar & terkonfirm atau blm
        set_pesan('silahkan login.');
        redirect('auth'); #jika ternyata data user blm dikonfirm maka akan diarahkan ke auth atau halaman login
    }
}

function is_admin()
{
    $ci = get_instance();
    $role = $ci->session->userdata('login_session')['role'];

    $status = true;

    if ($role != 'admin') {
        $status = false;
    }

    return $status;
}

function set_pesan($pesan, $tipe = true)
{
    $ci = get_instance();
    if ($tipe) {
        $ci->session->set_flashdata('pesan', "<div class='alert alert-success'><strong>SUCCESS!</strong> {$pesan} <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
    } else {
        $ci->session->set_flashdata('pesan', "<div class='alert alert-danger'><strong>ERROR!</strong> {$pesan} <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
    }
}

function userdata($field)
{
    $ci = get_instance();
    $ci->load->model('Admin_model', 'admin');

    $userId = $ci->session->userdata('login_session')['user'];
    return $ci->admin->get('user', ['id_user' => $userId])[$field];
}

function output_json($data)
{
    $ci = get_instance();
    $data = json_encode($data);
    $ci->output->set_content_type('application/json')->set_output($data);
}
