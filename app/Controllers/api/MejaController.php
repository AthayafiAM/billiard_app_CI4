<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;

class MejaController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        // PENTING: Supaya Flutter Chrome tidak error CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

        $data = [
            ['id' => 1, 'nama' => 'Meja VIP', 'status' => 'Tersedia'],
            ['id' => 2, 'nama' => 'Meja Biasa', 'status' => 'Penuh'],
        ];

        return $this->respond($data);
    }
}