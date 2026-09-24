<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class IsolatedDatabaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');

        foreach (glob(__DIR__ . '/../../database/migrations/*.sql') ?: [] as $migration) {
            $this->pdo->exec((string) file_get_contents($migration));
        }
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
        parent::tearDown();
    }

    protected function fixtureUser(string $email, string $password = 'password123'): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash) VALUES (?, ?)'
        );
        $statement->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function fixtureMovie(int $tmdbId, string $title, string $releaseDate = '2024-01-01'): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO movies (tmdb_id, title, release_date) VALUES (?, ?, ?)'
        );
        $statement->execute([$tmdbId, $title, $releaseDate]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function fixtureWatchlistItem(int $userId, int $movieId, string $status = 'planned'): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (?, ?, ?)'
        );
        $statement->execute([$userId, $movieId, $status]);

        return (int) $this->pdo->lastInsertId();
    }
}
