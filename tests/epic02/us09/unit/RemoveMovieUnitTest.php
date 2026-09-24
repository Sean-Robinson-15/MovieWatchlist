<?php

declare(strict_types=1);

namespace Tests\Epic02\Us09\Unit;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class RemoveMovieUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us09_removing_a_missing_item_is_idempotent(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');

        (new WatchlistService(new WatchlistRepository($this->pdo)))->remove($userId, 9999);

        $this->assertSame([], (new WatchlistRepository($this->pdo))->allForUser($userId));
    }
}