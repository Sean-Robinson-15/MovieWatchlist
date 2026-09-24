<?php

declare(strict_types=1);

namespace Tests\Epic03\Us13\Acceptance;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use Tests\Support\IsolatedDatabaseTestCase;

final class LeaveGroupAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us13_a_member_can_leave_without_removing_the_group(): void
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
}