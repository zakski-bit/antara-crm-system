<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CalendarEventRepository
{
    private PDO $db;
    private string $table = 'calendar_events';

    private array $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'all_day',
        'background_color',
        'text_color',
        'class_name',
    ];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function allBetween(?string $startDate, ?string $endDate): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($startDate && $endDate) {
            $sql .= " WHERE start_date <= :endDate AND (end_date IS NULL OR end_date >= :startDate)";
            $params = [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ];
        } elseif ($startDate) {
            $sql .= " WHERE end_date IS NULL OR end_date >= :startDate";
            $params = ['startDate' => $startDate];
        } elseif ($endDate) {
            $sql .= " WHERE start_date <= :endDate";
            $params = ['endDate' => $endDate];
        }

        $sql .= " ORDER BY start_date ASC, COALESCE(start_time, '00:00:00') ASC";

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
        $filtered = $this->filterFillable($data);
        $filtered['all_day'] = empty($filtered['all_day']) ? 0 : 1;

        $now = date('Y-m-d H:i:s');
        $filtered['created_at'] = $now;
        $filtered['updated_at'] = $now;

        $columns = array_keys($filtered);
        $placeholders = array_map(static fn ($column) => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($filtered);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);
        if (empty($filtered)) {
            return false;
        }

        $filtered['all_day'] = empty($filtered['all_day']) ? 0 : 1;
        $filtered['updated_at'] = date('Y-m-d H:i:s');

        $setClauses = [];
        foreach ($filtered as $column => $value) {
            $setClauses[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table,
            implode(', ', $setClauses)
        );

        $filtered['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($filtered);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    private function filterFillable(array $data): array
    {
        $filtered = [];
        foreach ($this->fillable as $column) {
            if (array_key_exists($column, $data)) {
                $filtered[$column] = $data[$column];
            }
        }

        return $filtered;
    }
}
