<?php

declare(strict_types=1);

namespace Tests\Epic03\Us11\Acceptance;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use Tests\Support\IsolatedDatabaseTestCase;

final class JoinGroupAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us11_a_user_can_join_a_group_with_its_shareable_code(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        $this->assertSame($groupId, (new GroupService($groups))->join($memberId, 'abc123'));
        $this->assertSame('member', $groups->findForMember($groupId, $memberId)['role']);
    }
}