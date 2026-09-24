<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Vote\Unit;

use App\Repository\GroupRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use App\Service\VoteService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class VoteOnFilmUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us14_vote_non_members_cannot_vote(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $movieId = $this->fixtureMovie(14001, 'Film');
        $groupId = (new GroupRepository($this->pdo))->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        (new VoteService(
            new VoteRepository($this->pdo),
            new GroupRepository($this->pdo),
            new WatchlistRepository($this->pdo)
        ))->toggle($groupId, 999, $movieId);
    }
}