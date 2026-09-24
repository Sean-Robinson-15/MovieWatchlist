<?php

declare(strict_types=1);

namespace Tests\Epic02\Us08\Unit;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class UpdateWatchlistUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us08_rejects_ratings_outside_one_to_five(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rating must be between 1 and 5.');

        (new WatchlistService(new WatchlistRepository($this->pdo)))->update(1, 1, 'planned', '0', '');
    }
}