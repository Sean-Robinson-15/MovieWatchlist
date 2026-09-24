<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Vote\Acceptance;

use App\Repository\GroupRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use App\Service\VoteService;
use Tests\Support\IsolatedDatabaseTestCase;

final class VoteOnFilmAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us14_vote_a_member_can_select_and_unselect_a_shared_film(): void
    {
        $userId = $this->fixtureUser('owner@example.com');
        $movieId = $this->fixtureMovie(14002, 'Film');
        $groupId = (new GroupRepository($this->pdo))->create('Film Night', 'ABC123', $userId);
        $this->fixtureWatchlistItem($userId, $movieId);
        $service = new VoteService(
            new VoteRepository($this->pdo),
            new GroupRepository($this->pdo),
            new WatchlistRepository($this->pdo)
        );

        $service->toggle($groupId, $userId, $movieId);
        $this->assertSame($movieId, (new VoteRepository($this->pdo))->currentMovieId($groupId, $userId));
        $service->toggle($groupId, $userId, $movieId);
        $this->assertNull((new VoteRepository($this->pdo))->currentMovieId($groupId, $userId));
    }
}