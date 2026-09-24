<?php

declare(strict_types=1);

namespace Tests\Epic03\Us13\Unit;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class LeaveGroupUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us13_the_owner_cannot_leave_the_group(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The group owner cannot leave the group yet.');
        (new GroupService($groups))->leave($groupId, $ownerId);
    }
}