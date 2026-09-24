<?php

declare(strict_types=1);

namespace Tests\Epic02\Us08\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class UpdateWatchlistAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us08_a_user_can_update_status_rating_and_notes(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $itemId = $this->fixtureWatchlistItem($userId, $this->fixtureMovie(8003, 'Film'));

        (new WatchlistService(new WatchlistRepository($this->pdo)))->update(
            $userId,
            $itemId,
            'watched',
            '4',
            '  Good  '
        );

        $item = (new WatchlistRepository($this->pdo))->allForUser($userId)[0];
        $this->assertSame('watched', $item['status']);
        $this->assertSame(4, (int) $item['rating']);
        $this->assertSame('Good', $item['notes']);
    }
}