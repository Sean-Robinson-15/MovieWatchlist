<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use RuntimeException;

final class AuthService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function register(string $email, string $password): int
    {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Enter a valid email address.');
        }

        if (strlen($password) < 8) {
            throw new RuntimeException('Your password must be at least 8 characters.');
        }

        if ($this->users->findByEmail($email) !== null) {
            throw new RuntimeException('An account with that email already exists.');
        }

        return $this->users->create($email, password_hash($password, PASSWORD_DEFAULT));
    }

    public function login(string $email, string $password): int
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('The email or password is incorrect.');
        }

        return (int) $user['id'];
    }
}
