<?php
/**
 * Middleware qui valide les données de la requête pour l'inscription et la connexion.
 */
namespace Middleware;

use Core\Controller;

class ValidationMiddleware extends Controller {

    // Fonction pour valider les champs de l'inscription
    public static function validateRegistration(array $data): void {
        $requiredFields = ['nom', 'prenom', 'telephone', 'mail', 'password', 'pseudo'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                (new Controller())->errorResponse('Tous les champs sont requis', 400);
                exit;
            }
        }

        // Vérification de la force du mot de passe
        if (strlen($data['password']) < 8) {
            (new Controller())->errorResponse('Le mot de passe doit contenir au moins 8 caractères', 400);
            exit;
        }
    }

    // Fonction pour valider les champs de la connexion
    public static function validateLogin(array $data): void {
        if (empty($data['identifier']) || empty($data['password'])) {
            (new Controller())->errorResponse('Identifiant et mot de passe requis', 400);
            exit;
        }
    }

    // Fonction pour valider les champs de la mise à jour de l'utilisateur
    public static function validateUpdateUser(array $data): void {
        $requiredFields = ['nom', 'prenom', 'telephone', 'mail', 'pseudo'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                (new Controller())->errorResponse('Tous les champs sont requis', 400);
                exit;
            }
        }
    }

    // Fonction pour valider les champs de la mise à jour du mot de passe
    public static function validateUpdatePassword(array $data): void {
        if (empty($data['oldPassword']) || empty($data['newPassword'])) {
            (new Controller())->errorResponse('Ancien et nouveau mot de passe requis', 400);
            exit;
        }
    }

    // Fonction pour valider les champs de la création d'un univers
    public static function validateCreateUnivers(array $data): void {
        if (empty($data['nom_univers'])) {
            (new Controller())->errorResponse('Tous les champs sont requis', 400);
            exit;
        }
    }

    public static function validateCharacter(array $data):void {
        if (empty($data['nom_character'])) {
            (new Controller())->errorResponse('Tous les champs sont requis', 400);
            exit;
        }
    }

    

}
