<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\DatabaseSeeder;
use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
$username = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASSWORD'];

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie à la base de données.\n";

    $migrationsPath = __DIR__ . '/../database/migrations/';
    $seeder = new DatabaseSeeder($db, $migrationsPath);

    $specificMigration = $argv[1] ?? null;

    if ($specificMigration) {
        $seeder->runSingle($specificMigration);
    } else {
        $seeder->run();
    }

} catch (Exception $e) {
    die("Erreur lors du lancement des migrations : " . $e->getMessage() . "\n");
}
