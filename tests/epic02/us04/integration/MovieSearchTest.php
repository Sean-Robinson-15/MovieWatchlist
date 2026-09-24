<?php

declare(strict_types=1);

namespace Tests\Epic02\Us04\Integration;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use Tests\Support\IsolatedDatabaseTestCase;

final class MovieSearchTest extends IsolatedDatabaseTestCase
{
    public function test_us04_ac09_returns_no_results_for_an_empty_search(): void
    {
        $client = new TmdbClient(new Config(sys_get_temp_dir() . '/movie-watchlist-test'));

        $this->assertSame([], $client->search('   '));
    }

    public function test_us04_ac11_returns_no_results_when_tmdb_is_not_configured(): void
    {
        $client = new TmdbClient(new Config(sys_get_temp_dir() . '/movie-watchlist-test'));

        $this->assertSame([], $client->search('The Matrix'));
    }
}