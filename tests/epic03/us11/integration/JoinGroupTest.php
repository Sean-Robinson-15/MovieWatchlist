<?php

declare(strict_types=1);

namespace Tests\Epic03\Us11\Integration;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class JoinGroupTest extends IsolatedDatabaseTestCase
{
    public function test_us11_ac01_through_ac05_joins_using_a_case_insensitive_code(): void
    {
        $ownerId = $this->fixtureUser('owner@example.com');
        $memberId = $this->fixtureUser('member@example.com');
        $groups = new GroupRepository($this->pdo);
        $groupId = $groups->create('Film Night', 'ABC123', $ownerId);

        $joinedId = (new GroupService($groups))->join($memberId, ' abc123 ');
        $group = $groups->findForMember($groupId, $memberId);

        $this->assertSame($groupId, $joinedId);
        $this->assertNotNull($group);
        $this->assertSame('member', $group['role']);
    }

    public function test_us11_ac06_rejects_an_unknown_join_code(): void
    {
        $memberId = $this->fixtureUser('member@example.com');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('That group code is not valid.');

        (new GroupService(new GroupRepository($this->pdo)))->join($memberId, 'missing');
    }
}