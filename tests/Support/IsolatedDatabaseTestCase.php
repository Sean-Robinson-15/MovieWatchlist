<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class IsolatedDatabaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');

        foreach (glob(__DIR__ . '/../../database/migrations/*.sql') ?: [] as $migration) {
            $this->pdo->exec((string) file_get_contents($migration));
        }
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
        parent::tearDown();
    }
}
