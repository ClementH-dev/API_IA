<?php
namespace Services;

use Models\Character;
use Models\Univers;
use Helpers\ClipdropHelper;
use Helpers\GroqHelper;

class CharacterService {
    private Character $characterModel;
    private Univers $universModel;

    public function __construct(Character $characterModel, Univers $universModel) {
        $this->characterModel = $characterModel;
        $this->universModel = $universModel;
    }

    public function createCharacter(int $universId, array $data): array {
        $univers = $this->universModel->getById($universId);
        if (!$univers) {
            return ['success' => false, 'message' => 'Univers non trouvé'];
        }

        $id_user = $univers['id_utilisateur'];
        $nom_univers = $univers['nom_univers'];

        if ($this->characterModel->getCharacterByName($data['nom_character'], $id_user)) {
            return ['success' => false, 'message' => 'Le personnage existe déjà'];
        }

        $combinedResponse = GroqHelper::generateCombinedPromptCharacter(
            $data['nom_character'], $data['description_character'], $nom_univers
        );

        $content = $combinedResponse['choices'][0]['message']['content'];
        $parts = preg_split("/\n\s*\n/", $content);
        if (count($parts) < 4) {
            return ['success' => false, 'message' => 'Le prompt GROQ ne contient pas les 4 parties attendues'];
        }

        [$IsKnow, $IsPolicyAccept, $ImagePrompt, $descriptionIA] = $parts;
        if (strtolower(trim($IsKnow)) !== '1. oui' || strtolower(trim($IsPolicyAccept)) !== '2. oui') {
            return ['success' => false, 'message' => 'L\'IA ne valide pas ce personnage'];
        }

        $imageData = ClipdropHelper::generateImage($ImagePrompt);
        if (isset($imageData['error'])) {
            return ['success' => false, 'message' => 'Erreur lors de la génération de l\'image'];
        }

        $folderName = strtolower(str_replace(' ', '_', $nom_univers));
        $uploadDir = "./uploads/" . $folderName;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . "/" . $data['nom_character'] . "_#" . $id_user . ".png";
        file_put_contents($filePath, $imageData);

        if (!$this->characterModel->saveCharacter($data['nom_character'], $descriptionIA, $filePath, $id_user, $universId)) {
            return ['success' => false, 'message' => 'Erreur lors de la sauvegarde du personnage'];
        }

        return ['success' => true, 'image' => $filePath];
    }

    public function deleteCharacter(int $id): array {
        $character = $this->characterModel->getCharacterById($id);
        if (!$character) {
            return ['success' => false, 'message' => 'Personnage introuvable'];
        }

        $filePath = $character['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (!$this->characterModel->deleteCharacterById($id)) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }

        return ['success' => true];
    }

    public function getCharacter(int $id) {
        return $this->characterModel->getCharacterById($id);
    }

    public function getAllCharactersByUnivers(int $universId) {
        return $this->characterModel->getAllCharactersByUnivers($universId);
    }
}
