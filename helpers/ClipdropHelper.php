<?php
namespace Helpers;

class ClipdropHelper {
    private static $api_url; 
    private static $api_key;
    
    public static function init(): void {
        self::$api_url = $_ENV['CLIPDROP_API_URL'];
        self::$api_key = $_ENV['CLIPDROP_API_KEY'];
    }

    public static function callClip($prompt) {
        $ch = curl_init(self::$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data',
            'x-api-key: ' . self::$api_key
        ]);

        $postData = [
            'prompt' => $prompt
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'error' => curl_error($ch)
            ];
        }

        curl_close($ch);

        return $response;
    }

    public static function generateImage($ImagePrompt){
        return self::callClip($ImagePrompt);
    }
}
// Initialisation de l'API Clip 
ClipdropHelper::init();
