<?php

declare(strict_types=1);

namespace Tests\Epic03\Us12\Unit;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewGroupUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us12_non_members_cannot_read_group_details(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $groupId = (new GroupRepository($this->pdo))->create('Film Night', 'ABC123', $ownerId);

        $this->expectException(RuntimeException::class);
        (new GroupService(new GroupRepository($this->pdo)))->getForMember($groupId, 999);
    }
}