<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Vote\Integration;

use App\Repository\GroupRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use App\Service\VoteService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class VoteOnFilmTest extends IsolatedDatabaseTestCase
{
    public function test_us14_vote_ac04_and_ac05_allows_one_vote_and_toggles_it_off(): void
    {
        [$groupId, $ownerId, $movieId] = $this->createGroupWithMovie();
        $service = $this->service();

        $service->toggle($groupId, $ownerId, $movieId);
        $this->assertSame($movieId, (new VoteRepository($this->pdo))->currentMovieId($groupId, $ownerId));

        $service->toggle($groupId, $ownerId, $movieId);
        $this->assertNull((new VoteRepository($this->pdo))->currentMovieId($groupId, $ownerId));
    }

    public function test_us14_vote_ac06_rejects_movies_outside_the_shared_pool(): void
    {
        [$groupId, $ownerId] = $this->createGroupWithMovie();
        $outsideMovieId = $this->createMovie(999, 'Outside Film');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('That movie is not part of this group.');

        $this->service()->toggle($groupId, $ownerId, $outsideMovieId);
    }

    private function service(): VoteService
    {
        return new VoteService(
            new VoteRepository($this->pdo),
            new GroupRepository($this->pdo),
            new WatchlistRepository($this->pdo)
        );
    }

    private function createGroupWithMovie(): array
    {
        $ownerId = $this->createUser('owner@example.com');
        $movieId = $this->createMovie(501, 'Shared Film');
        $groupId = (new GroupRepository($this->pdo))->create('Film Night', 'ABC123', $ownerId);
        $this->pdo->prepare(
            'INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (?, ?, ?)'
        )->execute([$ownerId, $movieId, 'planned']);
        return [$groupId, $ownerId, $movieId];
    }

    private function createUser(string $email): int
    {
        $statement = $this->pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $statement->execute([$email, password_hash('password123', PASSWORD_DEFAULT)]);
        return (int) $this->pdo->lastInsertId();
    }

    private function createMovie(int $tmdbId, string $title): int
    {
        $statement = $this->pdo->prepare('INSERT INTO movies (tmdb_id, title) VALUES (?, ?)');
        $statement->execute([$tmdbId, $title]);
        return (int) $this->pdo->lastInsertId();
    }
}
