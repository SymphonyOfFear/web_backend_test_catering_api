<?php

namespace App\Models;

use App\Plugins\Db\Db;
use Exception;

class Location
{
    protected Db $db;

    public function __construct()
    {
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    /**
     * Locatie toevoegen.
     */
    public function addLocation(array $data): bool
    {
        $query = "INSERT INTO locations (city, address, zip_code, country_code, phone_number) 
                  VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
        return $this->db->executeQuery($query, $data);
    }

    /**
     * Alle locaties ophalen.
     */
    public function getAllLocations(): array
    {
        $query = "SELECT * FROM locations";
        return $this->db->fetchAll($query);
    }
}
