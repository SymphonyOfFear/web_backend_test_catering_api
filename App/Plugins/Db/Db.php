<?php

namespace App\Plugins\Db;

use PDO;
use PDOException;
use PDOStatement;

class Db
{
    private PDO $connection;
    private ?PDOStatement $stmt;

    /**
     * Constructor of this class.
     * Initializes the database connection using the provided parameters.
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $database, string $username, string $password)
    {
        // Establish the database connection using provided parameters
        $this->connection = $this->connect($host, $database, $username, $password);
    }

    /**
     * Start a transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Roll back the transaction.
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Commit a transaction.
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Execute a query with binding parameters.
     */
    public function executeQuery(string $query, array $bind = []): bool
    {
        $this->stmt = $this->connection->prepare($query);
        return $this->stmt->execute($bind);
    }

    /**
     * Fetch all records from a query.
     */
    public function fetchAll(string $query, array $bind = []): array
    {
        $this->stmt = $this->connection->prepare($query);
        $this->stmt->execute($bind);
        return $this->stmt->fetchAll();
    }

    /**
     * Fetch one record from a query.
     */
    public function fetchOne(string $query, array $bind = []): ?array
    {
        $this->stmt = $this->connection->prepare($query);
        $this->stmt->execute($bind);
        return $this->stmt->fetch() ?: null;
    }

    /**
     * Get the last inserted ID.
     */
    public function getLastInsertedId(): int
    {
        return (int)$this->connection->lastInsertId();
    }

    /**
     * Get the current PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Connect to the database using the provided parameters.
     */
    private function connect(string $host, string $database, string $username, string $password): PDO
    {
        try {
            return new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException("Failed to connect to the database: " . $e->getMessage());
        }
    }

    /**
     * Return the last executed statement if any.
     */
    public function getStatement(): ?PDOStatement
    {
        return $this->stmt;
    }
}
