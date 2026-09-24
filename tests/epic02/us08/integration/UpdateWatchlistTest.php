<?php

declare(strict_types=1);

namespace Tests\Epic02\Us08\Integration;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class UpdateWatchlistTest extends IsolatedDatabaseTestCase
{
    public function test_us08_ac01_through_ac04_updates_status_rating_and_trimmed_notes(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $itemId = $this->fixtureWatchlistItem($userId, $this->fixtureMovie(7001, 'Film'));
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $service->update($userId, $itemId, 'watched', '5', '  Excellent film  ');
        $item = (new WatchlistRepository($this->pdo))->allForUser($userId)[0];

        $this->assertSame('watched', $item['status']);
        $this->assertSame(5, (int) $item['rating']);
        $this->assertSame('Excellent film', $item['notes']);
    }

    public function test_us08_ac05_through_ac09_rejects_invalid_values_and_other_users_items(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $itemId = $this->fixtureWatchlistItem($otherUserId, $this->fixtureMovie(7002, 'Film'));
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Choose a valid watch status.');
        $service->update($userId, $itemId, 'invalid', '', 'notes');
    }
}