<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BookingController extends ResourceController
{
    protected $format = 'json';

    // ✅ GET ALL BOOKINGS
    public function index()
    {
        $db = \Config\Database::connect();
        $data = $db->table('bookings')->get()->getResultArray();

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }

    // ➕ CREATE BOOKING
    public function create()
    {
        $db = \Config\Database::connect();

        $data = [
            'table_name' => $this->request->getPost('table_name'),
            'date' => $this->request->getPost('date'),
            'start_time' => $this->request->getPost('start_time'),
            'duration' => $this->request->getPost('duration'),
        ];

        $db->table('bookings')->insert($data);

        return $this->respond([
            "status" => "booked"
        ]);
    }

    // ❌ DELETE BOOKING
    public function delete($id = null)
    {
        $db = \Config\Database::connect();

        $db->table('bookings')->delete(['id' => $id]);

        return $this->respond([
            "status" => "deleted"
        ]);
    }

    // 🔥 GET BLOCKED TIMES (INI KUNCI UTAMA)
    public function getBlockedTimes()
    {
        $table = $this->request->getGet('table_name');
        $date = $this->request->getGet('date');

        $db = \Config\Database::connect();

        $bookings = $db->table('bookings')
            ->where('table_name', $table)
            ->where('date', $date)
            ->get()
            ->getResultArray();

        $blocked = [];

        foreach ($bookings as $b) {
            $start = strtotime($b['start_time']);
            $duration = (int)$b['duration'];

            for ($i = 0; $i < $duration; $i++) {
                $blocked[] = date("H:i", strtotime("+$i hour", $start));
            }
        }

        return $this->respond([
            "status" => "success",
            "blocked_times" => $blocked
        ]);
    }
}