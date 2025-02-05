<?php
namespace Services;

use Models\Message;
use Models\Chat;
use Helpers\GroqHelper;

class MessageService {
    private Message $messageModel;
    private Chat $chatModel;

    public function __construct(Message $messageModel, Chat $chatModel) {
        $this->messageModel = $messageModel;
        $this->chatModel = $chatModel;
    }

    public function getChatById(int $id): ?array {
        return $this->chatModel->getChatById($id);
    }

    public function sendMessage(int $chatId, string $userMessage): ?string {
        $chatter = $this->getChatById($chatId);
        if (!$chatter) return null;

        try {
            $responseAI = GroqHelper::generateResponse(
                $userMessage,
                $chatter['nom_personnage'],
                $chatter['description_univers'],
                $chatter['description_personnage']
            );
            return $responseAI['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function saveMessage(int $chatId, string $userMessage, string $response): bool {
        return $this->messageModel->createMessage($chatId, $userMessage, $response);
    }

    public function getAllMessagesByChatId(int $chatId): array {
        return $this->messageModel->getAllMessagesByChatId($chatId) ?: [];
    }

    public function updateLastMessage(int $chatId): ?string {
        $lastMessage = $this->messageModel->getLastUserMessageByChatId($chatId);
        if (!$lastMessage) return null;

        return $this->sendMessage($chatId, $lastMessage['contenu']);
    }
}
