<?php

declare(strict_types=1);

namespace Tests\Epic03\Us12\Integration;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewGroupTest extends IsolatedDatabaseTestCase
{
    public function test_us12_ac01_through_ac05_returns_group_details_and_members(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);
        $groups->addMember($groupId, $memberId);
        $service = new GroupService($groups);

        $group = $service->getForMember($groupId, $memberId);
        $members = $service->members($groupId, $memberId);

        $this->assertSame('Film Night', $group['name']);
        $this->assertSame('ABC123', $group['join_code']);
        $this->assertSame(2, (int) $group['member_count']);
        $this->assertSame(['owner', 'member'], array_column($members, 'role'));
    }

    public function test_us12_ac06_and_ac07_blocks_non_members(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $outsiderId = $this->fixtureUser('outsider@example.com');
        $groupId = (new GroupRepository($this->pdo))->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You are not a member of that group.');

        (new GroupService(new GroupRepository($this->pdo)))->getForMember($groupId, $outsiderId);
    }
}