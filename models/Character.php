<?php
namespace Models;

use Core\Model;
use PDO;
use PDOException;

class Character extends Model {
    protected string $table = 'personnage';

    /**
     * Récupère un personnage par son nom et l'identifiant de l'utilisateur
     * 
     * @param string $nomCharacter Le nom de l'univers
     * @param int $id L'identifiant de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant l'univers ou false en cas d'échec
     */
    public function getCharacterByName(string $nomCharacter, int $userId): array|false{
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE nom_personnage = :nom AND id_utilisateur = :userId");
            $stmt->bindParam(':nom', $nomCharacter, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }

    }

    /**
     * Enregistre un nouveau personnage dans la base de données
     * 
     * @param string $username nom de personnage
     * @param array $data Les données de l'univers
     * @param string $descriptionIA La description générée par l'IA
     * @param string $filePath Le chemin de l'image
     * @param int $id_user L'identifiant de l'utilisateur
     * @param int $id_univers L'univers lié a ce personnage
     * @return bool Retourne true en cas de succès, false sinon
     */

     public function saveCharacter(string $username, string $descriptionIA, string $filePath, int $userId, int $universeId): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom_personnage, description_personnage, image, id_utilisateur, id_univers) VALUES (:username, :description, :filePath, :userId, :universeId)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':description', $descriptionIA, PDO::PARAM_STR);
            $stmt->bindParam(':filePath', $filePath, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':universeId', $universeId, PDO::PARAM_INT);

            return $stmt->execute();
        }catch(PDOException $e){
            return false;
        }
    }

     /**
     * Récupère un personnage par son identifiant
     * 
     * @param int $id L'identifiant du personnage
     * @return array|false Retourne un tableau associatif contenant l'univers ou false en cas d'échec
     */
    public function getCharacterById(int $id): array|false {
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
     * Supprime un personnage par son identifiant
     * 
     * @param int $id
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function deleteCharacterById(int $id): bool {
        return $this->deleteById($this->table, $id);
    }

    /**
     * Récupère tous les personnages liés à un univers
     * 
     * @param int $id L'identifiant de l'univers
     * @return array|false Retourne un tableau associatif contenant les personnages ou false en cas d'échec
     * 
     */
    public function getAllCharactersByUnivers(int $id):array|false{
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_univers = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}