<?php

declare(strict_types=1);

namespace Tests\Epic02\Us06\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewWatchlistAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us06_a_user_can_view_movie_details_from_their_watchlist(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $movieId = $this->fixtureMovie(6003, 'Film', '2024-06-01');
        $this->fixtureWatchlistItem($userId, $movieId, 'watching');

        $item = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId)[0];

        $this->assertSame('Film', $item['title']);
        $this->assertSame('2024-06-01', $item['release_date']);
        $this->assertSame('watching', $item['status']);
    }
}