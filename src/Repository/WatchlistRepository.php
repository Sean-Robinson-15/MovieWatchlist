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

    public function aggregateForGroup(int $groupId, string $filter = 'planned'): array
    {
        $sortColumns = [
            'planned' => 'planned_count',
            'watching' => 'watching_count',
            'watched' => 'watched_count',
        ];
        $filter = $filter === 'all' ? 'all' : (array_key_exists($filter, $sortColumns) ? $filter : 'planned');

        $sql = 'SELECT m.id, m.title, m.overview, m.poster_path, m.release_date,
                       SUM(CASE WHEN wi.status = \'planned\' THEN 1 ELSE 0 END) AS planned_count,
                       SUM(CASE WHEN wi.status = \'watching\' THEN 1 ELSE 0 END) AS watching_count,
                       SUM(CASE WHEN wi.status = \'watched\' THEN 1 ELSE 0 END) AS watched_count
                FROM movies m
                JOIN watchlist_items wi ON wi.movie_id = m.id
                JOIN group_memberships gm ON gm.group_id = :group_id AND gm.user_id = wi.user_id
                GROUP BY m.id';

        if ($filter === 'all') {
            $sql .= ' ORDER BY m.title COLLATE NOCASE';
        } else {
            $column = $sortColumns[$filter];
            $sql .= " HAVING {$column} > 0 ORDER BY {$column} DESC, m.title COLLATE NOCASE";
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['group_id' => $groupId]);

        return $statement->fetchAll();
    }
}
