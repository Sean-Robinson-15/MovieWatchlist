<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\GroupRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use RuntimeException;

final class VoteService
{
    public function __construct(
        private VoteRepository $votes,
        private GroupRepository $groups,
        private WatchlistRepository $watchlist
    )
    {
    }

    public function listForGroup(int $groupId, int $userId): array
    {
        $this->assertMember($groupId, $userId);
        return $this->votes->countsForGroup($groupId, $userId);
    }

    public function toggle(int $groupId, int $userId, int $movieId): void
    {
        $this->assertMember($groupId, $userId);
        $groupMovies = $this->watchlist->aggregateForGroup($groupId, 'all');
        if (!in_array($movieId, array_map(static fn (array $movie): int => (int) $movie['id'], $groupMovies), true)) {
            throw new RuntimeException('That movie is not part of this group.');
        }
        $currentMovieId = $this->votes->currentMovieId($groupId, $userId);

        if ($currentMovieId === $movieId) {
            $this->votes->remove($groupId, $userId);
            return;
        }

        $this->votes->set($groupId, $userId, $movieId);
    }

    private function assertMember(int $groupId, int $userId): void
    {
        if ($this->groups->findForMember($groupId, $userId) === null) {
            throw new \RuntimeException('You are not a member of that group.');
        }
    }
}
