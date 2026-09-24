<?php

declare(strict_types=1);

namespace Tests\Epic02\Us05\Unit;

use App\Repository\MovieRepository;
use Tests\Support\IsolatedDatabaseTestCase;

final class AddMovieUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us05_movie_repository_reuses_the_tmdb_id(): void
    {
        $repository = new MovieRepository($this->pdo);
        $movie = [
            'tmdb_id' => 5001,
            'title' => 'Film',
            'overview' => '',
            'poster_path' => null,
            'release_date' => null,
        ];

        $this->assertSame($repository->findOrCreate($movie), $repository->findOrCreate($movie));
    }
}