<?php

declare(strict_types=1);

namespace Tests\Epic04\Us15\Unit;

use App\Repository\UserRepository;
use App\Service\AuthService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ErrorHandlingUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us15_authentication_failure_uses_a_generic_message(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The email or password is incorrect.');

        (new AuthService(new UserRepository($this->pdo)))->login('missing@example.com', 'secret');
    }
}