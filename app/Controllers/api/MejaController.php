<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class MejaController extends ResourceController
{
    protected $format = 'json';

    // ✅ GET TABLES (FILTER)
    public function index()
    {
        $db = \Config\Database::connect();

        $type = $this->request->getGet('type');
        $club_id = $this->request->getGet('club_id');

        $builder = $db->table('tables');

        if ($club_id) {
            $builder->where('club_id', $club_id);
        }

        if ($type) {
            $builder->where('type', $type);
        }

        $data = $builder->get()->getResultArray();

        return $this->respond([
            "status" => "success",
            "data" => $data
        ]);
    }

    // ➕ CREATE TABLE
    public function create()
    {
        $db = \Config\Database::connect();

        $data = [
            'club_id' => $this->request->getPost('club_id'),
            'table_name' => $this->request->getPost('table_name'),
            'type' => $this->request->getPost('type'),
            'price' => $this->request->getPost('price'),
            'status' => $this->request->getPost('status'),
        ];

        $db->table('tables')->insert($data);

        return $this->respond([
            "status" => "created"
        ]);
    }

    // ✏️ UPDATE TABLE
    public function update($id = null)
    {
        $db = \Config\Database::connect();

        $data = $this->request->getRawInput();

        $db->table('tables')->where('id', $id)->update($data);

        return $this->respond([
            "status" => "updated"
        ]);
    }

    // ❌ DELETE TABLE
    public function delete($id = null)
    {
        $db = \Config\Database::connect();

        $db->table('tables')->delete(['id' => $id]);

        return $this->respond([
            "status" => "deleted"
        ]);
    }
}