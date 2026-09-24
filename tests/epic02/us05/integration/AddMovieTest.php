<?php

declare(strict_types=1);

namespace Tests\Epic02\Us05\Integration;

use App\Infrastructure\Config;
use App\Infrastructure\TmdbClient;
use App\Repository\MovieRepository;
use App\Repository\WatchlistRepository;
use App\Service\MovieService;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class AddMovieTest extends IsolatedDatabaseTestCase
{
    public function test_us05_ac03_through_ac08_saves_one_movie_and_one_planned_item(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $movies = new MovieRepository($this->pdo);
        $movieService = new MovieService(
            new TmdbClient(new Config(sys_get_temp_dir() . '/movie-watchlist-test')),
            $movies
        );
        $movie = [
            'tmdb_id' => 9001,
            'title' => 'A Shared Film',
            'overview' => 'An overview.',
            'poster_path' => null,
            'release_date' => '2024-05-01',
        ];

        $movieId = $movieService->save($movie);
        $sameMovieId = $movieService->save($movie);
        $watchlist = new WatchlistService(new WatchlistRepository($this->pdo));
        $watchlist->add($userId, $movieId);
        $watchlist->add($userId, $sameMovieId);

        $items = $watchlist->list($userId);
        $this->assertSame($movieId, $sameMovieId);
        $this->assertCount(1, $items);
        $this->assertSame('planned', $items[0]['status']);
        $this->assertSame('A Shared Film', $items[0]['title']);
    }
}