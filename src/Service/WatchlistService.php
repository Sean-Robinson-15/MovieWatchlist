<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\WatchlistRepository;
use RuntimeException;

final class WatchlistService
{
    public function __construct(private WatchlistRepository $watchlist)
    {
    }

    public function list(int $userId, ?string $status = null): array
    {
        return $this->watchlist->allForUser($userId, $status);
    }

    public function add(int $userId, int $movieId): void
    {
        $this->watchlist->add($userId, $movieId);
    }

    public function update(int $userId, int $itemId, string $status, string $rating, string $notes): void
    {
        if (!in_array($status, ['planned', 'watching', 'watched'], true)) {
            throw new RuntimeException('Choose a valid watch status.');
        }

        $parsedRating = $rating === '' ? null : (int) $rating;
        if ($parsedRating !== null && ($parsedRating < 1 || $parsedRating > 5)) {
            throw new RuntimeException('Rating must be between 1 and 5.');
        }

        $this->watchlist->update($userId, $itemId, $status, $parsedRating, trim($notes));
    }

    public function remove(int $userId, int $itemId): void
    {
        $this->watchlist->remove($userId, $itemId);
    }
}
