<?php

declare(strict_types=1);

namespace Tests\Epic04\Us15\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ErrorHandlingAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us15_invalid_watchlist_status_is_explained_without_sql_details(): void
    {
        try {
            (new WatchlistService(new WatchlistRepository($this->pdo)))->update(1, 1, 'broken', '', '');
            $this->fail('Expected invalid input to fail.');
        } catch (RuntimeException $error) {
            $this->assertSame('Choose a valid watch status.', $error->getMessage());
            $this->assertStringNotContainsString('SQL', $error->getMessage());
        }
    }
}