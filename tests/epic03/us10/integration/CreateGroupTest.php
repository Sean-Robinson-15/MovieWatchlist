<?php

declare(strict_types=1);

namespace Tests\Epic03\Us10\Integration;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class CreateGroupTest extends IsolatedDatabaseTestCase
{
    public function test_us10_ac01_through_ac05_creates_group_and_owner_membership(): void
    {
        $userId = $this->createUser('owner@example.com');
        $groupId = (new GroupService(new GroupRepository($this->pdo)))->create($userId, 'Film Night');
        $group = (new GroupRepository($this->pdo))->findForMember($groupId, $userId);

        $this->assertNotNull($group);
        $this->assertSame('Film Night', $group['name']);
        $this->assertSame(6, strlen($group['join_code']));
        $this->assertSame('owner', $group['role']);
        $this->assertSame(1, (int) $group['member_count']);
    }

    public function test_us10_ac07_rejects_names_outside_the_allowed_length(): void
    {
        $userId = $this->createUser('owner@example.com');
        $service = new GroupService(new GroupRepository($this->pdo));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Group names must be between 2 and 80 characters.');

        $service->create($userId, 'x');
    }

    private function createUser(string $email): int
    {
        $statement = $this->pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $statement->execute([$email, password_hash('password123', PASSWORD_DEFAULT)]);
        return (int) $this->pdo->lastInsertId();
    }
}
