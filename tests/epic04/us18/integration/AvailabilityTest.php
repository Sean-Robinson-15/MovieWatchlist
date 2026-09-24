<?php

declare(strict_types=1);

namespace Tests\Epic04\Us18\Integration;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class AvailabilityTest extends IsolatedDatabaseTestCase
{
    public function test_us18_ac01_and_ac02_converts_tmdb_failure_to_a_safe_message(): void
    {
        $root = sys_get_temp_dir() . '/movie-watchlist-unavailable-' . bin2hex(random_bytes(4));
        mkdir($root, 0777, true);
        file_put_contents($root . '/.env', "TMDB_API_KEY=test-key\nTMDB_BASE_URL=http://127.0.0.1:1\n");

        try {
            (new TmdbClient(new Config($root)))->search('Unavailable Film');
            $this->fail('Expected the unavailable TMDB service to fail.');
        } catch (RuntimeException $error) {
            $this->assertSame('Movie search is temporarily unavailable.', $error->getMessage());
            $this->assertStringNotContainsString('127.0.0.1', $error->getMessage());
        } finally {
            unlink($root . '/.env');
            rmdir($root);
        }
    }

    public function test_us18_ac03_and_ac04_watchlist_data_remains_available_without_tmdb(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $movieId = $this->fixtureMovie(18001, 'Existing Film');
        $this->fixtureWatchlistItem($userId, $movieId);

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId);

        $this->assertCount(1, $items);
        $this->assertSame('Existing Film', $items[0]['title']);
    }
}