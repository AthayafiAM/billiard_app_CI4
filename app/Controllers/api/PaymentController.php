<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class PaymentController extends ResourceController
{
    protected $format = 'json';

    // 🔥 SAVE PAYMENT METHOD
    public function save()
    {
        $db = \Config\Database::connect();

        $email = $this->request->getPost('user_email');
        $type  = $this->request->getPost('type');

        if (!$email || !$type) {
            return $this->respond([
                "status" => "error",
                "message" => "Missing data"
            ], 400);
        }

        // 🔥 HAPUS YANG LAMA (BIAR 1 USER 1 PAYMENT)
        $db->table('payment_methods')
            ->where('user_email', $email)
            ->delete();

        // 🔥 INSERT BARU
        $db->table('payment_methods')->insert([
            "user_email" => $email,
            "type" => $type,
        ]);

        return $this->respond([
            "status" => "success"
        ]);
    }

    // 🔥 GET PAYMENT USER
    public function get()
    {
        $db = \Config\Database::connect();

        $email = $this->request->getGet('user_email');

        $data = $db->table('payment_methods')
            ->where('user_email', $email)
            ->get()
            ->getRowArray();

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }
}