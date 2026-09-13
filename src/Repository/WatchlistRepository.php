<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class WatchlistRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allForUser(int $userId, ?string $status = null): array
    {
        $sql = 'SELECT wi.*, m.title, m.overview, m.poster_path, m.release_date
                FROM watchlist_items wi JOIN movies m ON m.id = wi.movie_id
                WHERE wi.user_id = :user_id';
        $parameters = ['user_id' => $userId];

        if ($status !== null && in_array($status, ['planned', 'watching', 'watched'], true)) {
            $sql .= ' AND wi.status = :status';
            $parameters['status'] = $status;
        }

        $sql .= ' ORDER BY wi.updated_at DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function add(int $userId, int $movieId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT OR IGNORE INTO watchlist_items (user_id, movie_id) VALUES (:user_id, :movie_id)'
        );
        $statement->execute(['user_id' => $userId, 'movie_id' => $movieId]);
    }

    public function update(int $userId, int $itemId, string $status, ?int $rating, string $notes): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE watchlist_items SET status = :status, rating = :rating, notes = :notes,
             updated_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'status' => $status,
            'rating' => $rating,
            'notes' => $notes,
            'id' => $itemId,
            'user_id' => $userId,
        ]);
    }

    public function remove(int $userId, int $itemId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM watchlist_items WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $itemId, 'user_id' => $userId]);
    }
}
