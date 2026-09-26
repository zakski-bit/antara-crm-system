<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClientSubscriptionRepository
{
    private PDO $db;
    private string $table = 'crm_client_subscriptions';

    public function __construct(?PDO $connection = null)
    {
        $this->db = $connection ?? Database::connection();
    }

    /**
     * @return array{data: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginate(
        int $userId,
        int $perPage = 25,
        int $page = 1,
        ?string $search = null,
        ?string $status = null,
        ?string $cycle = null
    ): array {
        [$where, $params] = $this->buildFilters($userId, $search, $status, $cycle);
        $joins = "
            LEFT JOIN clients c ON s.client_id = c.id
            LEFT JOIN users u ON s.customer_user_id = u.id
        ";

        $limit = max(1, $perPage);
        $offset = max(0, ($page - 1) * $limit);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} s {$joins} {$where}");
        $this->bindParams($countStmt, $params);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT s.*, c.name AS client_name, c.email AS client_email, u.name AS customer_name, u.email AS customer_email
            FROM {$this->table} s
            {$joins}
            {$where}
            ORDER BY s.updated_at DESC, s.id DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->db->prepare($sql);
        $dataParams = $params;
        $dataParams['limit'] = $limit;
        $dataParams['offset'] = $offset;
        $this->bindParams($stmt, $dataParams);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll() ?: [],
            'total' => $total,
            'page' => $page,
            'per_page' => $limit,
        ];
    }

    public function listByCustomer(int $userId, int $customerUserId, ?string $customerEmail = null): array
    {
        // If the caller is the customer (customer login), don't filter by owner_id
        $matchClauses = [];
        $params = [];

        if ($customerUserId > 0) {
            $matchClauses[] = 's.customer_user_id = :cust';
            $params[':cust'] = $customerUserId;
        }

        if ($customerEmail !== null && $customerEmail !== '') {
            // allow mapping by email to clients table (untuk akun yang juga tercatat sebagai klien)
            $matchClauses[] = 'c.email = :cust_email';
            $params[':cust_email'] = $customerEmail;
        }

        // No identifiable target, return early to avoid dumping seluruh data
        if (empty($matchClauses)) {
            return [];
        }

        $where = '(' . implode(' OR ', $matchClauses) . ')';
        if ($userId > 0 && $userId !== $customerUserId) {
            $where .= ' AND s.user_id = :owner_id';
            $params[':owner_id'] = $userId;
        }

        $stmt = $this->db->prepare("
            SELECT s.*, c.name AS client_name, c.email AS client_email, u.name AS customer_name, u.email AS customer_email
            FROM {$this->table} s
            LEFT JOIN clients c ON s.client_id = c.id
            LEFT JOIN users u ON s.customer_user_id = u.id
            WHERE {$where}
            ORDER BY COALESCE(s.renewal_at, s.started_at, s.created_at) DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function create(int $ownerId, array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['user_id'] = $ownerId;
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $data['updated_at'] ?? $now;

        $columns = array_keys($data);
        $placeholders = array_map(static fn($col) => ':' . $col, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $stmt = $this->db->prepare($sql);
        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $ownerId, int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :owner_id");
        return $stmt->execute([
            ':id' => $id,
            ':owner_id' => $ownerId,
        ]);
    }

    /**
     * @return array{0:string,1:array<string,mixed>}
     */
    private function buildFilters(int $userId, ?string $search, ?string $status, ?string $cycle): array
    {
        $conditions = ['s.user_id = :owner_id'];
        $params = ['owner_id' => $userId];

        if ($status) {
            $conditions[] = 's.status = :status';
            $params['status'] = $status;
        }
        if ($cycle) {
            $conditions[] = 's.cycle = :cycle';
            $params['cycle'] = $cycle;
        }
        if ($search) {
            $conditions[] = "(CONCAT_WS(' ', s.plan_name, s.plan_code, COALESCE(c.name,''), COALESCE(u.name,'')) LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);
        return [$where, $params];
    }

    /**
     * @param \PDOStatement $stmt
     * @param array<string,mixed> $params
     */
    private function bindParams($stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . ltrim($key, ':'), $value);
        }
    }
}
