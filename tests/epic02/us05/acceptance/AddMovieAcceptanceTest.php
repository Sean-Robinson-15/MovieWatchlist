<?php

declare(strict_types=1);

namespace Tests\Epic02\Us05\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class AddMovieAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us05_an_authenticated_user_gets_a_planned_watchlist_item(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $movieId = $this->fixtureMovie(5002, 'Film');

        (new WatchlistService(new WatchlistRepository($this->pdo)))->add($userId, $movieId);

        $items = (new WatchlistRepository($this->pdo))->allForUser($userId);
        $this->assertCount(1, $items);
        $this->assertSame('planned', $items[0]['status']);
    }
}