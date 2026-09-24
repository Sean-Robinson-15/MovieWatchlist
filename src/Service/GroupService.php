<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\GroupRepository;
use RuntimeException;

final class GroupService
{
    public function __construct(private GroupRepository $groups)
    {
    }

    public function allForUser(int $userId): array
    {
        return $this->groups->allForUser($userId);
    }

    public function getForMember(int $groupId, int $userId): array
    {
        $group = $this->groups->findForMember($groupId, $userId);
        if ($group === null) {
            throw new RuntimeException('You are not a member of that group.');
        }

        return $group;
    }

    public function create(int $userId, string $name): int
    {
        $name = trim($name);
        if (strlen($name) < 2 || strlen($name) > 80) {
            throw new RuntimeException('Group names must be between 2 and 80 characters.');
        }

        do {
            $joinCode = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while ($this->groups->findByJoinCode($joinCode) !== null);

        return $this->groups->create($name, $joinCode, $userId);
    }

    public function join(int $userId, string $joinCode): int
    {
        $joinCode = strtoupper(trim($joinCode));
        $group = $this->groups->findByJoinCode($joinCode);
        if ($group === null) {
            throw new RuntimeException('That group code is not valid.');
        }

        $this->groups->addMember((int) $group['id'], $userId);
        return (int) $group['id'];
    }

    public function leave(int $groupId, int $userId): void
    {
        $group = $this->getForMember($groupId, $userId);
        if ($group['role'] === 'owner') {
            throw new RuntimeException('The group owner cannot leave the group yet.');
        }

        $this->groups->removeMember($groupId, $userId);
    }

    public function delete(int $groupId, int $userId): void
    {
        $group = $this->getForMember($groupId, $userId);
        if ($group['role'] !== 'owner' || (int) $group['created_by'] !== $userId) {
            throw new RuntimeException('Only the group owner can delete this group.');
        }

        if (!$this->groups->delete($groupId, $userId)) {
            throw new RuntimeException('The group could not be deleted.');
        }
    }

    public function members(int $groupId, int $userId): array
    {
        $this->getForMember($groupId, $userId);
        return $this->groups->members($groupId);
    }
}
