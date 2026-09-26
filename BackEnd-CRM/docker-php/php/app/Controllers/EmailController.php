<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\EmailRepository;

class EmailController extends Controller
{
    private EmailRepository $emails;

    private array $supportedFolders = [
        'inbox',
        'starred',
        'sent',
        'deleted',
    ];

    private array $supportedFilters = [];

    public function __construct()
    {
        $this->emails = new EmailRepository();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $this->renderMailbox($request, null);
    }

    public function show(Request $request, int $id): void
    {
        $this->renderMailbox($request, $id);
    }

    public function compose(Request $request): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $input = [
            'to_user_id' => (int) $request->input('to_user_id'),
            'subject' => trim((string)$request->input('subject')),
            'body_html' => trim((string)$request->input('body_html')),
        ];

        $errors = $this->validateComposeInput($input);
        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($input);
            $this->flash('error', 'Gagal mengirim korespondensi. Periksa kembali data yang diisi.');
            $this->redirect($this->mailboxUrl(['folder' => 'sent']));
        }

        $bodyHtml = $this->sanitizeRichText($input['body_html']);
        $bodyText = trim(strip_tags($bodyHtml));
        $snippet = $this->makeSnippet($bodyText);

        $senderName = $_SESSION['user_name'] ?? 'Redaksi Antara';
        $senderEmail = $_SESSION['user_email'] ?? 'no-reply@antara.id';
        $recipient = $this->findUser($input['to_user_id']);
        if (!$recipient) {
            $this->pushErrors(['to_user_id' => 'Penerima tidak ditemukan.']);
            $this->pushOld($input);
            $this->flash('error', 'Gagal mengirim korespondensi. Penerima tidak valid.');
            $this->redirect($this->mailboxUrl(['folder' => 'sent']));
        }

        $recipientName = $recipient['name'] ?? 'Pengguna';
        $recipientEmail = $recipient['email'] ?? 'user@antara.local';
        $now = date('Y-m-d H:i:s');

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentErrors = [];
        $attachmentFile = $request->file('attachment');
        if ($attachmentFile && ($attachmentFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploaded = $this->handleAttachmentUpload($attachmentFile, $attachmentErrors);
            if ($uploaded) {
                $attachmentPath = $uploaded;
                $attachmentName = $this->originalFileName($attachmentFile['name'] ?? '');
            } else {
                $errors = array_merge($errors, $attachmentErrors);
                $this->pushErrors($errors);
                $this->pushOld($input);
                $this->flash('error', 'Gagal mengunggah lampiran.');
                $this->redirect($this->mailboxUrl(['folder' => 'sent']));
            }
        }

        // Salin untuk kotak masuk penerima
        $inboxData = [
            'folder' => 'inbox',
            'subject' => $input['subject'],
            'snippet' => $snippet,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'to_addresses' => $recipientName,
            'cc_addresses' => null,
            'is_read' => 0,
            'is_starred' => 0,
            'has_attachments' => $attachmentPath ? 1 : 0,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'category' => 'correspondence',
            'labels' => '',
            'scheduled_for' => null,
            'received_at' => $now,
            'sender_user_id' => $userId,
            'recipient_user_id' => $input['to_user_id'],
        ];

        // Salin untuk kotak terkirim pengirim
        $sentData = $inboxData;
        $sentData['folder'] = 'sent';
        $sentData['is_read'] = 1;
        $sentData['recipient_user_id'] = $input['to_user_id'];
        $sentData['has_attachments'] = $attachmentPath ? 1 : 0;
        $sentData['attachment_name'] = $attachmentName;

        $this->emails->create($sentData);
        $this->emails->create($inboxData);

        $this->flash('success', 'Pesan terkirim.');
        $this->redirect($this->mailboxUrl(['folder' => 'sent']));
    }

    public function star(Request $request, int $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $this->emails->toggleStar($id, $userId, true);
        $this->flash('success', 'Email ditandai sebagai favorit.');
        $this->redirect($this->redirectBack($request));
    }

    public function unstar(Request $request, int $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $this->emails->toggleStar($id, $userId, false);
        $this->flash('success', 'Penanda favorit dihapus.');
        $this->redirect($this->redirectBack($request));
    }

    public function markRead(Request $request, int $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $this->emails->markRead($id, $userId, true);
        $this->flash('success', 'Email ditandai sebagai sudah dibaca.');
        $this->redirect($this->redirectBack($request));
    }

    public function markUnread(Request $request, int $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $this->emails->markRead($id, $userId, false);
        $this->flash('success', 'Email ditandai sebagai belum dibaca.');
        $this->redirect($this->redirectBack($request));
    }

    public function move(Request $request, int $id): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $requestedTarget = $this->normalizeFolder((string)$request->input('target', 'inbox'));

        // Jika restore dari Deleted, tentukan folder asal berdasarkan kepemilikan
        if ($requestedTarget === 'inbox') {
            $message = $this->emails->find($id, $userId);
            if ($message) {
                $senderId = (int) ($message['sender_user_id'] ?? 0);
                $recipientId = (int) ($message['recipient_user_id'] ?? 0);
                if ($senderId === $userId && $recipientId !== $userId) {
                    // Pesan milik pengirim (sent), kembalikan ke Sent
                    $requestedTarget = 'sent';
                } else {
                    // Pesan masuk, kembalikan ke Inbox
                    $requestedTarget = 'inbox';
                }
            }
        }

        $this->emails->moveToFolder($id, $userId, $requestedTarget);

        switch ($target) {
            case 'deleted':
                $message = 'Email dipindahkan ke folder Deleted.';
                break;
            case 'spam':
                $message = 'Email dipindahkan ke folder Spam.';
                break;
            case 'drafts':
                $message = 'Email dipindahkan ke Drafts.';
                break;
            default:
                $message = 'Email berhasil dipindahkan.';
                break;
        }

        $this->flash('success', $message);
        $this->redirect($this->redirectBack($request, ['folder' => $target]));
    }

    public function bulk(Request $request): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $ids = $this->sanitizeIds($request->input('ids', []));

        if (empty($ids)) {
            $this->flash('error', 'Pilih minimal satu email untuk melakukan aksi.');
            $this->redirect($this->redirectBack($request));
        }

        $action = trim((string)$request->input('action'));

        switch ($action) {
            case 'mark_read':
                $this->emails->bulkUpdate($ids, $userId, ['is_read' => 1]);
                $message = 'Email dipindahkan ke status sudah dibaca.';
                break;
            case 'mark_unread':
                $this->emails->bulkUpdate($ids, $userId, ['is_read' => 0]);
                $message = 'Email ditandai sebagai belum dibaca.';
                break;
            case 'star':
                $this->emails->bulkUpdate($ids, $userId, ['is_starred' => 1]);
                $message = 'Email ditandai sebagai penting.';
                break;
            case 'unstar':
                $this->emails->bulkUpdate($ids, $userId, ['is_starred' => 0]);
                $message = 'Penanda penting dihapus.';
                break;
            case 'delete':
                $this->emails->bulkDelete($ids, $userId, 'deleted');
                $message = 'Email dipindahkan ke folder Deleted.';
                break;
            case 'spam':
                $this->emails->bulkDelete($ids, $userId, 'spam');
                $message = 'Email dipindahkan ke folder Spam.';
                break;
            case 'inbox':
                $this->emails->bulkDelete($ids, $userId, 'inbox');
                $message = 'Email dikembalikan ke Inbox.';
                break;
            default:
                $this->flash('error', 'Aksi bulk tidak dikenali.');
                $this->redirect($this->redirectBack($request));
        }

        $this->flash('success', $message);
        $this->redirect($this->redirectBack($request));
    }

    private function renderMailbox(Request $request, ?int $messageId): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $folder = $this->normalizeFolder((string)$request->query('folder', 'inbox'));
        $search = trim((string)$request->query('q', ''));

        if ($messageId === null) {
            $queryMessage = $request->query('message');
            if ($queryMessage !== null && ctype_digit((string)$queryMessage)) {
                $messageId = (int)$queryMessage;
            }
        }

        $flash = $this->pullFlash();
        $errors = $this->pullErrors();
        $old = $this->pullOld();

        $filter = $this->normalizeFilter((string)$request->query('filter', ''));
        $label = trim((string)$request->query('label', ''));

        $messages = $this->emails->list($userId, $folder, $filter, $search, $label);
        $transformedMessages = array_map([$this, 'transformListItem'], $messages);
        $counts = $this->normalizeCounts($this->emails->counts($userId));
        $filterCounts = $this->emails->countsByFilter($userId);
        $labelCounts = $this->emails->labelCounts($userId);

        $activeMessage = null;
        if ($messageId !== null) {
            $activeMessage = $this->emails->find($messageId, $userId);
            if ($activeMessage && $activeMessage['is_read'] == 0) {
                $this->emails->markRead($messageId, $userId, true);
                $activeMessage['is_read'] = 1;
            }

            if ($activeMessage && !in_array($folder, ['starred', 'unread'], true) && $activeMessage['folder'] !== $folder) {
                $folder = $this->normalizeFolder((string)$activeMessage['folder']);
                $messages = $this->emails->list($userId, $folder, $filter, $search, $label);
                $transformedMessages = array_map([$this, 'transformListItem'], $messages);
            }
        }

        if ($activeMessage === null && !empty($messages)) {
            $activeMessage = $messages[0];
            if ($activeMessage['is_read'] == 0) {
                $this->emails->markRead((int)$activeMessage['id'], true);
                $activeMessage['is_read'] = 1;
            }
        }

        $activeMessageView = $activeMessage ? $this->transformDetail($activeMessage) : null;

        $baseUrl = $this->baseUrl();

        $currentPath = $this->mailboxUrl([
            'folder' => $folder,
            'q' => $search !== '' ? $search : null,
            'filter' => $filter !== '' ? $filter : null,
            'label' => $label !== '' ? $label : null,
            'message' => $activeMessageView['id'] ?? null,
        ]);

        $_SERVER['PHP_SELF'] = '/email.php';
        $_SERVER['REQUEST_URI'] = $currentPath;

        $recipientOptions = $this->recipientOptions($userId);

        $this->view('email/index', [
            'folder' => $folder,
            'search' => $search,
            'messages' => $transformedMessages,
            'activeMessage' => $activeMessageView,
            'counts' => $counts,
            'filterCounts' => $filterCounts,
            'labelCounts' => $labelCounts,
            'flash' => $flash,
            'errors' => $errors,
            'old' => $old,
            'baseUrl' => $baseUrl,
            'currentPath' => $currentPath,
            'filter' => $filter,
            'label' => $label,
            'recipientOptions' => $recipientOptions,
            'oldInput' => $old,
            'errorsCompose' => $errors,
        ]);
    }

    private function validateComposeInput(array $input): array
    {
        $errors = [];

        if (empty($input['to_user_id']) || (int) $input['to_user_id'] <= 0) {
            $errors['to_user_id'] = 'Pilih penerima.';
        }

        if ($input['subject'] === '') {
            $errors['subject'] = 'Subjek wajib diisi.';
        }

        if ($input['body_html'] === '') {
            $errors['body_html'] = 'Isi pesan tidak boleh kosong.';
        }

        return $errors;
    }

    private function convertBodyToHtml(string $body): string
    {
        $safe = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
        $paragraphs = preg_split("/\r\n|\r|\n/", $safe);

        $buffer = [];
        $current = [];
        foreach ($paragraphs as $line) {
            if ($line === '') {
                if (!empty($current)) {
                    $buffer[] = '<p>' . implode('<br>', $current) . '</p>';
                    $current = [];
                }
                continue;
            }
            $current[] = $line;
        }

        if (!empty($current)) {
            $buffer[] = '<p>' . implode('<br>', $current) . '</p>';
        }

        return implode("\n", $buffer);
    }

    private function makeSnippet(string $body): string
    {
        $trimmed = trim(preg_replace('/\s+/', ' ', $body));
        if ($trimmed === '') {
            return '';
        }

        if (mb_strlen($trimmed) <= 120) {
            return $trimmed;
        }

        return mb_substr($trimmed, 0, 117) . '...';
    }

    private function transformListItem(array $message): array
    {
        $timestamp = $this->resolveTimestamp($message);
        $sender = trim((string)($message['sender_name'] ?? '')) !== ''
            ? $message['sender_name']
            : $message['sender_email'];

        return [
            'id' => (int)$message['id'],
            'folder' => strtolower((string)$message['folder']),
            'sender' => $sender,
            'sender_email' => $message['sender_email'],
            'sender_user_id' => isset($message['sender_user_id']) ? (int)$message['sender_user_id'] : null,
            'recipient_user_id' => isset($message['recipient_user_id']) ? (int)$message['recipient_user_id'] : null,
            'initials' => $this->initials($sender),
            'subject' => $message['subject'],
            'snippet' => $message['snippet'],
            'is_read' => (bool)$message['is_read'],
            'is_starred' => (bool)$message['is_starred'],
            'has_attachments' => (bool)$message['has_attachments'],
            'received_at' => $timestamp,
            'time_label' => $this->formatTimestampShort($timestamp),
            'labels' => $this->extractLabels($message),
            'category' => (string)($message['category'] ?? 'general'),
            'scheduled_for' => $message['scheduled_for'] ?? null,
            'scheduled_label' => $this->formatScheduledLabel($message['scheduled_for'] ?? null),
            'to_addresses' => $message['to_addresses'],
            'cc_addresses' => $message['cc_addresses'],
            'body_text' => $message['body_text'],
        ];
    }

    private function transformDetail(array $message): array
    {
        $timestamp = $this->resolveTimestamp($message);
        $senderLabel = $message['sender_name'] ?: $message['sender_email'];

        return [
            'id' => (int)$message['id'],
            'folder' => strtolower((string)$message['folder']),
            'subject' => $message['subject'],
            'body_html' => $message['body_html'],
            'body_text' => $message['body_text'],
            'attachment_path' => $message['attachment_path'] ?? null,
            'attachment_name' => $message['attachment_name'] ?? null,
            'sender_name' => $senderLabel,
            'sender_email' => $message['sender_email'],
            'sender_user_id' => isset($message['sender_user_id']) ? (int)$message['sender_user_id'] : null,
            'recipient_user_id' => isset($message['recipient_user_id']) ? (int)$message['recipient_user_id'] : null,
            'to_addresses' => $message['to_addresses'],
            'cc_addresses' => $message['cc_addresses'],
            'is_starred' => (bool)$message['is_starred'],
            'is_read' => (bool)$message['is_read'],
            'has_attachments' => (bool)$message['has_attachments'],
            'received_at' => $timestamp,
            'time_label' => $this->formatTimestampFull($timestamp),
            'initials' => $this->initials($senderLabel),
            'labels' => $this->extractLabels($message),
            'category' => (string)($message['category'] ?? 'general'),
            'scheduled_for' => $message['scheduled_for'] ?? null,
            'scheduled_label' => $this->formatScheduledLabel($message['scheduled_for'] ?? null),
        ];
    }

    private function resolveTimestamp(array $message): int
    {
        $value = $message['received_at'] ?? $message['created_at'] ?? null;
        if ($value === null) {
            return time();
        }

        $timestamp = strtotime((string)$value);
        return $timestamp ?: time();
    }

    private function initials(string $name): string
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return 'NA';
        }

        $words = preg_split('/\s+/', $trimmed);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return substr($initials, 0, 2);
    }

    private function formatTimestampShort(int $timestamp): string
    {
        $today = strtotime('today');
        $yesterday = strtotime('yesterday');

        if ($timestamp >= $today) {
            return date('H:i', $timestamp);
        }

        if ($timestamp >= $yesterday) {
            return 'Kemarin';
        }

        return date('d M', $timestamp);
    }

    private function formatTimestampFull(int $timestamp): string
    {
        return date('l, d M Y H:i', $timestamp) . ' WIB';
    }

    private function normalizeCounts(array $raw): array
    {
        $defaults = [
            'inbox' => ['total' => 0, 'unread' => 0],
            'starred' => ['total' => 0, 'unread' => 0],
            'sent' => ['total' => 0, 'unread' => 0],
            'drafts' => ['total' => 0, 'unread' => 0],
            'deleted' => ['total' => 0, 'unread' => 0],
            'spam' => ['total' => 0, 'unread' => 0],
            'unread_total' => ['total' => 0, 'unread' => 0],
        ];

        return array_merge($defaults, $raw);
    }

    private function extractLabels(array $message): array
    {
        $raw = $message['labels'] ?? '';
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $parts = explode(',', $raw);
        return array_values(array_filter(array_map('trim', $parts), static fn ($label) => $label !== ''));
    }

    private function normalizeFolder(string $folder): string
    {
        $folder = strtolower(trim($folder));
        if (!in_array($folder, $this->supportedFolders, true)) {
            return 'inbox';
        }

        return $folder;
    }

    private function normalizeFilter(string $filter): string
    {
        $filter = strtolower(trim($filter));
        if ($filter === '' || !in_array($filter, $this->supportedFilters, true)) {
            return '';
        }

        return $filter;
    }

    private function formatScheduledLabel($scheduledFor): ?string
    {
        if (!$scheduledFor) {
            return null;
        }

        $timestamp = strtotime((string)$scheduledFor);
        if (!$timestamp) {
            return null;
        }

        return 'Scheduled ' . date('d M Y H:i', $timestamp);
    }

    private function redirectBack(Request $request, array $fallbackQuery = []): string
    {
        $redirect = trim((string)$request->input('redirect'));
        if ($redirect !== '' && $redirect[0] === '/') {
            return $redirect;
        }

        return $this->mailboxUrl($fallbackQuery);
    }

    private function recipientOptions(int $userId): array
    {
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id <> :id ORDER BY name ASC");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    private function findUser(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function originalFileName(string $name): string
    {
        $clean = trim(basename($name));
        return $clean === '' ? 'lampiran' : $clean;
    }

    private function sanitizeRichText(string $html): string
    {
        $allowed = '<p><br><strong><em><u><ol><ul><li><blockquote><code><pre><b><i><span><div><a><h1><h2><h3><h4><h5><h6>';
        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/on\\w+=\"[^\"]*\"/i', '', $clean);
        $clean = preg_replace('/javascript:/i', '', $clean);
        return trim($clean);
    }

    private function handleAttachmentUpload(array $file, array &$errors): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            $errors['attachment'] = 'Upload lampiran gagal.';
            return null;
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            $errors['attachment'] = 'Format lampiran tidak didukung.';
            return null;
        }

        if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
            $errors['attachment'] = 'Ukuran lampiran maksimal 8 MB.';
            return null;
        }

        $destinationDir = BASE_PATH . '/assets/uploads/correspondence';
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $filename = 'correspondence-' . uniqid('', true) . '.' . $extension;
        $targetPath = $destinationDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors['attachment'] = 'Gagal menyimpan lampiran.';
            return null;
        }

        return 'assets/uploads/correspondence/' . $filename;
    }

    private function mailboxUrl(array $query = []): string
    {
        $base = $this->baseUrl();
        $path = $base . '/dashboard/email';

        $query = array_filter($query, static fn ($value) => $value !== null && $value !== '');
        if (!empty($query)) {
            $path .= '?' . http_build_query($query);
        }

        return $path;
    }

    private function baseUrl(): string
    {
        $base = trim($this->config('app.base_url', ''), '/');
        return $base === '' ? '' : '/' . $base;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['email_flash'][$type] = $message;
    }

    private function pullFlash(): array
    {
        $flash = $_SESSION['email_flash'] ?? [];
        unset($_SESSION['email_flash']);
        return $flash;
    }

    private function pushErrors(array $errors): void
    {
        $_SESSION['email_errors'] = $errors;
    }

    private function pullErrors(): array
    {
        $errors = $_SESSION['email_errors'] ?? [];
        unset($_SESSION['email_errors']);
        return $errors;
    }

    private function pushOld(array $input): void
    {
        $_SESSION['email_old'] = $input;
    }

    private function pullOld(): array
    {
        $old = $_SESSION['email_old'] ?? [];
        unset($_SESSION['email_old']);
        return $old;
    }

    private function sanitizeIds($ids): array
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $clean = [];
        foreach ($ids as $id) {
            if (is_numeric($id) && (int)$id > 0) {
                $clean[] = (int)$id;
            }
        }

        return array_values(array_unique($clean));
    }
}
