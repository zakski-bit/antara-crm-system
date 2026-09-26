<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class EmailRepository
{
    private PDO $db;
    private string $table = 'email_messages';
    private bool $hasUserScope = false;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->hasUserScope = $this->supportsUserScope();
    }

    public function list(int $userId, string $folder, ?string $filter = null, ?string $search = null, ?string $label = null, int $limit = 50): array
    {
        $folder = strtolower(trim($folder));
        $conditions = [];
        $params = [];

        // Scope per folder
        if ($this->hasUserScope) {
            if ($folder === 'sent') {
                $conditions[] = 'folder = :folder';
                $params['folder'] = 'sent';
                $conditions[] = 'sender_user_id = :owner_id';
                $params['owner_id'] = $userId;
            } elseif ($folder === 'inbox') {
                $conditions[] = 'folder = :folder';
                $params['folder'] = 'inbox';
                $conditions[] = 'recipient_user_id = :owner_id';
                $params['owner_id'] = $userId;
            } else {
                $conditions[] = '(sender_user_id = :owner_id OR recipient_user_id = :owner_id2)';
                $params['owner_id'] = $userId;
                $params['owner_id2'] = $userId;
                $conditions[] = 'folder = :folder';
                $params['folder'] = $folder;
            }
        } else {
            $conditions[] = 'folder = :folder';
            $params['folder'] = $folder;
        }

        if ($folder === 'starred') {
            $conditions[] = 'is_starred = 1';
        } elseif ($folder === 'unread') {
            $conditions[] = 'is_read = 0';
        }

        if ($filter !== null && $filter !== '') {
            switch ($filter) {
                case 'important':
                    $conditions[] = 'is_starred = 1';
                    break;
                case 'scheduled':
                    $conditions[] = 'scheduled_for IS NOT NULL AND scheduled_for >= NOW()';
                    break;
                case 'upcoming':
                    $conditions[] = 'category = :category_upcoming';
                    $params['category_upcoming'] = 'events';
                    break;
                case 'trending':
                    $conditions[] = 'category = :category_trending';
                    $params['category_trending'] = 'campaign';
                    break;
                default:
                    break;
            }
        }

        if ($label !== null && $label !== '') {
            $conditions[] = 'FIND_IN_SET(:label, REPLACE(labels, \', \', \',\')) > 0';
            $params['label'] = $label;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = '(subject LIKE :search_subject OR sender_name LIKE :search_sender_name OR sender_email LIKE :search_sender_email OR body_text LIKE :search_body)';
            $pattern = '%' . $search . '%';
            $params['search_subject'] = $pattern;
            $params['search_sender_name'] = $pattern;
            $params['search_sender_email'] = $pattern;
            $params['search_body'] = $pattern;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY COALESCE(received_at, created_at) DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id, int $userId): ?array
    {
        if ($this->hasUserScope) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2) LIMIT 1");
            $stmt->execute([
                'id' => $id,
                'owner_id' => $userId,
                'owner_id2' => $userId,
            ]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
        }
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $now;

        $columns = array_keys($data);
        $placeholders = array_map(static fn ($column) => ':' . $column, $columns);

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

    public function markRead(int $id, int $userId, bool $isRead = true): bool
    {
        return $this->updateStatus($id, $userId, ['is_read' => $isRead ? 1 : 0]);
    }

    public function toggleStar(int $id, int $userId, bool $isStarred = true): bool
    {
        return $this->updateStatus($id, $userId, ['is_starred' => $isStarred ? 1 : 0]);
    }

    public function moveToFolder(int $id, int $userId, string $folder): bool
    {
        return $this->updateStatus($id, $userId, ['folder' => strtolower($folder)]);
    }

    public function counts(int $userId): array
    {
        $totals = [
            'inbox' => ['total' => 0, 'unread' => 0],
            'sent' => ['total' => 0, 'unread' => 0],
            'drafts' => ['total' => 0, 'unread' => 0],
            'deleted' => ['total' => 0, 'unread' => 0],
            'spam' => ['total' => 0, 'unread' => 0],
        ];

        if ($this->hasUserScope) {
            // inbox
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread FROM {$this->table} WHERE folder = 'inbox' AND recipient_user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $row = $stmt->fetch() ?: [];
            $totals['inbox']['total'] = (int) ($row['total'] ?? 0);
            $totals['inbox']['unread'] = (int) ($row['unread'] ?? 0);

            // sent
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread FROM {$this->table} WHERE folder = 'sent' AND sender_user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $row = $stmt->fetch() ?: [];
            $totals['sent']['total'] = (int) ($row['total'] ?? 0);
            $totals['sent']['unread'] = (int) ($row['unread'] ?? 0);

            // other folders use OR scope
            foreach (['drafts', 'deleted', 'spam'] as $folder) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread
                    FROM {$this->table}
                    WHERE folder = :folder AND (sender_user_id = :uid OR recipient_user_id = :uid2)
                ");
                $stmt->execute([':folder' => $folder, ':uid' => $userId, ':uid2' => $userId]);
                $row = $stmt->fetch() ?: [];
                $totals[$folder]['total'] = (int) ($row['total'] ?? 0);
                $totals[$folder]['unread'] = (int) ($row['unread'] ?? 0);
            }

            $starredStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE is_starred = 1 AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)");
            $starredStmt->execute([':owner_id' => $userId, ':owner_id2' => $userId]);
            $starred = (int) $starredStmt->fetchColumn();

            $unreadStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0 AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)");
            $unreadStmt->execute([':owner_id' => $userId, ':owner_id2' => $userId]);
            $unread = (int) $unreadStmt->fetchColumn();
        } else {
            foreach (array_keys($totals) as $folder) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread
                    FROM {$this->table}
                    WHERE folder = :folder
                ");
                $stmt->execute([':folder' => $folder]);
                $row = $stmt->fetch() ?: [];
                $totals[$folder]['total'] = (int) ($row['total'] ?? 0);
                $totals[$folder]['unread'] = (int) ($row['unread'] ?? 0);
            }

            $starred = (int) $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE is_starred = 1")->fetchColumn();
            $unread = (int) $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0")->fetchColumn();
        }

        $totals['starred'] = ['total' => $starred ?? 0, 'unread' => $starred ?? 0];
        $totals['unread_total'] = ['total' => $unread ?? 0, 'unread' => $unread ?? 0];

        return $totals;
    }

    public function labelCounts(int $userId): array
    {
        if ($this->hasUserScope) {
            $stmt = $this->db->prepare("
                SELECT labels
                FROM {$this->table}
                WHERE labels <> '' AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)
            ");
            $stmt->execute([
                ':owner_id' => $userId,
                ':owner_id2' => $userId,
            ]);
        } else {
            $stmt = $this->db->query("
                SELECT labels
                FROM {$this->table}
                WHERE labels <> ''
            ");
        }

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $labels = explode(',', (string)$row['labels']);
            foreach ($labels as $label) {
                $clean = trim($label);
                if ($clean === '') {
                    continue;
                }
                $counts[$clean] = ($counts[$clean] ?? 0) + 1;
            }
        }

        ksort($counts);

        return $counts;
    }

    public function countsByFilter(int $userId): array
    {
        if ($this->hasUserScope) {
            $scope = "AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)";
            $params = [':owner_id' => $userId, ':owner_id2' => $userId];
            $scheduled = (int)$this->scalar(
                "SELECT COUNT(*) FROM {$this->table} WHERE scheduled_for IS NOT NULL AND scheduled_for >= NOW() {$scope}",
                $params
            );
            $upcoming = (int)$this->scalar(
                "SELECT COUNT(*) FROM {$this->table} WHERE category = 'events' {$scope}",
                $params
            );
            $trending = (int)$this->scalar(
                "SELECT COUNT(*) FROM {$this->table} WHERE category = 'campaign' {$scope}",
                $params
            );
            $important = (int)$this->scalar(
                "SELECT COUNT(*) FROM {$this->table} WHERE is_starred = 1 {$scope}",
                $params
            );
        } else {
            $scheduled = (int)$this->scalar("SELECT COUNT(*) FROM {$this->table} WHERE scheduled_for IS NOT NULL AND scheduled_for >= NOW()");
            $upcoming = (int)$this->scalar("SELECT COUNT(*) FROM {$this->table} WHERE category = 'events'");
            $trending = (int)$this->scalar("SELECT COUNT(*) FROM {$this->table} WHERE category = 'campaign'");
            $important = (int)$this->scalar("SELECT COUNT(*) FROM {$this->table} WHERE is_starred = 1");
        }

        return [
            'important' => $important,
            'scheduled' => $scheduled,
            'upcoming' => $upcoming,
            'trending' => $trending,
        ];
    }

    public function updateLabels(int $id, int $userId, array $labels): bool
    {
        $labelString = implode(', ', array_filter(array_map('trim', $labels)));
        return $this->updateStatus($id, $userId, ['labels' => $labelString]);
    }

    public function bulkUpdate(array $ids, int $userId, array $data): bool
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        if ($this->hasUserScope) {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE id IN (%s) AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)',
                $this->table,
                implode(', ', $sets),
                $placeholders
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE id IN (%s)',
                $this->table,
                implode(', ', $sets),
                $placeholders
            );
        }

        $stmt = $this->db->prepare($sql);

        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }

        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, (int)$id, PDO::PARAM_INT);
        }
        if ($this->hasUserScope) {
            $stmt->bindValue(':owner_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':owner_id2', $userId, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }

    public function bulkDelete(array $ids, int $userId, string $targetFolder): bool
    {
        return $this->bulkUpdate($ids, $userId, ['folder' => strtolower($targetFolder)]);
    }

    private function updateStatus(int $id, int $userId, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = :{$column}";
        }

        if ($this->hasUserScope) {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE id = :id AND (sender_user_id = :owner_id OR recipient_user_id = :owner_id2)',
                $this->table,
                implode(', ', $sets)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE id = :id',
                $this->table,
                implode(', ', $sets)
            );
        }

        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        if ($this->hasUserScope) {
            $data['owner_id'] = $userId;
            $data['owner_id2'] = $userId;
        }

        return $stmt->execute($data);
    }

    private function scalar(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    private function supportsUserScope(): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'sender_user_id'");
            $stmt->execute();
            $senderExists = (bool) $stmt->fetchColumn();

            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'recipient_user_id'");
            $stmt->execute();
            $recipientExists = (bool) $stmt->fetchColumn();

            return $senderExists && $recipientExists;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
