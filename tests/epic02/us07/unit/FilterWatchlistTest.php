<?php

declare(strict_types=1);

namespace Tests\Epic02\Us07\Unit;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class FilterWatchlistTest extends IsolatedDatabaseTestCase
{
    public function test_us07_ac01_and_ac02_returns_only_planned_items_for_the_current_user(): void
    {
        $firstUser = $this->createUser('first@example.com');
        $secondUser = $this->createUser('second@example.com');
        $plannedMovie = $this->createMovie(101, 'Planned Film');
        $watchingMovie = $this->createMovie(102, 'Watching Film');
        $otherUsersMovie = $this->createMovie(103, 'Private Film');
        $this->addWatchlistItem($firstUser, $plannedMovie, 'planned');
        $this->addWatchlistItem($firstUser, $watchingMovie, 'watching');
        $this->addWatchlistItem($secondUser, $otherUsersMovie, 'planned');

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($firstUser, 'planned');

        $this->assertCount(1, $items);
        $this->assertSame('Planned Film', $items[0]['title']);
    }

    public function test_us07_ac03_and_ac04_supports_each_status_and_ignores_invalid_filters(): void
    {
        $userId = $this->createUser('viewer@example.com');
        $this->addWatchlistItem($userId, $this->createMovie(201, 'Planned'), 'planned');
        $this->addWatchlistItem($userId, $this->createMovie(202, 'Watching'), 'watching');
        $this->addWatchlistItem($userId, $this->createMovie(203, 'Watched'), 'watched');
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $this->assertCount(1, $service->list($userId, 'watching'));
        $this->assertCount(1, $service->list($userId, 'watched'));
        $this->assertCount(3, $service->list($userId, 'unexpected'));
    }

    private function createUser(string $email): int
    {
        $statement = $this->pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $statement->execute([$email, password_hash('password123', PASSWORD_DEFAULT)]);
        return (int) $this->pdo->lastInsertId();
    }

    private function createMovie(int $tmdbId, string $title): int
    {
        $statement = $this->pdo->prepare('INSERT INTO movies (tmdb_id, title) VALUES (?, ?)');
        $statement->execute([$tmdbId, $title]);
        return (int) $this->pdo->lastInsertId();
    }

    private function addWatchlistItem(int $userId, int $movieId, string $status): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (?, ?, ?)'
        );
        $statement->execute([$userId, $movieId, $status]);
    }
}
