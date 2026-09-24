<?php

declare(strict_types=1);

namespace Tests\Epic02\Us04\Unit;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use PHPUnit\Framework\TestCase;

final class MovieSearchUnitTest extends TestCase
{
    public function test_us04_an_empty_query_does_not_call_tmdb(): void
    {
        $client = new TmdbClient(new Config(sys_get_temp_dir() . '/missing-movie-watchlist-config'));

        $this->assertSame([], $client->search(''));
    }
}