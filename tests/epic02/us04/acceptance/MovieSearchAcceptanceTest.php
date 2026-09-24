<?php

declare(strict_types=1);

namespace Tests\Epic02\Us04\Acceptance;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use Tests\Support\IsolatedDatabaseTestCase;

final class MovieSearchAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us04_a_search_without_external_configuration_is_safe(): void
    {
        $service = new MovieService(
            new TmdbClient(new Config(sys_get_temp_dir() . '/missing-movie-watchlist-config')),
            new MovieRepository($this->pdo)
        );

        $this->assertSame([], $service->search('Film'));
    }
}