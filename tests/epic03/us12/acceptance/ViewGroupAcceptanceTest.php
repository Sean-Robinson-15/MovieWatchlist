<?php

declare(strict_types=1);

namespace Tests\Epic03\Us12\Acceptance;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewGroupAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us12_a_member_can_view_the_group_and_member_roles(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);
        $groups->addMember($groupId, $memberId);

        $members = (new GroupService($groups))->members($groupId, $memberId);

        $this->assertCount(2, $members);
        $this->assertSame(['owner', 'member'], array_column($members, 'role'));
    }
}