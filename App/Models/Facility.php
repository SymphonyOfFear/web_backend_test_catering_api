<?php

namespace App\Models;

use PDO;
use App\Plugins\Db\Db;

class Facility
{
    protected Db $db;

    public function __construct()
    {
        $this->db = new Db('localhost', 'dtt_assessment', 'root', '');
    }

    public function createFacilityWithDetails(array $facilityData, array $locationData, array $tagIds): bool
    {
        $this->db->beginTransaction();

        $query = "INSERT INTO facilities (name, created_at) VALUES (:name, NOW())";
        $result = $this->db->executeQuery($query, ['name' => $facilityData['name']]);

        if ($result) {
            $facilityId = $this->db->getLastInsertedId();

            $query = "INSERT INTO locations (city, address, zip_code, country_code, phone_number) 
                      VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
            $this->db->executeQuery($query, $locationData);
            $locationId = $this->db->getLastInsertedId();

            $this->linkLocation($facilityId, $locationId);
            $this->linkTags($facilityId, $tagIds);

            $this->db->commit();
            return true;
        }

        $this->db->rollBack();
        return false;
    }

    public function getAllFacilities(): array
    {
        $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                         GROUP_CONCAT(t.name) AS tags
                  FROM facilities f
                  LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                  LEFT JOIN locations l ON fl.location_id = l.id
                  LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                  LEFT JOIN tags t ON ft.tag_id = t.id
                  GROUP BY f.id";
        return $this->db->fetchAll($query);
    }

    public function getFacilityWithDetails(int $id): ?array
    {
        $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                         GROUP_CONCAT(t.name) AS tags
                  FROM facilities f
                  LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                  LEFT JOIN locations l ON fl.location_id = l.id
                  LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                  LEFT JOIN tags t ON ft.tag_id = t.id
                  WHERE f.id = :id
                  GROUP BY f.id";
        return $this->db->fetchOne($query, ['id' => $id]);
    }

    public function updateFacility(int $id, array $data): bool
    {
        $query = "UPDATE facilities SET name = :name WHERE id = :id";
        return $this->db->executeQuery($query, ['name' => $data['name'], 'id' => $id]);
    }

    public function deleteFacility(int $id): bool
    {
        $query = "DELETE FROM facilities WHERE id = :id";
        return $this->db->executeQuery($query, ['id' => $id]);
    }

    public function searchFacilities(string $query): array
    {
        $sql = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                       GROUP_CONCAT(t.name) AS tags
                FROM facilities f
                LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                LEFT JOIN locations l ON fl.location_id = l.id
                LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                LEFT JOIN tags t ON ft.tag_id = t.id
                WHERE f.name LIKE :query OR l.city LIKE :query OR t.name LIKE :query
                GROUP BY f.id";
        return $this->db->fetchAll($sql, ['query' => '%' . $query . '%']);
    }

    protected function linkLocation(int $facilityId, int $locationId): bool
    {
        $query = "INSERT INTO facility_locations (facility_id, location_id) VALUES (:facility_id, :location_id)";
        return $this->db->executeQuery($query, ['facility_id' => $facilityId, 'location_id' => $locationId]);
    }

    protected function linkTags(int $facilityId, array $tagIds): bool
    {
        $queryCheckTag = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
        $queryInsert = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";

        foreach ($tagIds as $tagId) {
            echo "Processing tag ID: " . $tagId . "<br>";

            $stmt = $this->db->getConnection()->prepare($queryCheckTag);
            $stmt->bindParam(':tag_id', $tagId, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $this->db->executeQuery($queryInsert, [
                    'facility_id' => $facilityId,
                    'tag_id' => $tagId
                ]);
                echo "Tag ID {$tagId} linked successfully.<br>";
            } else {
                echo "Tag ID {$tagId} does not exist.<br>";
                throw new \Exception("Tag ID {$tagId} does not exist.");
            }
        }
        return true;
    }
}
