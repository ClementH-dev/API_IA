<?php

use Core\Router;
use Controllers\UserController;
use Controllers\UniversController;
use Controllers\CharacterController;
use Controllers\ChatController;
use Controllers\MessageController;
use Middleware\AuthMiddleware;
use Middleware\ValidationMiddleware;
use Middleware\SanitizeMiddleware;


/**
 * Routes pour les utilisateurs
 */
Router::group('/users', function () {
    Router::post('', [UserController::class, 'register'], [ValidationMiddleware::class, SanitizeMiddleware::class]); // Enregistrer un utilisateur
    Router::post('/auth', [UserController::class, 'login'], [ValidationMiddleware::class, SanitizeMiddleware::class]); // Authentifier un utilisateur
    Router::get('', [UserController::class, 'getAllUsers'], [AuthMiddleware::class]); // Récupérer tous les utilisateurs
    Router::get('/{id}', [UserController::class, 'getUser'], [AuthMiddleware::class]); // Récupérer un utilisateur par ID
    Router::put('/{id}', [UserController::class, 'upUser'], [AuthMiddleware::class, ValidationMiddleware::class, SanitizeMiddleware::class]); // Mettre à jour un utilisateur par ID
    Router::put('/{id}/password', [UserController::class, 'upPassword'], [AuthMiddleware::class, ValidationMiddleware::class, SanitizeMiddleware::class]); // Mettre à jour le mot de passe d'un utilisateur
    Router::delete('/{id}', [UserController::class, 'delUser'], [AuthMiddleware::class]); // Supprimer un utilisateur par ID
});

/**
 * Routes pour les univers
 */
Router::group('/univers', function () {
    Router::post('/', [UniversController::class, 'addUnivers'], [AuthMiddleware::class, ValidationMiddleware::class, SanitizeMiddleware::class]); // Ajouter un univers
    Router::get('/{universId}', [UniversController::class, 'getUniversById'], [AuthMiddleware::class]); // Récupérer un univers par ID
    Router::get('/', [UniversController::class, 'getAllUnivers'], [AuthMiddleware::class]); // Récupérer tous les univers d'un utilisateur
    Router::delete('/{universId}', [UniversController::class, 'delUnivers'], [AuthMiddleware::class]); // Supprimer un univers par ID
});

/**
 * Routes pour les personnages
 */
Router::group('/characters', function () {
    Router::post('/{universId}', [CharacterController::class, 'addCharacter'], [AuthMiddleware::class, ValidationMiddleware::class, SanitizeMiddleware::class]); // Ajouter un personnage
    Router::get('/{characterId}', [CharacterController::class, 'getCharacterById'], [AuthMiddleware::class]); // Récupérer un personnage par ID
    Router::get('/univers/{universId}', [CharacterController::class, 'getAllCharactersByUnivers'], [AuthMiddleware::class]); // Récupérer tous les personnages d'un univers
    Router::delete('/{characterId}', [CharacterController::class, 'delCharacter'], [AuthMiddleware::class]); // Supprimer un personnage par ID
});

/**
 * Routes pour les chats
 */
Router::group('/chats', function () {
    Router::post('/{characterId}', [ChatController::class, 'createChat'], [AuthMiddleware::class]); // Créer un chat
    Router::get('/{chatId}', [ChatController::class, 'getChat'], [AuthMiddleware::class]); // Récupérer un chat par ID
    Router::get('/', [ChatController::class, 'getAllChat'], [AuthMiddleware::class]); // Récupérer tous les chats d'un utilisateur
    Router::delete('/{chatId}', [ChatController::class, 'deleteChat'], [AuthMiddleware::class]); // Supprimer un chat par ID
});

/**
 * Routes pour les messages
 */

Router::group('/messages', function () {
    Router::post('/{chatId}', [MessageController::class, 'sendMessage'], [AuthMiddleware::class]); // Ajouter un message
    Router::get('/{chatId}', [MessageController::class, 'getAllMessagesFromChat'], [AuthMiddleware::class]); // Récupérer tous les messages d'un chat
    Router::put('/{chatId}', [MessageController::class, 'updateLastMessage'], [AuthMiddleware::class]); // Mettre a jour le dernier message de la conversation
});

// Démarrer le routeur
Router::handle();
