<?php
namespace Models;

use Core\Model;
use PDO;
use PDOException;

class Message extends Model{
    protected string $table ='message';

    /**
    * Insert le message de l'utilisateur et la réponse de l'ia en base
    * @param int $id L'identifiant de l'utilisateur
    * @param string $message Le message de l'utilisateur
    * @param string $reponseAI La réponse de l'IA
    * @return bool Retourne true en cas de succès, false sinon
    */
    public function createMessage(int $id, string $message, string $reponseAI): bool{
        try {
            // Commencer une transaction
            $this->db->beginTransaction();
    
            // Insertion du message
            $sql = "INSERT INTO {$this->table} (contenu, envoye_par_ia, id_conversation) VALUES (:message, false, :id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            // Insertion de la réponse IA
            $sql2 = "INSERT INTO {$this->table} (contenu, envoye_par_ia, id_conversation) VALUES (:reponseAI, true, :id)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->bindParam(':reponseAI', $reponseAI);
            $stmt2->bindParam(':id', $id);
            $stmt2->execute();
    
            // Valider la transaction
            $this->db->commit();
    
            return true;
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->rollBack();
            // Log l'erreur
            error_log($e->getMessage());
            return false;
        }
    }

    /**
    * Récupère tous les messages d'une conversation donnée
    * @param int $id L'identifiant de la conversation
    * @return array|false Retourne un tableau associatif contenant tous les messages ou false en cas d'échec
    */
    public function getAllMessagesByChatId(int $id): array|bool{
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_conversation = :id ORDER BY id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            return false;
        }
    }

    /**
    * Met à jour le dernier message d'une conversation
    * @param int $id L'identifiant de la conversation
    * @param string $message Le nouveau message
    * @return array|false Retourne le dernier message mis à jour ou false en cas d'échec
    */
    public function updateLastMessage(int $id, string $message): bool{
        try{
            $stmt = $this->db->prepare("UPDATE {$this->table} SET contenu = :contenu WHERE id_conversation = :id ORDER BY id DESC LIMIT 1");
            $stmt->bindParam(':contenu', $message);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }catch(PDOException $e) {
            return false;
        }
    }

    public function getLastUserMessageByChatId(int $chatId): array|bool{
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_conversation = :id AND envoye_par_ia = false ORDER BY id DESC LIMIT 1");
            $stmt->bindParam(':id', $chatId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            return false;
        }
    }
    
}