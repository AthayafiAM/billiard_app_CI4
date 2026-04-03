<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ClubController extends ResourceController
{
    protected $format = 'json';

    // ✅ GET ALL CLUBS
    public function index()
    {
        $db = \Config\Database::connect();

        $data = $db->table('clubs')->get()->getResultArray();

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }

    // ➕ CREATE CLUB
    public function create()
    {
        $db = \Config\Database::connect();

        $data = [
            'name' => $this->request->getPost('name'),
            'location' => $this->request->getPost('location'),
            'description' => $this->request->getPost('description'),
            'image' => $this->request->getPost('image'),
            'rating' => $this->request->getPost('rating'),
        ];

        $db->table('clubs')->insert($data);

        return $this->respond([
            "status" => "created"
        ]);
    }

    // ✏️ UPDATE CLUB
    public function update($id = null)
    {
        $db = \Config\Database::connect();

        $data = $this->request->getRawInput();

        $db->table('clubs')->where('id', $id)->update($data);

        return $this->respond([
            "status" => "updated"
        ]);
    }

    // ❌ DELETE CLUB
    public function delete($id = null)
    {
        $db = \Config\Database::connect();

        $db->table('clubs')->delete(['id' => $id]);

        return $this->respond([
            "status" => "deleted"
        ]);
    }
}