<?php
namespace Services;

use Models\User;
use Models\Univers;
use Models\Character;
use Helpers\JwtHelper;

class UserService {
    private User $userModel;
    private Univers $universModel;
    private Character $characterModel;

    public function __construct() {
        $this->userModel = new User();
        $this->universModel = new Univers();
        $this->characterModel = new Character();
    }

    public function registerUser(array $data): bool {
        $existingMail = $this->userModel->findByEmail($data['mail']);
        $existingPseudo = $this->userModel->findByPseudo($data['pseudo']);
        
        if ($existingMail) {
            throw new \Exception('Un utilisateur avec cet email existe déjà');
        }
        if ($existingPseudo) {
            throw new \Exception('Un utilisateur avec ce pseudo existe déjà');
        }

        return $this->userModel->register($data);
    }

    public function loginUser(array $data): string {
        $user = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL)
            ? $this->userModel->findByEmail($data['identifier'])
            : $this->userModel->findByPseudo($data['identifier']);
        
        if (!$user || !password_verify($data['password'], $user['password'])) {
            throw new \Exception('Identifiants incorrects');
        }

        $payload = [
            'sub' => $user['id'],
            'pseudo' => $user['pseudo']
        ];

        return JwtHelper::generateToken($payload);
    }

    public function deleteUser(int $id): bool {
        // Récupérer les univers et les personnages créés par l'utilisateur
        $univers = $this->universModel->getAllUnivers($id);
        foreach ($univers as $univer) {
            $universeName = strtolower(str_replace(' ', '_', $univer['nom_univers']));
            $characters = $this->characterModel->getAllCharactersByUnivers($univer['id']);
            // Supprimer images des personnages
            foreach ($characters as $character) {
                $characterFilePath = $character['image'];
                if (file_exists($characterFilePath)) {
                    unlink($characterFilePath);
                }
            }

            // Supprimer images de l'univers
            $filePath = $univer['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Vérifier si des fichiers sont encore présents dans le répertoire
            $files = glob("./uploads/" . $universeName . "/*");
            if (count($files) === 0) {
                rmdir("./uploads/" . $universeName);
            }
        }

        return $this->userModel->deleteUserById($id);
    }

    public function updateUser(int $id, array $data): bool {
        return $this->userModel->updateUserById($id, $data);
    }

    public function updatePassword(int $id, string $oldPassword, string $newPassword): bool {
        $user = $this->userModel->findUserById($id);
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            throw new \Exception('Ancien mot de passe incorrect');
        }
        return $this->userModel->updatePasswordById($id, $newPassword);
    }

    public function getUser(int $id): array {
        return $this->userModel->findUserById($id);
    }

    public function getAllUsers(): array {
        return $this->userModel->getAllUsers();
    }
}
