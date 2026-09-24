<?php

declare(strict_types=1);

namespace Tests\Epic03\Us10\Unit;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class CreateGroupUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us10_rejects_names_shorter_than_two_characters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Group names must be between 2 and 80 characters.');

        (new GroupService(new GroupRepository($this->pdo)))->create(1, 'x');
    }
}