<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class UpdateProfileController extends ResourceController
{
    protected $format = 'json';

    public function updateProfile()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        $this->response->setHeader('Content-Type', 'application/json');

        if (strtolower($this->request->getMethod()) === 'options') {
            return $this->response->setStatusCode(200);
        }

        $db = \Config\Database::connect();

        $email = trim($this->request->getPost('email')); 
        $name  = $this->request->getPost('name');
        $file  = $this->request->getFile('image');

        log_message('error', 'UPDATE EMAIL: ' . $email);

        if (empty($email)) {
            return $this->respond([
                'status' => 'error', 
                'message' => 'Email identifier tidak ditemukan'
            ], 400);
        }

        $user = $db->table('users')->where('email', $email)->get()->getRow();

        if (!$user) {
            return $this->respond([
                'status' => 'error', 
                'message' => 'User tidak ditemukan',
                'debug_email' => $email
            ], 404);
        }

        $updateData = [
            'name' => $name
        ];

        // 🔥 FIX UPLOAD
        if ($file && $file->isValid()) {
            $uploadPath = FCPATH . 'uploads/profile';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // hapus lama
            if (!empty($user->profile_picture) && file_exists($uploadPath . '/' . $user->profile_picture)) {
                @unlink($uploadPath . '/' . $user->profile_picture);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $updateData['profile_picture'] = $newName;
        }

        try {
            $db->table('users')->where('email', $email)->update($updateData);

            // 🔥 FIX: FULL URL
            $baseUrl = base_url('uploads/profile/');

            $finalImage = isset($updateData['profile_picture'])
                ? $baseUrl . $updateData['profile_picture']
                : (!empty($user->profile_picture)
                    ? $baseUrl . $user->profile_picture
                    : null);

            return $this->respond([
                'status' => 'success', 
                'message' => 'Update Berhasil!',
                'data' => [
                    'updated_name' => $name,
                    'current_email' => $email,
                    'profile_picture' => $finalImage
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error', 
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔥 GET PROFILE
    public function getProfile()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $email = trim($this->request->getGet('email'));

        $db = \Config\Database::connect();

        $user = $db->table('users')
            ->where('email', $email)
            ->get()
            ->getRow();

        if (!$user) {
            return $this->respond([
                'status' => 'error',
                'message' => 'User tidak ditemukan',
                'debug_email' => $email
            ], 404);
        }

        // 🔥 FIX: FULL URL
        $baseUrl = base_url('uploads/profile/');

        $imageUrl = !empty($user->profile_picture)
            ? $baseUrl . $user->profile_picture
            : null;

        return $this->respond([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $imageUrl
            ]
        ], 200);
    }
}