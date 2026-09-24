<?php

declare(strict_types=1);

namespace Tests;

use App\Repository\GroupRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use App\Service\GroupService;
use App\Service\VoteService;
use PHPUnit\Framework\TestCase;
use PDO;

final class GroupFeatureTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(file_get_contents(__DIR__ . '/../database/migrations/001_initial.sql'));
        $this->pdo->exec(file_get_contents(__DIR__ . '/../database/migrations/002_groups_and_votes.sql'));
        $this->pdo->exec("INSERT INTO users (email, password_hash) VALUES ('one@example.com', 'x'), ('two@example.com', 'x')");
        $this->pdo->exec("INSERT INTO movies (tmdb_id, title) VALUES (1, 'Beta'), (2, 'Alpha')");
        $this->pdo->exec("INSERT INTO groups (name, join_code, created_by) VALUES ('Film Club', 'ABC123', 1)");
        $this->pdo->exec("INSERT INTO group_memberships (group_id, user_id, role) VALUES (1, 1, 'owner'), (1, 2, 'member')");
    }

    public function testStatusFiltersCountMembersAndSortByCount(): void
    {
        $this->pdo->exec("INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (1, 1, 'planned'), (2, 1, 'planned'), (1, 2, 'watching')");
        $repository = new WatchlistRepository($this->pdo);

        $planned = $repository->aggregateForGroup(1, 'planned');
        $all = $repository->aggregateForGroup(1, 'all');

        self::assertSame('Beta', $planned[0]['title']);
        self::assertSame(2, (int) $planned[0]['planned_count']);
        self::assertSame(['Alpha', 'Beta'], array_column($all, 'title'));
    }

    public function testInvalidStatusFilterDefaultsToPlannedSafely(): void
    {
        $this->pdo->exec("INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (1, 1, 'planned'), (1, 2, 'watching')");
        $repository = new WatchlistRepository($this->pdo);

        self::assertSame(['Beta'], array_column($repository->aggregateForGroup(1, ''), 'title'));
        self::assertSame(['Beta'], array_column($repository->aggregateForGroup(1, 'invalid'), 'title'));
    }

    public function testGroupCreationAddsOwnerMembershipAndJoinIsIdempotent(): void
    {
        $repository = new GroupRepository($this->pdo);
        $groupId = $repository->create('Second Club', 'XYZ789', 1);
        $repository->addMember($groupId, 2);
        $repository->addMember($groupId, 2);

        $groups = $repository->allForUser(2);
        self::assertCount(2, $groups);
        self::assertSame(2, (int) $groups[1]['member_count']);
    }

    public function testEachMemberHasOneVoteAndCanMoveIt(): void
    {
        $this->pdo->exec("INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (1, 1, 'planned'), (2, 2, 'planned')");
        $repository = new VoteRepository($this->pdo);
        $repository->set(1, 1, 1);
        $repository->set(1, 1, 2);

        self::assertSame(2, $repository->currentMovieId(1, 1));
        self::assertSame(1, (int) $repository->countsForGroup(1, 1)[0]['vote_count']);
    }

    public function testVoteServiceTogglesAndRejectsMoviesOutsideTheGroup(): void
    {
        $this->pdo->exec("INSERT INTO watchlist_items (user_id, movie_id, status) VALUES (1, 1, 'planned')");
        $groupRepository = new GroupRepository($this->pdo);
        $voteService = new VoteService(
            new VoteRepository($this->pdo),
            $groupRepository,
            new WatchlistRepository($this->pdo)
        );

        $voteService->toggle(1, 1, 1);
        $voteService->toggle(1, 1, 1);

        self::assertNull((new VoteRepository($this->pdo))->currentMovieId(1, 1));
        $this->expectException(\RuntimeException::class);
        $voteService->toggle(1, 1, 2);
    }

    public function testMemberCanLeaveButOwnerCannot(): void
    {
        $service = new GroupService(new GroupRepository($this->pdo));

        $service->leave(1, 2);
        self::assertNull((new GroupRepository($this->pdo))->findForMember(1, 2));

        $this->expectException(\RuntimeException::class);
        $service->leave(1, 1);
    }
}
