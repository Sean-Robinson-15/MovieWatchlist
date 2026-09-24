<?php

declare(strict_types=1);

namespace Tests\Epic03\Us10\Acceptance;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use Tests\Support\IsolatedDatabaseTestCase;

final class CreateGroupAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us10_a_user_creates_a_group_and_becomes_its_owner(): void
    {
        $userId = $this->fixtureUser('owner@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = (new GroupService($groups))->create($userId, 'Film Night');
        $group = $groups->findForMember($groupId, $userId);

        $this->assertNotNull($group);
        $this->assertSame('owner', $group['role']);
        $this->assertSame(6, strlen($group['join_code']));
    }
}