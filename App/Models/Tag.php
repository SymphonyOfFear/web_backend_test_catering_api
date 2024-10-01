<?php

namespace App\Models;

use App\Plugins\Db\Db;

class Tag
{
    protected Db $db;

    public function __construct()
    {
        // Provide database connection parameters directly
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO tags (name) VALUES (:name)";
        return $this->db->executeQuery($query, ['name' => $data['name']]);
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM tags";
        return $this->db->fetchAll($query);
    }
}
