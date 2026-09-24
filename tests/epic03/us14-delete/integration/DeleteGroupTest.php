<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Delete\Integration;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class DeleteGroupTest extends IsolatedDatabaseTestCase
{
    public function test_us14_delete_ac01_through_ac04_deletes_the_group_and_memberships(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);
        $groups->addMember($groupId, $memberId);

        (new GroupService($groups))->delete($groupId, $ownerId);

        $this->assertNull($groups->findForMember($groupId, $ownerId));
        $this->assertNull($groups->findForMember($groupId, $memberId));
        $this->assertFalse((bool) $this->pdo->query("SELECT 1 FROM groups WHERE id = {$groupId}")->fetchColumn());
    }

    public function test_us14_delete_ac01_and_ac05_rejects_non_owners(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);
        $groups->addMember($groupId, $memberId);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only the group owner can delete this group.');
        (new GroupService($groups))->delete($groupId, $memberId);
    }
}