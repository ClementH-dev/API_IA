<?php
namespace Controllers;

use Core\Controller;
use Core\Response;
use Middleware\AuthMiddleware;
use Services\ChatService;

class ChatController extends Controller {
    private ChatService $chatService;
    private AuthMiddleware $authMiddleware;

    public function __construct(ChatService $chatService) 
    {
        $this->chatService = $chatService;
        $this->authMiddleware = new AuthMiddleware();
    }

    public function createChat(int $characterId): Response 
    {
        $this->authMiddleware->handle();
        
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$token) {
            return $this->errorResponse("Token manquant", 401);
        }

        $token = trim(str_replace('Bearer', '', $token));
        $userId = $this->chatService->getUserIdFromToken($token);
        if (!$userId) {
            return $this->errorResponse("Token invalide ou expiré", 401);
        }

        if ($this->chatService->createChat($characterId, $userId)) {
            return $this->createdResponse(['message' => 'Conversation créée']);
        }
        return $this->errorResponse('Erreur lors de la création de la conversation');
    }

    public function getChat(int $chatId): Response 
    {
        $this->authMiddleware->handle();
        
        $chat = $this->chatService->getChatById($chatId);
        if (!$chat) {
            return $this->notFoundResponse('Conversation introuvable.');
        }
        return $this->jsonResponse($chat);
    }

    public function getAllChat(): Response 
    {
        $this->authMiddleware->handle();
        
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$token) {
            return $this->errorResponse("Token manquant", 401);
        }

        $token = trim(str_replace('Bearer', '', $token));
        $userId = $this->chatService->getUserIdFromToken($token);
        if (!$userId) {
            return $this->errorResponse("Token invalide ou expiré", 401);
        }

        $chats = $this->chatService->getAllChatsByUser($userId);
        if (!$chats) {
            return $this->notFoundResponse('Aucune conversation trouvée.');
        }
        return $this->jsonResponse($chats);
    }

    public function deleteChat(int $chatId): Response 
    {
        $this->authMiddleware->handle();
        
        if ($this->chatService->deleteChat($chatId)) {
            return $this->createdResponse(['message' => 'Conversation supprimée']);
        }
        return $this->errorResponse('Erreur lors de la suppression de la conversation');
    }
}
