<?php
namespace Services;

use Models\Univers;
use Models\Character;
use Helpers\ClipdropHelper;
use Helpers\GroqHelper;
use Middleware\ValidationMiddleware;
use Middleware\SanitizeMiddleware;
use Helpers\JwtHelper;

class UniversService {
    private Univers $universModel;
    private Character $characterModel;

    public function __construct() {
        $this->universModel = new Univers();
        $this->characterModel = new Character();
    }

    public function addUnivers(array $data, string $token): array {
        // Décoder le token pour récupérer l'ID utilisateur
        $decodedToken = JwtHelper::decodeToken(trim(str_replace('Bearer', '', $token)));
        if (!$decodedToken || !isset($decodedToken->sub)) {
            throw new \Exception("Token invalide ou expiré");
        }
        $userId = $decodedToken->sub;

        // Validation et nettoyage des données
        $data = SanitizeMiddleware::sanitizeData($data);
        ValidationMiddleware::validateCreateUnivers($data);

        // Vérification si l'utilisateur a déjà un univers avec ce nom
        if ($this->universModel->getByNomUnivers($data['nom_univers'], $userId)) {
            throw new \Exception("Vous avez déjà un univers avec ce nom.");
        }

        // Appel à GROQ pour générer du contenu
        $response = GroqHelper::generateCombinedPrompt($data['nom_univers'], $data['description_univers']);
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new \Exception("Erreur dans la réponse de l'IA");
        }

        // Extraction des parties de la réponse
        $parts = preg_split("/\n\s*\n/", $response['choices'][0]['message']['content']);
        if (count($parts) < 4) {
            throw new \Exception("Le prompt de Groq est incomplet");
        }

        [$IsKnow, $IsPolicyAccept, $ImagePrompt, $descriptionIA] = array_map('trim', $parts);

        // Vérification des réponses de l'IA
        if (strtolower($IsKnow) !== '1. oui') {
            throw new \Exception("L'IA ne connaît pas cet univers.");
        }
        if (strtolower($IsPolicyAccept) !== '2. oui') {
            throw new \Exception("L'univers ne respecte pas les règles.");
        }

        // Génération de l'image avec Clipdrop
        $imageData = ClipdropHelper::generateImage($ImagePrompt);
        if (!$imageData || isset($imageData['error'])) {
            throw new \Exception("Erreur lors de la génération de l'image : " . ($imageData['error'] ?? "Inconnue"));
        }

        // Création du dossier de stockage
        $universeName = strtolower(str_replace(' ', '_', $data['nom_univers']));
        $uploadDir = "./uploads/" . $universeName;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new \Exception("Impossible de créer le répertoire d'upload");
        }

        // Sauvegarde de l'image
        $filePath = "{$uploadDir}/pictureUnivers_#{$userId}.png";
        if (!file_put_contents($filePath, $imageData)) {
            throw new \Exception("Erreur lors de la sauvegarde de l'image");
        }

        // Sauvegarde de l'univers en base de données
        $saveResult = $this->universModel->saveUnivers($userId, $data, $descriptionIA, $filePath);
        if (!$saveResult) {
            throw new \Exception("Erreur lors de l'enregistrement de l'univers");
        }

        return [
            'message' => "L'univers a bien été créé.",
            'filepath' => $filePath
        ];
    }

    public function delUnivers(int $id): array {
        // Récupérer les informations de l'univers
        $univers = $this->universModel->getById($id);
        if (!$univers) {
            throw new \Exception("Univers introuvable.");
        }

        // Récupérer l'id de l'utilisateur qui a créé l'univers
        $id_utilisateur = $univers['id_utilisateur'];

        // Récupérer les personnages liés à cet univers
        $characters = $this->characterModel->getAllCharactersByUnivers($id, $id_utilisateur);

        // Supprimer les images liées à cet univers
        foreach ($characters as $character) {
            $characterFilePath = $character['image'];
            if (file_exists($characterFilePath)) {
                unlink($characterFilePath);
            }
        }

        // Supprimer le fichier de l'univers
        $filePath = $univers['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $universeName = strtolower(str_replace(' ', '_', $univers['nom_univers']));
        // Vérifier si des fichiers sont encore présents dans le répertoire
        $files = glob("./uploads/" . $universeName . "/*");
        if (count($files) === 0) {
            rmdir("./uploads/" . $universeName);
        }

        // Supprimer l'entrée de la base de données
        $result = $this->universModel->deleteUniversById($id);
        if (!$result) {
            throw new \Exception("Erreur lors de la suppression.");
        }

        return ['message' => 'Univers supprimé.'];
    }

    public function getUniversById(int $id): array {
        // Récupérer les informations de l'univers
        $univers = $this->universModel->getById($id);
        if (!$univers) {
            throw new \Exception("Univers introuvable.");
        }
        return $univers;
    }

    public function getAllUnivers(int $userId): array {
        // Récupérer tous les univers de l'utilisateur
        $univers = $this->universModel->getAllUnivers($userId);
        if (!$univers) {
            throw new \Exception("Aucun univers trouvé.");
        }
        return $univers;
    }
}
