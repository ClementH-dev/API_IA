<?php
use Dotenv\Dotenv;

// Charger automatiquement les fichiers si non gérés par Composer
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Charger le routeur
require_once __DIR__ . '/../routes/api.php';
