<?php
namespace Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtHelper {
    private static string $secretKey;

    public static function init(): void {
        self::$secretKey = $_ENV['JWT_SECRET'] ?: "cle_secrete";
    }

    public static function generateToken(array $payload, int $expiry = 3600): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;

        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function decodeToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (ExpiredException $e) {
            // Token expiré
            return null;
        } catch (Exception $e) {
            // Autres exceptions
            return null;
        }
    }
}

// Initialise la clé secrète lors du chargement du script.
JwtHelper::init();
