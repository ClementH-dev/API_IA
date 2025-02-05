<?php
namespace Services;

use Models\Character;
use Models\Chat;
use Helpers\JwtHelper;

class ChatService {
    private Chat $chatModel;
    private Character $characterModel;

    public function __construct(Chat $chatModel, Character $characterModel) {
        $this->chatModel = $chatModel;
        $this->characterModel = $characterModel;
    }

    public function createChat(int $characterId, int $userId): bool {
        $character = $this->characterModel->getCharacterById($characterId);
        if (!$character) {
            return false;
        }
        return $this->chatModel->createChat($characterId, $userId);
    }

    public function getChatById(int $chatId): array|false {
        return $this->chatModel->getChatById($chatId);
    }

    public function getAllChatsByUser(int $userId): array|false {
        return $this->chatModel->getAllChatsByUserId($userId);
    }

    public function deleteChat(int $chatId): bool {
        return $this->chatModel->deleteChat($chatId);
    }

    public function getUserIdFromToken(string $token): int|null {
        $decodedToken = JwtHelper::decodeToken($token);
        return $decodedToken->sub ?? null;
    }
}
