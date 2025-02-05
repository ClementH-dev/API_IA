<?php
namespace Models;

use Core\Model;
use PDO;
use PDOException;

class Chat extends Model {
    protected string $table = 'conversation';

    /**
     * Crée une nouvelle conversation entre deux utilisateurs
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param int $idUser L'identifiant de l'autre utilisateur
     * 
     */ 
    public function createChat(int $id, int $idUser): array|bool{
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (id_personnage, id_utilisateur ) VALUES (:id, :idUser)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':idUser', $idUser);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère une conversation par son id ainsi que les infos de son personnage et son univers
     * 
     * @param int $id L'identifiant de la conversation
     * @return array|bool
     */
    public function getChatById(int $id): array|bool {
        try {
            $stmt = $this->db->prepare("SELECT c.*, p.*, u.* 
                                        FROM {$this->table} c
                                        JOIN personnage p ON c.id_personnage = p.id
                                        JOIN univers u ON p.id_univers = u.id 
                                        WHERE c.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère toutes les conversations d'un utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @return array|bool
     */
    public function getAllChatsByUserId(int $id): array|bool {
        try {
            $stmt = $this->db->prepare("SELECT c.*, u.id AS id_univers
                                        FROM {$this->table} c
                                        JOIN personnage p ON c.id_personnage = p.id
                                        JOIN univers u ON p.id_univers = u.id 
                                        WHERE c.id_utilisateur = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteChat(int $id):bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
}