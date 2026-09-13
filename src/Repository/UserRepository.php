<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function create(string $email, string $passwordHash): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)'
        );
        $statement->execute(['email' => $email, 'password_hash' => $passwordHash]);

        return (int) $this->pdo->lastInsertId();
    }
}
