<?php
namespace Middleware;

use Helpers\JwtHelper;
use Core\Controller;

class AuthMiddleware extends Controller {
    public static function handle(): void {
        $controller = new Controller();

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Vérification de la présence du token
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $controller->unauthorizedResponse('Token invalide');
            exit;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = JwtHelper::decodeToken($token);

        // Vérification de la validité du token
        if (!$decoded) {
            $controller->unauthorizedResponse('Token invalide');
            exit;
        }

        // Ajouter les informations de l'utilisateur dans les headers de la requête
        foreach ($decoded as $key => $value) {
            $_SERVER['X-User-' . str_replace('_', '-', ucfirst($key))] = $value;
        }
    }
}
