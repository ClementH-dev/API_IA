<?php
namespace Helpers;

class GroqHelper {
    private static $api_url; 
    private static $api_key;
    
    public static function init(): void {
        self::$api_url = $_ENV['GROQ_API_URL'];
        self::$api_key = $_ENV['GROQ_API_KEY'];
    }

    public static function callGroq($prompt) {
        $ch = curl_init(self::$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer " . self::$api_key
        ]);

        $postData = json_encode([
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            "model" => "llama-3.3-70b-versatile"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public static function generateCombinedPrompt($univers, $description) {
        $templateUnivers = file_get_contents('../prompt/generatePromptUnivers.txt');

        if (empty($description)) {
            $description = "non fournie.";
        }
    
        $prompt = str_replace(
            ['{{univers}}', '{{description}}'],
            [$univers, $description],
            $templateUnivers
        );
    
        return self::callGroq($prompt);
    }
    

    public static function generateCombinedPromptCharacter($character, $description, $univers) {
        $templateCharacter = file_get_contents('../prompt/generatePromptCharacter.txt');
        
        if (empty($description)) {
            $description = "non fournie.";
        }
    
        $prompt = str_replace(
            ['{{character}}', '{{univers}}', '{{description}}'],
            [$character, $univers, $description],
            $templateCharacter
        );
    
        return self::callGroq($prompt);
    }
    

    public static function generateResponse(string $message, string $nomPersonnage, string $descriptionUnivers, string $descriptionPersonnage){
        $prompt = file_get_contents('../prompt/reponse.txt');
        $prompt = str_replace('{message}', $message, $prompt);
        $prompt = str_replace('{nom_personnage}', $nomPersonnage, $prompt);
        $prompt = str_replace('{description_univers}', $descriptionUnivers, $prompt);
        $prompt = str_replace('{description_personnage}', $descriptionPersonnage, $prompt);
        
        return self::callGroq($prompt);
    }
}

// Initialisation de l'API Groq 
GroqHelper::init();
