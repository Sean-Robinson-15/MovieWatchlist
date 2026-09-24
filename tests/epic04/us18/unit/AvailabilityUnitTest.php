<?php

declare(strict_types=1);

namespace Tests\Epic04\Us18\Unit;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use PHPUnit\Framework\TestCase;

final class AvailabilityUnitTest extends TestCase
{
    public function test_us18_search_without_an_api_key_returns_an_empty_result(): void
    {
        $client = new TmdbClient(new Config(sys_get_temp_dir() . '/missing-movie-watchlist-config'));

        $this->assertSame([], $client->search('Film'));
    }
}