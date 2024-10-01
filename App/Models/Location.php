<?php

namespace App\Models;

use App\Plugins\Db\Db;

class Location
{
    protected Db $db;

    public function __construct()
    {
        // Provide database connection parameters directly
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO locations (city, address, zip_code, country_code, phone_number)
                  VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
        return $this->db->executeQuery($query, [
            'city' => $data['city'],
            'address' => $data['address'],
            'zip_code' => $data['zip_code'],
            'country_code' => $data['country_code'],
            'phone_number' => $data['phone_number']
        ]);
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM locations";
        return $this->db->fetchAll($query);
    }
}
