<?php
namespace Controllers;

use Core\Controller;
use Core\Response;
use Helpers\JwtHelper;
use Services\UniversService;
use Middleware\AuthMiddleware;

class UniversController extends Controller {
    private UniversService $universService;
    private AuthMiddleware $authMiddleware;

    public function __construct(UniversService $universService) 
    {
        $this->universService = $universService;
        $this->authMiddleware = new AuthMiddleware();
    }

    public function addUnivers(): Response 
    {
        try {
            // Vérification JWT
            $this->authMiddleware->handle();

            $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
            if (!$token) {
                return $this->errorResponse("Token manquant", 401);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                return $this->errorResponse("Données JSON invalides", 400);
            }

            // Appel au service pour créer l'univers
            $result = $this->universService->addUnivers($data, $token);

            return $this->createdResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function delUnivers(int $id): Response 
    {
        try {
            // Vérification JWT
            $this->authMiddleware->handle();

            // Appel au service pour supprimer l'univers
            $result = $this->universService->delUnivers($id);

            return $this->createdResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getUniversById(int $id): Response 
    {
        try {
            // Vérification JWT
            $this->authMiddleware->handle();

            // Appel au service pour obtenir l'univers
            $univers = $this->universService->getUniversById($id);

            return $this->jsonResponse($univers);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getAllUnivers(): Response 
    {
        try {
            // Vérification JWT
            $this->authMiddleware->handle();
            
            $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
            if (!$token) {
                return $this->errorResponse("Token manquant", 401);
            }

            $decodedToken = JwtHelper::decodeToken(trim(str_replace('Bearer', '', $token)));
            if (!$decodedToken || !isset($decodedToken->sub)) {
                return $this->errorResponse("Token invalide ou expiré", 401);
            }

            // Appel au service pour obtenir tous les univers
            $univers = $this->universService->getAllUnivers($decodedToken->sub);

            return $this->jsonResponse($univers);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
