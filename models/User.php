<?php
namespace Models;

use Core\Model;
use PDO;
use PDOException;

class User extends Model {
    protected string $table = 'utilisateurs';

    /**
     * Enregistre un nouvel utilisateur dans la base de données
     * 
     * @param array $data Les données de l'utilisateur
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function register(array $data): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom, prenom, telephone, mail, password, pseudo) VALUES (:nom, :prenom, :telephone, :mail, :password, :pseudo)");

            // Hacher le mot de passe
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':telephone', $data['telephone']);
            $stmt->bindParam(':mail', $data['mail']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':pseudo', $data['pseudo']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Recherche un utilisateur par son email
     * 
     * @param string $email L'email de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant l'utilisateur ou false en cas d'échec
     */
    public function findByEmail(string $email): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE mail = :mail");
            $stmt->bindParam(':mail', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Recherche un utilisateur par son pseudo
     * 
     * @param string $pseudo Le pseudo de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant l'utilisateur ou false en cas d'échec
     */
    public function findByPseudo(string $pseudo): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE pseudo = :pseudo");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime un utilisateur par son identifiant
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function deleteUserById(int $id): bool {
        return $this->deleteById($this->table, $id);
    }

    /**
     * Recherche un utilisateur par son identifiant
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @return array|false Retourne un tableau associatif contenant l'utilisateur ou false en cas d'échec
     */
    public function findUserById(int $id): array|false {
        try {
            return $this->findById($this->table, $id);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour les informations d'un utilisateur par son identifiant
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param array $data Les nouvelles données de l'utilisateur
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function updateUserById(int $id, array $data): bool {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} 
                                        SET nom = :nom, 
                                            prenom = :prenom, 
                                            telephone = :telephone, 
                                            mail = :mail, 
                                            pseudo = :pseudo 
                                        WHERE id = :id"); 
            
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':telephone', $data['telephone']);
            $stmt->bindParam(':mail', $data['mail']);
            $stmt->bindParam(':pseudo', $data['pseudo']);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Met à jour le mot de passe d'un utilisateur par son identifiant
     * 
     * @param int $id L'identifiant de l'utilisateur
     * @param string $password Le nouveau mot de passe
     * @return bool Retourne true en cas de succès, false sinon
     */
    public function updatePasswordById(int $id, string $password): bool {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE id = :id");
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère tous les utilisateurs
     * 
     * @return array Retourne un tableau contenant tous les utilisateurs
     */
    public function getAllUsers(): array {
        return $this->findAll($this->table);
    }
}
