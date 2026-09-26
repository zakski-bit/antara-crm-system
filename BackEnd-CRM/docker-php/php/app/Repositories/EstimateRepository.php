<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class EstimateRepository
{
    private PDO $db;
    private string $table = 'crm_estimates';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(int $userId, ?string $status = null, string $sort = 'recent'): array
    {
        $params = ['user_id' => $userId];
        $where = 'WHERE user_id = :user_id';

        if ($status) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }

        $orderBy = $this->buildOrderBy($sort);

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} {$where} {$orderBy}");
        $stmt->execute($params);

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

        if (empty($data['ref_no'])) {
            $data['ref_no'] = $this->generateRef();
        }

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

        return (int) $this->db->lastInsertId();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        unset($data['user_id']);

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf('UPDATE %s SET %s WHERE id = :id AND user_id = :user_id', $this->table, implode(', ', $sets));
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

    private function generateRef(): string
    {
        return 'EST-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    private function buildOrderBy(string $sort): string
    {
        switch ($sort) {
            case 'amount_desc':
                return 'ORDER BY amount DESC, estimate_date DESC';
            case 'amount_asc':
                return 'ORDER BY amount ASC, estimate_date DESC';
            case 'expiry':
                // Prioritise soonest expiry; NULL expiry pushed last
                return 'ORDER BY (expiry_date IS NULL), expiry_date ASC, estimate_date DESC';
            default:
                return 'ORDER BY estimate_date DESC, id DESC';
        }
    }
}
