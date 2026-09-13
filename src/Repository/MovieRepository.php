<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class MovieRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findOrCreate(array $movie): int
    {
        $existing = $this->pdo->prepare('SELECT id FROM movies WHERE tmdb_id = :tmdb_id');
        $existing->execute(['tmdb_id' => $movie['tmdb_id']]);
        $id = $existing->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO movies (tmdb_id, title, overview, poster_path, release_date)
             VALUES (:tmdb_id, :title, :overview, :poster_path, :release_date)'
        );
        $statement->execute([
            'tmdb_id' => $movie['tmdb_id'],
            'title' => $movie['title'],
            'overview' => $movie['overview'],
            'poster_path' => $movie['poster_path'],
            'release_date' => $movie['release_date'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM movies WHERE id = :id');
        $statement->execute(['id' => $id]);
        $movie = $statement->fetch();

        return $movie ?: null;
    }
}
