<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;

class LoginController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        // 1. CORS Headers (Penting agar Flutter Web/Chrome lancar)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

        // Handle pre-flight request (OPTIONS)
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

        // 2. Ambil data (Mendukung Form-Data dan JSON)
        $email    = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Fallback jika Flutter mengirim Raw JSON
        if (empty($email)) {
            $json     = $this->request->getJSON();
            $email    = $json->email ?? null;
            $password = $json->password ?? null;
        }

        // 3. Validasi Input Dasar
        if (empty($email) || empty($password)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Email dan Password tidak boleh kosong'
            ], 400);
        }

        try {
            $db   = \Config\Database::connect();
            // Cari user berdasarkan email
            $user = $db->table('users')->where('email', $email)->get()->getRow();

            // 4. CEK APAKAH AKUN ADA
            if (!$user) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Akun tidak terdaftar di server'
                ], 404); // 404 Not Found
            }

            // 5. CEK APAKAH PASSWORD BENAR
            // Note: Saya pakai '==' karena kode Abang sebelumnya pakai plain text.
            // Jika nanti sudah pakai password_hash(), ganti jadi: !password_verify($password, $user->password)
            if ($user->password !== $password) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Password yang Anda masukkan salah'
                ], 401); // 401 Unauthorized
            }

            // 6. LOGIN BERHASIL
            return $this->respond([
                'status'  => 'success',
                'message' => 'Selamat datang kembali!',
                'user'    => [
                    'id'    => $user->id,
                    'email' => $user->email,
                    'name'  => $user->name
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Masalah teknis: ' . $e->getMessage()
            ], 500);
        }
    }
}