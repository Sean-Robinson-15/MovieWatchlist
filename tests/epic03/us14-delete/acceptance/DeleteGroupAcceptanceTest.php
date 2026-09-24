<?php

declare(strict_types=1);

namespace Tests\Epic03\Us14Delete\Acceptance;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use Tests\Support\IsolatedDatabaseTestCase;

final class DeleteGroupAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us14_delete_an_owner_can_remove_the_group(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        (new GroupService($groups))->delete($groupId, $ownerId);

        $this->assertNull($groups->findForMember($groupId, $ownerId));
    }
}