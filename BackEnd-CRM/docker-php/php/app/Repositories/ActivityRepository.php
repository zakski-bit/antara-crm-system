<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ActivityRepository
{
    private PDO $db;
    private string $table = 'activities';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY due_date ASC, COALESCE(due_time, '00:00:00') ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll() ?: [];
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS aggregate FROM {$this->table}");
        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function filtered(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['start_date'])) {
            $conditions[] = 'due_date >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'due_date <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['type'])) {
            $conditions[] = 'activity_type = :activity_type';
            $params['activity_type'] = $filters['type'];
        }

        $orderMap = [
            'recent' => 'due_date DESC, COALESCE(due_time, "23:59:59") DESC',
            'oldest' => 'due_date ASC, COALESCE(due_time, "00:00:00") ASC',
            'title_asc' => 'title ASC',
            'title_desc' => 'title DESC',
        ];

        $orderBy = $orderMap[$filters['sort'] ?? 'recent'] ?? $orderMap['recent'];

        $sql = "SELECT * FROM {$this->table}";
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY {$orderBy}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

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

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table,
            implode(', ', $sets)
        );

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
