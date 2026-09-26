<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PipelineRepository
{
    private PDO $db;
    private string $table = 'crm_pipeline_entries';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $userId, int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['user_id'] = $userId;

        $columns = array_keys($data);
        $placeholders = array_map(fn ($column) => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        unset($data['user_id']);

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id AND user_id = :user_id',
            $this->table,
            implode(', ', $sets)
        );

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        $data['user_id'] = $userId;

        return $stmt->execute($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }
}
