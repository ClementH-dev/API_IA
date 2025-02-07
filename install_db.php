<?php
// Paramètres de connexion MySQL
$host = 'localhost'; 
$user = 'root';
$password = '';
$database = 'chat_api'; 
try {
    // Connexion sans base sélectionnée (obligatoire pour la créer)
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Création de la base si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Base de données '$database' créée ou déjà existante. ✅<br>";

    // Se reconnecter avec la base sélectionnée
    $pdo->exec("USE `$database`;");

    // Lire le fichier SQL
    $sqlFile = file_get_contents(__DIR__ . '/database/backup.sql');
    if ($sqlFile === false) {
        throw new Exception("Impossible de lire le fichier SQL.");
    }

    // Découper et exécuter chaque requête SQL (au cas où il y a plusieurs requêtes)
    $queries = explode(";", $sqlFile);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }

    echo "Base de données importée avec succès ! ✅";

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
