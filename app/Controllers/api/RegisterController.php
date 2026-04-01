<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;

class RegisterController extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: POST, OPTIONS");

        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

        $nama     = $this->request->getVar('name');
        $email    = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        if (empty($nama) || empty($email) || empty($password)) {
            return $this->respond(['status' => 'error', 'message' => 'Semua data harus diisi'], 400);
        }

        $db = \Config\Database::connect();
        
        // Cek apakah email sudah terdaftar
        $cek = $db->table('users')->where('email', $email)->get()->getRow();
        if ($cek) {
            return $this->respond(['status' => 'error', 'message' => 'Email sudah digunakan'], 400);
        }

        // Simpan ke database (Sesuai gambar database Abang: email, name, password)
        $data = [
            'name'     => $nama,
            'email'    => $email,
            'password' => $password // Disarankan pakai password_hash() nanti
        ];

        if ($db->table('users')->insert($data)) {
            return $this->respond(['status' => 'success', 'message' => 'Akun berhasil dibuat'], 200);
        }

        return $this->respond(['status' => 'error', 'message' => 'Gagal membuat akun'], 500);
    }
}