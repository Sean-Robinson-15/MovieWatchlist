<?php

declare(strict_types=1);

namespace Tests\Epic02\Us06\Integration;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewWatchlistTest extends IsolatedDatabaseTestCase
{
    public function test_us06_ac02_through_ac05_lists_only_the_current_users_items(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $ownedMovieId = $this->fixtureMovie(6001, 'Owned Film', '2023-03-01');
        $privateMovieId = $this->fixtureMovie(6002, 'Private Film', '2023-04-01');
        $this->fixtureWatchlistItem($userId, $ownedMovieId, 'watching');
        $this->fixtureWatchlistItem($otherUserId, $privateMovieId);

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId);

        $this->assertCount(1, $items);
        $this->assertSame('Owned Film', $items[0]['title']);
        $this->assertSame('2023-03-01', $items[0]['release_date']);
        $this->assertSame('watching', $items[0]['status']);
    }
}