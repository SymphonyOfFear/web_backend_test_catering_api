<?php

namespace App\Models;

use App\Plugins\Db\Db;
use Exception;

class Facility
{
    protected Db $db;

    public function __construct()
    {
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    /**
     * Maak een nieuwe faciliteit aan met locatie en tags.
     */
    public function addFacility(array $facilityData, array $locationData, array $tagIds): bool
    {
        try {
            // Debug statement voor ingevoerde gegevens.
            var_dump($facilityData, $locationData, $tagIds);

            $this->db->beginTransaction();

            // Faciliteit aanmaken
            $facilityQuery = "INSERT INTO facilities (name, created_at) VALUES (:name, NOW())";
            $result = $this->db->executeQuery($facilityQuery, ['name' => $facilityData['name']]);

            if (!$result) {
                throw new Exception('Failed to create facility.');
            }

            // Haal het nieuw aangemaakte faciliteit-ID op
            $facilityId = $this->db->getLastInsertedId();

            // Locatie aanmaken
            $locationQuery = "INSERT INTO locations (city, address, zip_code, country_code, phone_number) 
                              VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
            $this->db->executeQuery($locationQuery, $locationData);
            $locationId = $this->db->getLastInsertedId();

            // Faciliteit koppelen aan locatie
            $this->linkLocation($facilityId, $locationId);

            // Faciliteit koppelen aan tags
            $this->linkTags($facilityId, $tagIds);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Haal alle faciliteiten op met hun gerelateerde details.
     */
    public function listFacilities(): array
    {
        $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                         GROUP_CONCAT(t.name) as tags
                  FROM facilities f
                  LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                  LEFT JOIN locations l ON fl.location_id = l.id
                  LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                  LEFT JOIN tags t ON ft.tag_id = t.id
                  GROUP BY f.id";

        // Debug statement voor de query.
        var_dump($query);

        return $this->db->fetchAll($query);
    }

    /**
     * Haal een faciliteit op via ID met details.
     */
    public function getFacilityById(int $id): ?array
    {
        $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                         GROUP_CONCAT(t.name) as tags
                  FROM facilities f
                  LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                  LEFT JOIN locations l ON fl.location_id = l.id
                  LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                  LEFT JOIN tags t ON ft.tag_id = t.id
                  WHERE f.id = :id
                  GROUP BY f.id";

        // Debug statement voor de query en parameters.
        var_dump($query, ['id' => $id]);

        return $this->db->fetchOne($query, ['id' => $id]);
    }

    /**
     * Zoek naar faciliteiten via query.
     */
    public function searchFacilityByName(string $query): array
    {
        $sql = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                   GROUP_CONCAT(t.name) as tags
            FROM facilities f
            LEFT JOIN facility_locations fl ON f.id = fl.facility_id
            LEFT JOIN locations l ON fl.location_id = l.id
            LEFT JOIN facility_tags ft ON f.id = ft.facility_id
            LEFT JOIN tags t ON ft.tag_id = t.id
            WHERE f.name LIKE :query OR l.city LIKE :query OR t.name LIKE :query
            GROUP BY f.id";

        try {
            // Debug statement verwijderen of van commentaar voorzien
            // var_dump($sql, ['query' => '%' . $query . '%']);

            // Voer de query uit met de parameter
            return $this->db->fetchAll($sql, ['query' => '%' . $query . '%']);
        } catch (\PDOException $e) {
            throw new \Exception('Query uitvoeren mislukt: ' . $e->getMessage());
        }
    }


    /**
     * Faciliteit updaten.
     */
    public function editFacility(int $id, array $data): bool
    {
        $query = "UPDATE facilities SET name = :name WHERE id = :id";

        // Debug statement voor de query en parameters.
        var_dump($query, ['name' => $data['name'], 'id' => $id]);

        return $this->db->executeQuery($query, ['name' => $data['name'], 'id' => $id]);
    }

    /**
     * Faciliteit verwijderen.
     */
    public function removeFacility(int $id): bool
    {
        $query = "DELETE FROM facilities WHERE id = :id";

        // Debug statement voor de query en parameters.
        var_dump($query, ['id' => $id]);

        return $this->db->executeQuery($query, ['id' => $id]);
    }

    /**
     * Faciliteit koppelen aan locatie.
     */
    protected function linkLocation(int $facilityId, int $locationId): bool
    {
        $query = "INSERT INTO facility_locations (facility_id, location_id) VALUES (:facility_id, :location_id)";

        // Debug statement voor de query en parameters.
        var_dump($query, ['facility_id' => $facilityId, 'location_id' => $locationId]);

        return $this->db->executeQuery($query, ['facility_id' => $facilityId, 'location_id' => $locationId]);
    }

    /**
     * Faciliteit koppelen aan meerdere tags.
     */
    protected function linkTags(int $facilityId, array $tagIds): bool
    {
        $queryCheckTag = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
        $queryInsert = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";

        foreach ($tagIds as $tagId) {
            // Debug statement voor elke tag.
            var_dump("Controleren of tag bestaat", ['tag_id' => $tagId]);

            // Controleer of de tag bestaat
            $stmt = $this->db->getConnection()->prepare($queryCheckTag);
            $stmt->bindParam(':tag_id', $tagId, \PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Debug statement voor tag koppeling.
                var_dump("Tag bestaat, koppelen aan faciliteit", ['facility_id' => $facilityId, 'tag_id' => $tagId]);

                // Als de tag bestaat, voer de insert uit
                $this->db->executeQuery($queryInsert, ['facility_id' => $facilityId, 'tag_id' => $tagId]);
            } else {
                throw new Exception("Tag ID {$tagId} bestaat niet.");
            }
        }
        return true;
    }
}
