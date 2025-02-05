<?php
namespace Controllers;

use Core\Controller;
use Core\Response;
use Services\UserService;
use Middleware\AuthMiddleware;
use Middleware\SanitizeMiddleware;
use Middleware\ValidationMiddleware;

class UserController extends Controller {
    private UserService $userService;
    private SanitizeMiddleware $sanitizenMiddleware;
    private ValidationMiddleware $validationMiddleware;
    private AuthMiddleware $authMiddleware;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
        $this->sanitizenMiddleware = new SanitizeMiddleware();
        $this->validationMiddleware = new ValidationMiddleware();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function register(): Response
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Nettoyer les champs 
        $data = $this->sanitizenMiddleware->sanitizeData($data);

        // Valider les champs requis
        $this->validationMiddleware->validateRegistration($data);

        try {
            $this->userService->registerUser($data);
            return $this->createdResponse(['message' => 'Utilisateur enregistré avec succès']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function login(): Response 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Nettoyer les champs
        $data = $this->sanitizenMiddleware->sanitizeData($data);

        // Valider les champs requis
        $this->validationMiddleware->validateLogin($data);

        try {
            $jwt = $this->userService->loginUser($data);
            header('Authorization: Bearer ' . $jwt);
            return $this->jsonResponse(['token' => $jwt]);
        } catch (\Exception $e) {
            return $this->unauthorizedResponse($e->getMessage());
        }
    }

    public function delUser(int $id): Response 
    {
        $this->authMiddleware->handle();

        try {
            $this->userService->deleteUser($id);
            return $this->createdResponse(['message' => 'Utilisateur supprimé.']);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse($e->getMessage());
        }
    }

    public function upUser(int $id): Response 
    {
        //Vérification JWT
        $this->authMiddleware->handle();

        $data = json_decode(file_get_contents('php://input'), true);
        // Nettoyer les champs
        $data = $this->sanitizenMiddleware->sanitizeData($data);
        // Vérifier les champs requis
        $this->validationMiddleware->validateUpdateUser($data);

        try {
            $this->userService->updateUser($id, $data);
            return $this->createdResponse(['message' => 'Utilisateur mis à jour.']);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse($e->getMessage());
        }
    }

    public function upPassword(int $id): Response 
    {
        // Vérification JWT
        $this->authMiddleware->handle();

        $data = json_decode(file_get_contents('php://input'), true);
        // Nettoyer les champs
        $data = $this->sanitizenMiddleware->sanitizeData($data);
        // Vérifier les champs requis
        $this->validationMiddleware->validateUpdatePassword($data);

        try {
            $this->userService->updatePassword($id, $data['oldPassword'], $data['newPassword']);
            return $this->createdResponse(['message' => 'Mot de passe mis à jour.']);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse($e->getMessage());
        }
    }

    public function getUser(int $id): Response 
    {
        $this->authMiddleware->handle();
        
        try {
            $user = $this->userService->getUser($id);
            return $this->jsonResponse($user);
        } catch (\Exception $e) {
            return $this->notFoundResponse($e->getMessage());
        }
    }

    public function getAllUsers(): Response 
    {
        $this->authMiddleware->handle();
        
        try {
            $users = $this->userService->getAllUsers();
            return $this->jsonResponse($users);
        } catch (\Exception $e) {
            return $this->notFoundResponse($e->getMessage());
        }
    }
}