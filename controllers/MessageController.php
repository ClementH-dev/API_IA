<?php
namespace Controllers;

use Core\Controller;
use Core\Response;
use Services\MessageService;
use Middleware\AuthMiddleware;
use Middleware\SanitizeMiddleware;

class MessageController extends Controller {
    private MessageService $messageService;
    private SanitizeMiddleware $sanitizeMiddleware;
    private AuthMiddleware $authMiddleware;

    public function __construct(MessageService $messageService) 
    {
        $this->messageService = $messageService;
        $this->sanitizeMiddleware = new SanitizeMiddleware();  
        $this->authMiddleware = new AuthMiddleware();
    }

    public function sendMessage(int $id): Response 
    {
        $this->authMiddleware->handle();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->sanitizeMiddleware->sanitizeData($data);

        $response = $this->messageService->sendMessage($id, $data['message']);
        if (!$response) return $this->internalServerErrorResponse('Erreur lors de la génération de la réponse.');

        if (!$this->messageService->saveMessage($id, $data['message'], $response)) {
            return $this->internalServerErrorResponse('Erreur lors de l\'enregistrement du message.');
        }

        return $this->createdResponse([
            'message' => 'Message envoyé avec succès.',
            'reponse' => $response
        ]);
    }

    public function getAllMessagesFromChat(int $id): Response 
    {
        $this->authMiddleware->handle();
        $messages = $this->messageService->getAllMessagesByChatId($id);

        if (empty($messages)) return $this->notFoundResponse('Aucun message trouvé.');

        return $this->jsonResponse($messages);
    }

    public function updateLastMessage(int $id): Response 
    {
        $this->authMiddleware->handle();
        $newMessage = $this->messageService->updateLastMessage($id);

        if (!$newMessage) return $this->internalServerErrorResponse('Erreur lors de la mise à jour du dernier message utilisateur.');

        return $this->createdResponse([
            'message' => 'Dernier message utilisateur mis à jour.',
            'reponse' => $newMessage
        ]);
    }
}