<?php

declare(strict_types=1);

namespace Tests\Epic03\Us13\Integration;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class LeaveGroupTest extends IsolatedDatabaseTestCase
{
    public function test_us13_ac01_through_ac04_removes_a_member_from_the_group(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);
        $groups->addMember($groupId, $memberId);

        (new GroupService($groups))->leave($groupId, $memberId);

        $this->assertNull($groups->findForMember($groupId, $memberId));
        $this->assertNotNull($groups->findForMember($groupId, $ownerId));
    }

    public function test_us13_ac02_rejects_the_owner_leaving(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The group owner cannot leave the group yet.');
        (new GroupService($groups))->leave($groupId, $ownerId);
    }

    public function test_us13_ac05_rejects_a_non_member_leaving(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $outsiderId = $this->fixtureUser('outsider@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You are not a member of that group.');
        (new GroupService($groups))->leave($groupId, $outsiderId);
    }
}