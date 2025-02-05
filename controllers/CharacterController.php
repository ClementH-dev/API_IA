<?php
namespace Controllers;

use Core\Controller;
use Core\Response;
use Services\CharacterService;
use Middleware\AuthMiddleware;
use Middleware\ValidationMiddleware;
use Middleware\SanitizeMiddleware;

class CharacterController extends Controller {
    private CharacterService $characterService;
    private SanitizeMiddleware $sanitizeMiddleware;
    private ValidationMiddleware $validationMiddleware;
    private AuthMiddleware $authMiddleware;

    public function __construct(CharacterService $characterService) 
    {
        $this->characterService = $characterService;
        $this->sanitizeMiddleware = new SanitizeMiddleware();
        $this->validationMiddleware = new ValidationMiddleware();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function addCharacter(int $id): Response 
    {
        $this->authMiddleware->handle();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->sanitizeMiddleware->sanitizeData($data);
        $this->validationMiddleware->validateCharacter($data);

        $response = $this->characterService->createCharacter($id, $data);
        if (!$response['success']) {
            return $this->errorResponse($response['message']);
        }

        return $this->createdResponse([
            'message' => 'Le personnage a bien été créé.',
            'image' => $response['image']
        ]);
    }

    public function delCharacter(int $id): Response 
    {
        $this->authMiddleware->handle();

        $response = $this->characterService->deleteCharacter($id);
        if (!$response['success']) {
            return $this->errorResponse($response['message']);
        }

        return $this->createdResponse(['message' => 'Personnage supprimé.']);
    }

    public function getCharacterById(int $id): Response 
    {
        $this->authMiddleware->handle();

        $character = $this->characterService->getCharacter($id);
        if (!$character) {
            return $this->notFoundResponse('Personnage introuvable.');
        }

        return $this->jsonResponse($character);
    }

    public function getAllCharactersByUnivers(int $id): Response 
    {
        $this->authMiddleware->handle();

        $characters = $this->characterService->getAllCharactersByUnivers($id);
        if (!$characters) {
            return $this->notFoundResponse('Aucun personnage trouvé pour cet univers.');
        }

        return $this->jsonResponse($characters);
    }
}
