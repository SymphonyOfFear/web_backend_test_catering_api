<?php

namespace App\Plugins\Db;

use PDO;
use PDOException;
use Exception;

class Db
{
    protected PDO $conn;

    public function __construct(string $host, string $database, string $username, string $password)
    {
        // Verbinden met de database
        try {
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Database verbinding mislukt: ' . $e->getMessage());
        }
    }

    // Start een transactie
    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    // Bevestig een transactie
    public function commit(): bool
    {
        return $this->conn->commit();
    }

    // Rollback een transactie
    public function rollBack(): bool
    {
        return $this->conn->rollBack();
    }

    // Voer een query uit met optionele parameters
    public function executeQuery(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception('Query uitvoeren mislukt: ' . $e->getMessage());
        }
    }

    // Haal één resultaat op van een query
    public function fetchOne(string $query, array $params = []): ?array
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new Exception('Query uitvoeren mislukt: ' . $e->getMessage());
        }
    }

    // Haal alle resultaten op van een query
    public function fetchAll(string $query, array $params = []): array
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Query uitvoeren mislukt: ' . $e->getMessage());
        }
    }

    // Haal het laatst toegevoegde ID op
    public function getLastInsertedId(): int
    {
        return (int) $this->conn->lastInsertId();
    }

    // Haal de PDO-verbinding op
    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
