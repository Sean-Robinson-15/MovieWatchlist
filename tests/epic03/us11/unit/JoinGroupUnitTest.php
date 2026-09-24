<?php

declare(strict_types=1);

namespace Tests\Epic03\Us11\Unit;

use App\Repository\GroupRepository;
use App\Service\GroupService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class JoinGroupUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us11_rejects_an_invalid_code(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('That group code is not valid.');

        (new GroupService(new GroupRepository($this->pdo)))->join(1, 'unknown');
    }
}