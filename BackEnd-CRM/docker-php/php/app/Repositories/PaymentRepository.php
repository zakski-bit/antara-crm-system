<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PaymentRepository
{
    private PDO $db;
    private string $table = 'crm_payments';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @return array{data: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginate(int $userId, ?string $status, string $sort, int $perPage, int $page, ?string $search = null, ?string $startDate = null, ?string $endDate = null, ?int $recipientUserId = null): array
    {
        [$where, $params] = $this->buildFilters($userId, $status, $search, $startDate, $endDate, $recipientUserId);

        $orderBy = $this->buildOrderBy($sort);

        $limit = max(1, $perPage);
        $offset = max(0, ($page - 1) * $limit);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} p LEFT JOIN crm_product_invoices i ON p.invoice_id = i.id {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT
                p.*,
                i.invoice_title,
                i.total_amount AS invoice_total,
                i.amount_paid AS invoice_amount_paid,
                (i.total_amount - i.amount_paid) AS invoice_amount_due,
                i.status AS invoice_status,
                i.client_company AS invoice_company,
                i.client_name AS invoice_client_name,
                i.client_position AS invoice_client_position,
                i.client_avatar AS invoice_client_avatar
            FROM {$this->table} p
            LEFT JOIN crm_product_invoices i ON p.invoice_id = i.id
            {$where}
            {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll() ?: [];

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $limit,
        ];
    }

    public function find(int $userId, int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                p.*,
                i.invoice_title,
                i.total_amount AS invoice_total,
                i.amount_paid AS invoice_amount_paid,
                (i.total_amount - i.amount_paid) AS invoice_amount_due,
                i.status AS invoice_status,
                i.client_company AS invoice_company,
                i.client_name AS invoice_client_name,
                i.client_position AS invoice_client_position,
                i.client_avatar AS invoice_client_avatar
            FROM {$this->table} p
            LEFT JOIN crm_product_invoices i ON p.invoice_id = i.id AND i.user_id = p.user_id
            WHERE p.id = :id AND p.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
        ]);

        $row = $stmt->fetch();
        return $row ?: null;
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

        $paymentId = (int) $this->db->lastInsertId();

        if (!empty($data['invoice_id'])) {
            $this->refreshInvoicePayment($userId, (int) $data['invoice_id']);
        }

        return $paymentId;
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
        $data['id'] = $id;
        $data['user_id'] = $userId;

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($data);

        if (!empty($data['invoice_id'])) {
            $this->refreshInvoicePayment($userId, (int) $data['invoice_id']);
        }

        return $result;
    }

    public function verify(
        int $userId,
        int $id,
        string $verificationStatus,
        ?int $verifiedByUserId,
        ?string $verificationNotes = null,
        ?string $paymentStatus = null
    ): bool {
        $payment = $this->find($userId, $id);
        if (!$payment) {
            return false;
        }

        $payload = [
            'id' => $id,
            'user_id' => $userId,
            'verification_status' => $verificationStatus,
            'verified_by_user_id' => $verifiedByUserId,
            'verified_at' => date('Y-m-d H:i:s'),
            'verification_notes' => $verificationNotes,
        ];

        $sets = [
            'verification_status = :verification_status',
            'verified_by_user_id = :verified_by_user_id',
            'verified_at = :verified_at',
            'verification_notes = :verification_notes',
            'updated_at = NOW()',
        ];

        if ($paymentStatus !== null && $paymentStatus !== '') {
            $sets[] = 'status = :status';
            $payload['status'] = $paymentStatus;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id AND user_id = :user_id',
            $this->table,
            implode(', ', $sets)
        );

        $stmt = $this->db->prepare($sql);
        $updated = $stmt->execute($payload);

        if ($updated && !empty($payment['invoice_id'])) {
            $this->refreshInvoicePayment($userId, (int) $payment['invoice_id']);
        }

        return $updated;
    }

    public function delete(int $userId, int $id): bool
    {
        $payment = $this->find($userId, $id);

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        $deleted = $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
        ]);

        if ($deleted && !empty($payment['invoice_id'])) {
            $this->refreshInvoicePayment($userId, (int) $payment['invoice_id']);
        }

        return $deleted;
    }

    /**
     * @return array<string, float|int>
     */
    public function stats(int $userId, ?string $startDate, ?string $endDate, ?int $recipientUserId = null): array
    {
        [$where, $params] = $this->buildFilters($userId, null, null, $startDate, $endDate, $recipientUserId);

        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN p.status IN ('settled', 'partial') AND COALESCE(p.verification_status, 'approved') = 'approved' THEN p.amount ELSE 0 END), 0) AS paid_amount,
                COALESCE(SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END), 0) AS pending_amount,
                COALESCE(SUM(CASE WHEN p.status IN ('failed', 'refunded') THEN p.amount ELSE 0 END), 0) AS failed_amount,
                COUNT(*) AS total_rows
            FROM {$this->table} p
            LEFT JOIN crm_product_invoices i ON p.invoice_id = i.id
            {$where}
        ");
        $stmt->execute($params);

        $row = $stmt->fetch() ?: [];

        return [
            'paid_amount' => (float) ($row['paid_amount'] ?? 0),
            'pending_amount' => (float) ($row['pending_amount'] ?? 0),
            'failed_amount' => (float) ($row['failed_amount'] ?? 0),
            'total_rows' => (int) ($row['total_rows'] ?? 0),
            'linked_outstanding' => $this->linkedOutstanding($userId, $recipientUserId),
        ];
    }

    private function linkedOutstanding(int $userId, ?int $recipientUserId): float
    {
        if ($recipientUserId !== null) {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(GREATEST(i.total_amount - i.amount_paid, 0)), 0) AS outstanding
                FROM crm_product_invoices i
                WHERE (i.user_id = :owner_id OR i.recipient_user_id = :recipient_id) AND EXISTS (
                    SELECT 1 FROM {$this->table} p WHERE p.invoice_id = i.id
                )
            ");
            $stmt->bindValue(':owner_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':recipient_id', $recipientUserId, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(GREATEST(i.total_amount - i.amount_paid, 0)), 0) AS outstanding
                FROM crm_product_invoices i
                WHERE (i.user_id = :owner_id OR i.recipient_user_id = :owner_id2) AND EXISTS (
                    SELECT 1 FROM {$this->table} p WHERE p.invoice_id = i.id AND p.user_id = :owner_id3
                )
            ");
            $stmt->bindValue(':owner_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':owner_id2', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':owner_id3', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();

        $row = $stmt->fetch() ?: [];
        return (float) ($row['outstanding'] ?? 0);
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildFilters(int $userId, ?string $status, ?string $search, ?string $startDate, ?string $endDate, ?int $recipientUserId = null): array
    {
        $conditions = [];
        $params = [];

        if ($recipientUserId !== null) {
            $conditions[] = '(p.user_id = :owner_id OR i.recipient_user_id = :recipient_id)';
            $params['owner_id'] = $userId;
            $params['recipient_id'] = $recipientUserId;
            $conditions[] = 'p.invoice_id IS NOT NULL';
            $conditions[] = "COALESCE(p.verification_status, 'approved') = 'approved'";
        } else {
            $conditions[] = 'p.user_id = :owner_id';
            $params['owner_id'] = $userId;
        }

        if ($status) {
            $conditions[] = 'p.status = :status';
            $params['status'] = $status;
        }

        if ($search) {
            $conditions[] = '(p.invoice_no LIKE :search OR p.client_name LIKE :search OR p.company_name LIKE :search OR p.reference_no LIKE :search OR p.payment_method LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($startDate) {
            $conditions[] = 'p.paid_at >= :start_date';
            $params['start_date'] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $conditions[] = 'p.paid_at <= :end_date';
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }

    private function buildOrderBy(string $sort): string
    {
        switch ($sort) {
            case 'amount_desc':
                return 'ORDER BY p.amount DESC, p.paid_at DESC';
            case 'amount_asc':
                return 'ORDER BY p.amount ASC, p.paid_at DESC';
            case 'oldest':
                return 'ORDER BY p.paid_at ASC, p.id ASC';
            case 'client':
                return 'ORDER BY p.client_name ASC, p.paid_at DESC';
            default:
                return 'ORDER BY p.paid_at DESC, p.id DESC';
        }
    }

    private function refreshInvoicePayment(int $userId, int $invoiceId): void
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN status IN ('settled', 'partial') AND COALESCE(verification_status, 'approved') = 'approved' THEN amount ELSE 0 END), 0) AS paid
            FROM {$this->table}
            WHERE invoice_id = :invoice_id AND user_id = :user_id
        ");
        $stmt->execute([
            ':invoice_id' => $invoiceId,
            ':user_id' => $userId,
        ]);
        $paid = (float) ($stmt->fetchColumn() ?: 0);

        $invoiceStmt = $this->db->prepare("
            SELECT total_amount, status
            FROM crm_product_invoices
            WHERE id = :invoice_id AND user_id = :user_id
            LIMIT 1
        ");
        $invoiceStmt->execute([
            ':invoice_id' => $invoiceId,
            ':user_id' => $userId,
        ]);
        $invoice = $invoiceStmt->fetch() ?: null;

        if (!$invoice) {
            return;
        }

        $total = (float) ($invoice['total_amount'] ?? 0);
        $currentStatus = (string) ($invoice['status'] ?? 'draft');
        $newStatus = $currentStatus;

        if ($total > 0 && $paid >= $total) {
            $newStatus = 'paid';
        } elseif ($paid > 0 && $currentStatus === 'paid') {
            $newStatus = 'partial';
        } elseif ($paid > 0 && $currentStatus === 'draft') {
            $newStatus = 'pending';
        }

        $updateStmt = $this->db->prepare("
            UPDATE crm_product_invoices
            SET amount_paid = :amount_paid, status = :status, updated_at = NOW()
            WHERE id = :invoice_id AND user_id = :user_id
        ");
        $updateStmt->execute([
            ':amount_paid' => $paid,
            ':status' => $newStatus,
            ':invoice_id' => $invoiceId,
            ':user_id' => $userId,
        ]);
    }
}
