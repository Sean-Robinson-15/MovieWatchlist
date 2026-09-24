<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;
use RuntimeException;

final class Database
{
    public static function connect(Config $config): PDO
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new RuntimeException(
                'SQLite support is not enabled. Enable the pdo_sqlite extension in the active php.ini file.'
            );
        }

        $path = $config->get('db_path');
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        self::migrate($pdo);
        return $pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                version TEXT PRIMARY KEY,
                applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $migrationDirectory = dirname(__DIR__, 2) . '/database/migrations';
        $migrations = glob($migrationDirectory . '/*.sql') ?: [];
        sort($migrations, SORT_STRING);

        foreach ($migrations as $migration) {
            $version = basename($migration, '.sql');
            $statement = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE version = :version');
            $statement->execute(['version' => $version]);

            if ($statement->fetchColumn() !== false) {
                continue;
            }

            $pdo->beginTransaction();
            try {
                $pdo->exec((string) file_get_contents($migration));
                $record = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (:version)');
                $record->execute(['version' => $version]);
                $pdo->commit();
            } catch (\Throwable $error) {
                $pdo->rollBack();
                throw $error;
            }
        }
    }
}
