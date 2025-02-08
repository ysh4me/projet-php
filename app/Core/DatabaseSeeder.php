<?php

namespace App\Core;

use PDO;

class DatabaseSeeder
{
    private PDO $db;
    private string $migrationsPath;

    public function __construct(PDO $db, string $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    public function run(): void
    {
        $files = glob($this->migrationsPath . '*.sql');

        if (!$files) {
            echo "Aucune migration trouvée dans le dossier : {$this->migrationsPath}\n";
            return;
        }

        foreach ($files as $file) {
            $this->executeMigration($file);
        }

        echo "Toutes les migrations ont été exécutées avec succès !\n";
    }

    public function runSingle(string $migration): void
    {
        $filePath = $this->migrationsPath . $migration;

        if (!file_exists($filePath)) {
            echo "La migration spécifiée n'existe pas : $migration\n";
            return;
        }

        $this->executeMigration($filePath);
    }

    private function executeMigration(string $file): void
    {
        try {
            $sql = file_get_contents($file);
            $this->db->exec($sql);
            echo "Migration exécutée : " . basename($file) . "\n";
        } catch (\Exception $e) {
            echo "Erreur lors de l'exécution de la migration " . basename($file) . " : " . $e->getMessage() . "\n";
        }
    }
}
