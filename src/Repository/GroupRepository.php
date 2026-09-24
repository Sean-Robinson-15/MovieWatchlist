<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class GroupRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $name, string $joinCode, int $createdBy): int
    {
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO groups (name, join_code, created_by) VALUES (:name, :join_code, :created_by)'
            );
            $statement->execute(['name' => $name, 'join_code' => $joinCode, 'created_by' => $createdBy]);
            $groupId = (int) $this->pdo->lastInsertId();

            $membership = $this->pdo->prepare(
                "INSERT INTO group_memberships (group_id, user_id, role) VALUES (:group_id, :user_id, 'owner')"
            );
            $membership->execute(['group_id' => $groupId, 'user_id' => $createdBy]);
            $this->pdo->commit();

            return $groupId;
        } catch (\Throwable $error) {
            $this->pdo->rollBack();
            throw $error;
        }
    }

    public function allForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT g.*, gm.role, COUNT(all_members.user_id) AS member_count
             FROM groups g
             JOIN group_memberships gm ON gm.group_id = g.id AND gm.user_id = :user_id
             JOIN group_memberships all_members ON all_members.group_id = g.id
             GROUP BY g.id, gm.role ORDER BY g.name COLLATE NOCASE'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findForMember(int $groupId, int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT g.*, gm.role, COUNT(all_members.user_id) AS member_count
             FROM groups g
             JOIN group_memberships gm ON gm.group_id = g.id AND gm.user_id = :user_id
             JOIN group_memberships all_members ON all_members.group_id = g.id
             WHERE g.id = :group_id GROUP BY g.id, gm.role'
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId]);
        $group = $statement->fetch();

        return $group ?: null;
    }

    public function findByJoinCode(string $joinCode): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM groups WHERE join_code = :join_code');
        $statement->execute(['join_code' => strtoupper($joinCode)]);
        $group = $statement->fetch();

        return $group ?: null;
    }

    public function addMember(int $groupId, int $userId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT OR IGNORE INTO group_memberships (group_id, user_id) VALUES (:group_id, :user_id)'
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId]);
    }

    public function removeMember(int $groupId, int $userId): void
    {
        $statement = $this->pdo->prepare(
            "DELETE FROM group_memberships WHERE group_id = :group_id AND user_id = :user_id AND role = 'member'"
        );
        $statement->execute(['group_id' => $groupId, 'user_id' => $userId]);
    }

    public function members(int $groupId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT u.email, gm.role, gm.created_at FROM group_memberships gm
             JOIN users u ON u.id = gm.user_id WHERE gm.group_id = :group_id
             ORDER BY CASE gm.role WHEN \'owner\' THEN 0 ELSE 1 END, u.email COLLATE NOCASE'
        );
        $statement->execute(['group_id' => $groupId]);

        return $statement->fetchAll();
    }

}
