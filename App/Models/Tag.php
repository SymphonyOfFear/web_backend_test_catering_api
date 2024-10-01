<?php

namespace App\Models;

use App\Plugins\Db\Db;
use Exception;

class Tag
{
    protected Db $db;

    public function __construct()
    {
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    /**
     * Tag toevoegen.
     */
    public function addTag(array $data): bool
    {
        $query = "INSERT INTO tags (name) VALUES (:name)";
        return $this->db->executeQuery($query, $data);
    }

    /**
     * Alle tags ophalen.
     */
    public function getAllTags(): array
    {
        $query = "SELECT * FROM tags";
        return $this->db->fetchAll($query);
    }
}
