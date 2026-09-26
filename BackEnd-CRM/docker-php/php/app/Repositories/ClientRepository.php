<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClientRepository
{
    private PDO $db;

    public function __construct(?PDO $connection = null)
    {
        $this->db = $connection ?? Database::connection();
    }

    /**
     * Fetch a list of clients ordered by newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(int $userId, int $limit = 50): array
    {
        $statement = $this->db->prepare(
            'SELECT
                id,
                COALESCE(code, CONCAT("CLI-", LPAD(id, 3, "0"))) AS code,
                first_name,
                last_name,
                name,
                username,
                email,
                company,
                phone,
                status,
                job_title,
                position,
                password_hash,
                avatar_path,
                project_name,
                project_progress,
                notes,
                created_at,
                updated_at
            FROM clients
            WHERE user_id = :user_id
            ORDER BY created_at DESC, id DESC
            LIMIT :limit'
        );

        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll() ?: [];
    }

    /**
     * Calculate summary statistics for the provided clients collection.
     *
     * @param array<int, array<string, mixed>> $clients
     */
    public function stats(array $clients): array
    {
        $total = count($clients);
        $active = 0;
        $inactive = 0;
        $prospect = 0;
        $createdThisMonth = 0;

        $now = new \DateTimeImmutable('now');
        $monthStart = $now->modify('first day of this month midnight');

        foreach ($clients as $client) {
            $status = strtolower((string)($client['status'] ?? ''));
            if ($status === 'inactive') {
                $inactive++;
            } elseif (in_array($status, ['prospect', 'pending'], true)) {
                $prospect++;
            } else {
                $active++;
            }

            if (!empty($client['created_at'])) {
                try {
                    $createdAt = new \DateTimeImmutable((string)$client['created_at']);
                    if ($createdAt >= $monthStart) {
                        $createdThisMonth++;
                    }
                } catch (\Throwable $ignored) {
                    // Ignore parse failures so that a single bad record does not break the stats.
                }
            }
        }

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'prospect' => $prospect,
            'new_this_month' => $createdThisMonth,
        ];
    }

    public function find(int $userId, int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM clients WHERE id = :id AND user_id = :user_id LIMIT 1');
        $statement->execute([
            ':id' => $id,
            ':user_id' => $userId,
        ]);
        $client = $statement->fetch();

        return $client !== false ? $client : null;
    }

    public function emailExists(int $userId, string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM clients WHERE email = :email AND user_id = :user_id';
        $params = [
            ':email' => $email,
            ':user_id' => $userId,
        ];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :exceptId';
            $params[':exceptId'] = $exceptId;
        }

        $sql .= ' LIMIT 1';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchColumn() !== false;
    }

    public function usernameExists(int $userId, string $username, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM clients WHERE username = :username AND user_id = :user_id';
        $params = [
            ':username' => $username,
            ':user_id' => $userId,
        ];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :exceptId';
            $params[':exceptId'] = $exceptId;
        }

        $sql .= ' LIMIT 1';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchColumn() !== false;
    }

    public function update(int $userId, int $id, array $fields): bool
    {
        if (empty($fields)) {
            return false;
        }

        $setClauses = [];
        $params = [':id' => $id];

        foreach ($fields as $column => $value) {
            $placeholder = ':' . $column;
            $setClauses[] = "{$column} = {$placeholder}";
            $params[$placeholder] = $value;
        }

        $setClauses[] = 'updated_at = NOW()';
        $params[':user_id'] = $userId;

        $sql = 'UPDATE clients SET ' . implode(', ', $setClauses) . ' WHERE id = :id AND user_id = :user_id LIMIT 1';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }

    public function create(int $userId, array $fields): int
    {
        if (empty($fields)) {
            return 0;
        }

        $fields['user_id'] = $userId;
        $columns = array_keys($fields);
        $placeholders = array_map(static fn(string $column): string => ':' . $column, $columns);

        $sql = 'INSERT INTO clients (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

        $statement = $this->db->prepare($sql);

        foreach ($fields as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->execute();

        return (int)$this->db->lastInsertId();
    }

    public function delete(int $userId, int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM clients WHERE id = :id AND user_id = :user_id LIMIT 1');
        $statement->execute([
            ':id' => $id,
            ':user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}
