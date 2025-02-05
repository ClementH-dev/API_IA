<?php
namespace Models;

use Core\Model;
use PDO;
use PDOException;

class Univers extends Model {
    protected string $table = 'univers';

    /**
     * Enregistre un nouvel univers dans la base de données
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param array $data Les données de l'univers
     * @param string $descriptionIA La description générée par l'IA
     * @param string $filePath Le chemin de l'image
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function saveUnivers(int $id, array $data, string $descriptionIA, string $filePath): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom_univers, description_univers, image, id_utilisateur) 
                                        VALUES (:nom_univers, :description_univers, :image, :id_utilisateur)");

            $stmt->bindParam(':nom_univers', $data['nom_univers']);
            $stmt->bindParam(':description_univers', $descriptionIA);
            $stmt->bindParam(':image', $filePath);
            $stmt->bindParam(':id_utilisateur', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère un univers par son identifiant
     * 
     * @param int $id L'identifiant de l'univers
     * @return array|false Retourne un tableau associatif contenant l'univers ou false en cas d'échec
     */
    public function getById(int $id): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime un univers par son identifiant
     * 
     * @param int $id L'identifiant de l'univers
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function deleteUniversById(int $id): bool {
        return $this->deleteById($this->table, $id);
    }

    /**
     * Récupère un univers par son nom et l'identifiant de l'utilisateur
     * 
     * @param string $nom_univers Le nom de l'univers
     * @param int $id L'identifiant de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant l'univers ou false en cas d'échec
     */
    public function getByNomUnivers(string $nom_univers, int $id): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_utilisateur = :id AND nom_univers = :nom_univers");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nom_univers', $nom_univers);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère tous les univers associés à un utilisateur
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant tous les univers ou false en cas d'échec
     */
    public function getAllUnivers(int $id): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_utilisateur = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
