<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Delete\Unit;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class DeleteGroupUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us14_delete_only_allows_the_owner(): void
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