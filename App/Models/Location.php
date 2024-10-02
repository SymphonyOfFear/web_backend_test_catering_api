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
     * Voeg een locatie toe.
     *
     * @param array $locationData
     * @return bool
     * @throws Exception
     */
    public function addLocation(array $locationData): bool
    {
        try {
            $query = "INSERT INTO locations (city, address, zip_code, country_code, phone_number) 
                      VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
            return $this
                ->db
                ->executeQuery($query, $locationData);
        } catch (Exception $e) {
            error_log('Fout in addLocation: ' . $e->getMessage());
            throw new Exception('Fout bij het toevoegen van de locatie: ' . $e->getMessage());
        }
    }

    /**
     * Haal alle locaties op.
     *
     * @return array
     * @throws Exception
     */
    public function getAllLocations(): array
    {
        try {
            $query = "SELECT * FROM locations";
            return $this
                ->db
                ->fetchAll($query);
        } catch (Exception $e) {
            error_log('Fout in getAllLocations: ' . $e->getMessage());
            throw new Exception('Fout bij het ophalen van alle locaties: ' . $e->getMessage());
        }
    }

    /**
     * Haal een locatie op met ID.
     *
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public function getLocationById(int $id): ?array
    {
        try {
            $query = "SELECT * FROM locations WHERE id = :id";
            return $this
                ->db
                ->fetchOne($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Fout in getLocationById: ' . $e->getMessage());
            throw new Exception('Fout bij het ophalen van de locatie met ID: ' . $e->getMessage());
        }
    }

    /**
     * Bewerk een locatie met ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function editLocation(int $id, array $data): bool
    {
        try {
            $query = "UPDATE locations SET city = :city, address = :address, zip_code = :zip_code, country_code = :country_code, phone_number = :phone_number WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, array_merge($data, ['id' => $id]));
        } catch (Exception $e) {
            error_log('Fout in editLocation: ' . $e->getMessage());
            throw new Exception('Fout bij het bewerken van de locatie: ' . $e->getMessage());
        }
    }

    /**
     * Verwijder een locatie met ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function removeLocation(int $id): bool
    {
        try {
            $query = "DELETE FROM locations WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Fout in removeLocation: ' . $e->getMessage());
            throw new Exception('Fout bij het verwijderen van de locatie: ' . $e->getMessage());
        }
    }
}
