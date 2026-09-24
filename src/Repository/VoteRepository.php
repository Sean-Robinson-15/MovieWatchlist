<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class VoteRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function currentMovieId(int $groupId, int $userId): ?int
    {
        $statement = $this->pdo->prepare(
            'SELECT movie_id FROM group_votes WHERE group_id = :group_id AND user_id = :user_id'
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId]);
        $movieId = $statement->fetchColumn();

        return $movieId === false ? null : (int) $movieId;
    }

    public function set(int $groupId, int $userId, int $movieId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO group_votes (group_id, user_id, movie_id) VALUES (:group_id, :user_id, :movie_id)
             ON CONFLICT(group_id, user_id) DO UPDATE SET movie_id = excluded.movie_id, updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId, 'movie_id' => $movieId]);
    }

    public function remove(int $groupId, int $userId): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM group_votes WHERE group_id = :group_id AND user_id = :user_id'
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId]);
    }

    public function countsForGroup(int $groupId, int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT m.id, m.title, m.overview, m.poster_path, m.release_date,
                    COUNT(gv.user_id) AS vote_count,
                    CASE WHEN EXISTS (
                        SELECT 1 FROM group_votes own_vote
                        WHERE own_vote.group_id = :group_id AND own_vote.user_id = :user_id
                          AND own_vote.movie_id = m.id
                    ) THEN 1 ELSE 0 END AS voted
             FROM movies m
             JOIN watchlist_items wi ON wi.movie_id = m.id
             JOIN group_memberships gm ON gm.group_id = :group_id AND gm.user_id = wi.user_id
             LEFT JOIN group_votes gv ON gv.group_id = :group_id AND gv.movie_id = m.id
             GROUP BY m.id
             ORDER BY vote_count DESC, m.title COLLATE NOCASE'
        );
        $statement->execute([
            'group_id' => $groupId,
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }
}
