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
     * Voeg een tag toe.
     *
     * @param array $tagData
     * @return bool
     * @throws Exception
     */
    public function addTag(array $tagData): bool
    {
        try {
            $query = "INSERT INTO tags (name) VALUES (:name)";
            return $this
                ->db
                ->executeQuery($query, $tagData);
        } catch (Exception $e) {
            error_log('Fout in addTag: ' . $e->getMessage());
            throw new Exception('Fout bij het toevoegen van de tag: ' . $e->getMessage());
        }
    }

    /**
     * Haal alle tags op.
     *
     * @return array
     * @throws Exception
     */
    public function getAllTags(): array
    {
        try {
            $query = "SELECT * FROM tags";
            return $this
                ->db
                ->fetchAll($query);
        } catch (Exception $e) {
            error_log('Fout in getAllTags: ' . $e->getMessage());
            throw new Exception('Fout bij het ophalen van alle tags: ' . $e->getMessage());
        }
    }

    /**
     * Haal een tag op met ID.
     *
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public function getTagById(int $id): ?array
    {
        try {
            $query = "SELECT * FROM tags WHERE id = :id";
            return $this
                ->db
                ->fetchOne($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Fout in getTagById: ' . $e->getMessage());
            throw new Exception('Fout bij het ophalen van de tag met ID: ' . $e->getMessage());
        }
    }

    /**
     * Bewerk een tag met ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function editTag(int $id, array $data): bool
    {
        try {
            $query = "UPDATE tags SET name = :name WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, array_merge($data, ['id' => $id]));
        } catch (Exception $e) {
            error_log('Fout in editTag: ' . $e->getMessage());
            throw new Exception('Fout bij het bewerken van de tag: ' . $e->getMessage());
        }
    }

    /**
     * Verwijder een tag met ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function removeTag(int $id): bool
    {
        try {
            $query = "DELETE FROM tags WHERE id = :id";
            return $this
                ->db
                ->executeQuery($query, ['id' => $id]);
        } catch (Exception $e) {
            error_log('Fout in removeTag: ' . $e->getMessage());
            throw new Exception('Fout bij het verwijderen van de tag: ' . $e->getMessage());
        }
    }
}
