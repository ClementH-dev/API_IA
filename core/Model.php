<?php
namespace Core;

use PDO;
use PDOException;

class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findAll(string $table): array {
        try {
            $stmt = $this->db->query("SELECT * FROM {$table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Gestion des erreurs
            return ['error' => $e->getMessage()];
        }
    }

    public function findById(string $table, int $id): array | false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Gestion des erreurs
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteById(string $table, int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
