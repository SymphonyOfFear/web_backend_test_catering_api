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

    public function addFacility(array $facilityData, array $locationData, array $tagIds): bool
    {
        try {
            $this
                ->db
                ->beginTransaction();

            // Create facility
            $facilityQuery = "INSERT INTO facilities (name, created_at) VALUES (:name, NOW())";
            $result = $this
                ->db
                ->executeQuery($facilityQuery, ['name' => $facilityData['name']]);

            if (!$result) {
                throw new Exception('Failed to create facility.');
            }

            $facilityId = $this
                ->db
                ->getLastInsertedId();

            $locationQuery = "INSERT INTO locations (city, address, zip_code, country_code, phone_number) 
                              VALUES (:city, :address, :zip_code, :country_code, :phone_number)";
            $this
                ->db
                ->executeQuery($locationQuery, $locationData);
            $locationId = $this
                ->db
                ->getLastInsertedId();

            $this->linkLocation($facilityId, $locationId);
            $this->linkTags($facilityId, $tagIds);

            $this
                ->db
                ->commit();
            return true;
        } catch (Exception $e) {
            $this
                ->db
                ->rollBack();
            error_log('Error in addFacility: ' . $e->getMessage());
            throw new Exception('Error adding facility: ' . $e->getMessage());
        }
    }

    public function listFacilities(): array
    {
        try {
            $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                             GROUP_CONCAT(t.name) as tags
                      FROM facilities f
                      LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                      LEFT JOIN locations l ON fl.location_id = l.id
                      LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                      LEFT JOIN tags t ON ft.tag_id = t.id
                      GROUP BY f.id";
            return $this
                ->db
                ->fetchAll($query);
        } catch (Exception $e) {
            error_log('Error in listFacilities: ' . $e->getMessage());
            throw new Exception('Error listing facilities: ' . $e->getMessage());
        }
    }

    public function getFacilityById(int $id): ?array
    {
        try {
            $query = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                             GROUP_CONCAT(t.name) as tags
                      FROM facilities f
                      LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                      LEFT JOIN locations l ON fl.location_id = l.id
                      LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                      LEFT JOIN tags t ON ft.tag_id = t.id
                      WHERE f.id = :id
                      GROUP BY f.id";
            return $this
                ->db
                ->fetchOne($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Error in getFacilityById: ' . $e->getMessage());
            throw new Exception('Error retrieving facility by ID: ' . $e->getMessage());
        }
    }

    public function editFacility(int $id, array $data): bool
    {
        try {
            $query = "UPDATE facilities SET name = :name WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, ['name' => $data['name'], 'id' => $id]);
        } catch (Exception $e) {
            error_log('Error in editFacility: ' . $e->getMessage());
            throw new Exception('Error editing facility: ' . $e->getMessage());
        }
    }

    public function removeFacility(int $id): bool
    {
        try {
            $query = "DELETE FROM facilities WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Error in removeFacility: ' . $e->getMessage());
            throw new Exception('Error removing facility: ' . $e->getMessage());
        }
    }

    public function searchFacilityByName(string $query): array
    {
        try {
            $sql = "SELECT f.id, f.name, f.created_at, l.city, l.address, l.zip_code, l.country_code, l.phone_number,
                           GROUP_CONCAT(t.name) as tags
                    FROM facilities f
                    LEFT JOIN facility_locations fl ON f.id = fl.facility_id
                    LEFT JOIN locations l ON fl.location_id = l.id
                    LEFT JOIN facility_tags ft ON f.id = ft.facility_id
                    LEFT JOIN tags t ON ft.tag_id = t.id
                    WHERE f.name LIKE :query OR l.city LIKE :query OR t.name LIKE :query
                    GROUP BY f.id";
            return $this
                ->db
                ->fetchAll($sql, ['query' => '%' . $query . '%']);
        } catch (Exception $e) {
            error_log('Error in searchFacilityByName: ' . $e->getMessage());
            throw new Exception('Error searching facilities by name: ' . $e->getMessage());
        }
    }

    protected function linkLocation(int $facilityId, int $locationId): bool
    {
        try {
            $query = "INSERT INTO facility_locations (facility_id, location_id) VALUES (:facility_id, :location_id)";
            return $this
                ->db
                ->executeQuery($query, ['facility_id' => $facilityId, 'location_id' => $locationId]);
        } catch (Exception $e) {
            error_log('Error in linkLocation: ' . $e->getMessage());
            throw new Exception('Error linking location: ' . $e->getMessage());
        }
    }

    protected function linkTags(int $facilityId, array $tagIds): bool
    {
        try {
            $queryCheckTag = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
            $queryInsert = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";

            foreach ($tagIds as $tagId) {
                $stmt = $this
                    ->db
                    ->getConnection()
                    ->prepare($queryCheckTag);
                $stmt->bindParam(':tag_id', $tagId, \PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $this
                        ->db
                        ->executeQuery($queryInsert, ['facility_id' => $facilityId, 'tag_id' => $tagId]);
                } else {
                    throw new Exception("Tag ID {$tagId} does not exist.");
                }
            }
            return true;
        } catch (Exception $e) {
            error_log('Error in linkTags: ' . $e->getMessage());
            throw new Exception('Error linking tags: ' . $e->getMessage());
        }
    }
}
