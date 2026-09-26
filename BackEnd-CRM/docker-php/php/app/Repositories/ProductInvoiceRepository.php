<?php

namespace App\Repositories;

use App\Core\Database;
use DateInterval;
use DateTimeImmutable;
use PDO;

class ProductInvoiceRepository
{
    private PDO $db;
    private string $table = 'crm_product_invoices';
    private string $itemsTable = 'crm_product_invoice_items';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function invoiceNoExists(int $userId, string $invoiceNo, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE user_id = :user_id AND invoice_no = :invoice_no";
        $params = [
            'user_id' => $userId,
            'invoice_no' => $invoiceNo,
        ];

        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @return array{data: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginate(
        int $userId,
        ?string $status,
        string $sort,
        int $perPage,
        int $page,
        ?string $search = null,
        ?string $direction = null,
        ?int $recipientUserId = null
    ): array
    {
        if ($recipientUserId !== null) {
            $conditions = ['(user_id = :owner_id OR recipient_user_id = :recipient_id)'];
            $params = [
                'owner_id' => $userId,
                'recipient_id' => $recipientUserId,
            ];
        } else {
            $conditions = ['user_id = :owner_id'];
            $params = ['owner_id' => $userId];
        }

        if ($direction) {
            $conditions[] = 'direction = :direction';
            $params['direction'] = $direction;
        }

        if ($status) {
            $conditions[] = 'status = :status';
            $params['status'] = $status;
        }

        if ($search) {
            $conditions[] = '(invoice_no LIKE :search_invoice OR client_name LIKE :search_client OR client_company LIKE :search_company)';
            $likeSearch = '%' . $search . '%';
            $params['search_invoice'] = $likeSearch;
            $params['search_client'] = $likeSearch;
            $params['search_company'] = $likeSearch;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $orderBy = $this->buildOrderBy($sort);

        $limit = max(1, $perPage);
        $offset = max(0, ($page - 1) * $limit);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT *, (total_amount - amount_paid) AS amount_due FROM {$this->table} {$where} {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $invoices = $stmt->fetchAll() ?: [];

        if (!empty($invoices)) {
            $items = $this->itemsForInvoices(array_column($invoices, 'id'));
            foreach ($invoices as &$invoice) {
                $invoice['items'] = $items[$invoice['id']] ?? [];
            }
        }

        return [
            'data' => $invoices,
            'total' => $total,
            'page' => $page,
            'per_page' => $limit,
        ];
    }

    public function find(int $userId, int $id, ?string $direction = null, ?int $recipientUserId = null): ?array
    {
        if ($recipientUserId !== null) {
            $sql = "
                SELECT *, (total_amount - amount_paid) AS amount_due
                FROM {$this->table}
                WHERE id = :id AND (user_id = :owner_id OR recipient_user_id = :recipient_id)
            ";
            $params = [
                'id' => $id,
                'owner_id' => $userId,
                'recipient_id' => $recipientUserId,
            ];
        } else {
            $sql = "
                SELECT *, (total_amount - amount_paid) AS amount_due
                FROM {$this->table}
                WHERE id = :id AND user_id = :owner_id
            ";
            $params = [
                'id' => $id,
                'owner_id' => $userId,
            ];
        }

        if ($direction) {
            $sql .= " AND direction = :direction";
            $params['direction'] = $direction;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            return null;
        }

        $invoice['items'] = $this->itemsForInvoices([$invoice['id']])[$invoice['id']] ?? [];

        return $invoice;
    }

    public function create(int $userId, array $data, array $items): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['user_id'] = $userId;
        if (empty($data['direction'])) {
            $data['direction'] = 'outgoing';
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn ($column) => ':' . $column, $columns);

        $this->db->beginTransaction();
        try {
            $sql = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);

            $invoiceId = (int) $this->db->lastInsertId();
            $this->syncItems($invoiceId, $items, false);

            $this->db->commit();
            return $invoiceId;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function update(int $userId, int $id, array $data, array $items): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        unset($data['user_id']);

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf('UPDATE %s SET %s WHERE id = :id AND user_id = :user_id', $this->table, implode(', ', $sets));
        $data['id'] = $id;
        $data['user_id'] = $userId;

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            $this->syncItems($id, $items, true);
            $this->db->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function delete(int $userId, int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }

    /**
     * @return array<string, float>
     */
    public function statsForPeriod(int $userId, DateTimeImmutable $start, DateTimeImmutable $end, ?string $direction = null, ?int $recipientUserId = null): array
    {
        if ($recipientUserId !== null) {
            $sql = "
                SELECT
                    COALESCE(SUM(total_amount), 0) AS total_amount,
                    COALESCE(SUM(CASE WHEN status <> 'paid' THEN total_amount - amount_paid ELSE 0 END), 0) AS outstanding,
                    COALESCE(SUM(CASE WHEN status = 'draft' THEN total_amount ELSE 0 END), 0) AS draft_amount,
                    COALESCE(SUM(CASE WHEN status = 'overdue' THEN total_amount - amount_paid ELSE 0 END), 0) AS overdue_amount
                FROM {$this->table}
                WHERE issue_date BETWEEN :start AND :end
                  AND (user_id = :owner_id OR recipient_user_id = :recipient_id)
            ";
            $params = [
                'owner_id' => $userId,
                'recipient_id' => $recipientUserId,
                'start' => $start->format('Y-m-d H:i:s'),
                'end' => $end->format('Y-m-d H:i:s'),
            ];
        } else {
            $sql = "
                SELECT
                    COALESCE(SUM(total_amount), 0) AS total_amount,
                    COALESCE(SUM(CASE WHEN status <> 'paid' THEN total_amount - amount_paid ELSE 0 END), 0) AS outstanding,
                    COALESCE(SUM(CASE WHEN status = 'draft' THEN total_amount ELSE 0 END), 0) AS draft_amount,
                    COALESCE(SUM(CASE WHEN status = 'overdue' THEN total_amount - amount_paid ELSE 0 END), 0) AS overdue_amount
                FROM {$this->table}
                WHERE issue_date BETWEEN :start AND :end
                  AND user_id = :owner_id
            ";
            $params = [
                'owner_id' => $userId,
                'start' => $start->format('Y-m-d H:i:s'),
                'end' => $end->format('Y-m-d H:i:s'),
            ];
        }

        if ($direction) {
            $sql .= " AND direction = :direction";
            $params['direction'] = $direction;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch() ?: [];

        return [
            'total_amount' => (float) ($result['total_amount'] ?? 0),
            'outstanding' => (float) ($result['outstanding'] ?? 0),
            'draft_amount' => (float) ($result['draft_amount'] ?? 0),
            'overdue_amount' => (float) ($result['overdue_amount'] ?? 0),
        ];
    }

    /**
     * @return array{
     *     current: array<string, float>,
     *     previous: array<string, float>
     * }
     */
    public function statsSummary(int $userId, ?string $direction = null, ?int $recipientUserId = null): array
    {
        $currentStart = new DateTimeImmutable('first day of this month 00:00:00');
        $currentEnd = new DateTimeImmutable('last day of this month 23:59:59');
        $previousStart = $currentStart->sub(new DateInterval('P1M'));
        $previousEnd = $currentStart->sub(new DateInterval('PT1S'));

        return [
            'current' => $this->statsForPeriod($userId, $currentStart, $currentEnd, $direction, $recipientUserId),
            'previous' => $this->statsForPeriod($userId, $previousStart, $previousEnd, $direction, $recipientUserId),
        ];
    }

    /**
     * @param array<int, int> $invoiceIds
     * @return array<int, array<int, array>>
     */
    private function itemsForInvoices(array $invoiceIds): array
    {
        if (empty($invoiceIds)) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($invoiceIds), '?'));
        $sql = "SELECT * FROM {$this->itemsTable} WHERE invoice_id IN ({$placeholders}) ORDER BY invoice_id, line_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($invoiceIds));
        $rows = $stmt->fetchAll() ?: [];

        $grouped = [];
        foreach ($rows as $row) {
            $invoiceId = (int) ($row['invoice_id'] ?? 0);
            if (!isset($grouped[$invoiceId])) {
                $grouped[$invoiceId] = [];
            }
            $grouped[$invoiceId][] = $row;
        }

        return $grouped;
    }

    private function buildOrderBy(string $sort): string
    {
        switch ($sort) {
            case 'due_asc':
                return 'ORDER BY due_date ASC, issue_date DESC';
            case 'due_desc':
                return 'ORDER BY due_date DESC, issue_date DESC';
            case 'amount_desc':
                return 'ORDER BY total_amount DESC, issue_date DESC';
            case 'amount_asc':
                return 'ORDER BY total_amount ASC, issue_date DESC';
            default:
                return 'ORDER BY issue_date DESC, id DESC';
        }
    }

    /**
     * Ambil daftar tagihan terbaru untuk dropdown pembayaran.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listLatest(int $userId, int $limit = 50, ?string $direction = null, ?int $recipientUserId = null): array
    {
        $sql = "
            SELECT
                id,
                invoice_no,
                invoice_title,
                client_name,
                client_company,
                client_position,
                client_avatar,
                issue_date,
                due_date,
                status,
                total_amount,
                amount_paid,
                (total_amount - amount_paid) AS amount_due
            FROM {$this->table}
        ";

        $scope = ['user_id = :user_id'];
        if ($recipientUserId !== null) {
            $scope[] = 'recipient_user_id = :recipient_id';
        }

        $sql .= ' WHERE (' . implode(' OR ', $scope) . ')';

        if ($direction) {
            $sql .= " AND direction = :direction";
        }

        $sql .= " ORDER BY issue_date DESC, id DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        if ($recipientUserId !== null) {
            $stmt->bindValue(':recipient_id', $recipientUserId, PDO::PARAM_INT);
        }
        if ($direction) {
            $stmt->bindValue(':direction', $direction, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * @param array<int, array> $items
     */
    private function syncItems(int $invoiceId, array $items, bool $replace): void
    {
        if ($replace) {
            $deleteStmt = $this->db->prepare("DELETE FROM {$this->itemsTable} WHERE invoice_id = :invoice_id");
            $deleteStmt->execute(['invoice_id' => $invoiceId]);
        }

        if (empty($items)) {
            return;
        }

        $sql = "INSERT INTO {$this->itemsTable} (invoice_id, line_order, product_name, description, quantity, unit_price, discount_percent, line_total)
                VALUES (:invoice_id, :line_order, :product_name, :description, :quantity, :unit_price, :discount_percent, :line_total)";
        $stmt = $this->db->prepare($sql);

        foreach ($items as $index => $item) {
            $stmt->execute([
                'invoice_id' => $invoiceId,
                'line_order' => $item['line_order'] ?? $index,
                'product_name' => $item['product_name'] ?? $item['description'] ?? 'Item',
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount_percent' => $item['discount_percent'] ?? 0,
                'line_total' => $item['line_total'] ?? 0,
            ]);
        }
    }
}
