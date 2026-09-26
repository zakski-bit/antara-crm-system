<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct(?PDO $connection = null)
    {
        $this->db = $connection ?? Database::connection();
    }

    /**
     * Retrieve all users ordered by most recent.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC, id DESC'
        );

        return $statement->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $user = $statement->fetch();

        return $user !== false ? $user : null;
    }
}

