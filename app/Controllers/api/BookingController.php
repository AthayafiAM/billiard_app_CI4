<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BookingController extends ResourceController
{
    protected $format = 'json';

    // ✅ GET BOOKINGS (PRIVATE PER USER)
    public function index()
    {
        $db = \Config\Database::connect();

        // 🔥 AMBIL EMAIL DARI QUERY (?user_email=...)
        $user_email = $this->request->getGet('user_email');

        $builder = $db->table('bookings');

        // 🔐 FILTER AGAR HANYA DATA USER INI
        if (!empty($user_email)) {
            $builder->where('user_email', $user_email);
        }

        $data = $builder->get()->getResultArray();

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }

    // ➕ CREATE BOOKING (FIX BENTROK + USER)
    public function create()
    {
        $db = \Config\Database::connect();

        // 🔥 AMBIL DATA
        $table_name = $this->request->getPost('table_name');
        $date       = $this->request->getPost('date');
        $start_time = $this->request->getPost('start_time');
        $duration   = $this->request->getPost('duration');

        $club_id    = $this->request->getPost('club_id');
        $club_name  = $this->request->getPost('club');
        
        $username   = $this->request->getPost('user_name'); 
        $user_email = $this->request->getPost('user_email');
        $user_image = $this->request->getPost('user_image');

        // 🔥 VALIDASI
        if (!$table_name || !$date || !$start_time || !$duration || !$club_id) {
            return $this->respond([
                "status" => "error",
                "message" => "Missing required fields"
            ], 400);
        }

        // 🔥 CEK BENTROK (FIX: TAMBAH CLUB_ID)
        $existing = $db->table('bookings')
            ->where('table_name', $table_name)
            ->where('club_id', $club_id)
            ->where('date', $date)
            ->get()
            ->getResultArray();

        foreach ($existing as $b) {
            $start = strtotime($b['start_time']);
            $end   = strtotime("+{$b['duration']} hour", $start);

            $newStart = strtotime($start_time);
            $newEnd   = strtotime("+{$duration} hour", $newStart);

            if ($newStart < $end && $newEnd > $start) {
                return $this->respond([
                    "status" => "error",
                    "message" => "Time slot already booked"
                ], 409);
            }
        }

        // 🔥 INSERT
        $data = [
            'table_name' => $table_name,
            'club_id'    => $club_id,
            'club'       => $club_name,
            'user_name'  => $username,
            'user_email' => $user_email,
            'user_image' => $user_image,
            'date'       => $date,
            'start_time' => $start_time,
            'duration'   => $duration,
        ];

        $db->table('bookings')->insert($data);

        return $this->respond([
            "status" => "booked"
        ]);
    }

    // ❌ DELETE
    public function delete($id = null)
    {
        $db = \Config\Database::connect();

        $db->table('bookings')->delete(['id' => $id]);

        return $this->respond([
            "status" => "deleted"
        ]);
    }

    // 🔥 GET BLOCKED TIMES (FIX CLUB_ID)
    public function getBlockedTimes()
    {
        $table   = $this->request->getGet('table_name');
        $date    = $this->request->getGet('date');
        $club_id = $this->request->getGet('club_id');

        $db = \Config\Database::connect();

        $bookings = $db->table('bookings')
            ->where('table_name', $table)
            ->where('club_id', $club_id)
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