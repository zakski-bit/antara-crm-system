<?php
/**
 * Registration handler that stores new users in the MariaDB users table
 * in the backend's antara_crm database.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../page-login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$fullName = trim($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$_SESSION['auth_old'] = [
    'full_name' => $fullName,
    'email' => $email,
];

function addMessage(string $type, string $text): void
{
    $_SESSION['auth_messages'][] = [
        'type' => $type,
        'text' => $text,
    ];
}

if ($fullName === '' || $email === '' || $password === '') {
    addMessage('error', 'Mohon lengkapi seluruh kolom registrasi.');
    header('Location: ../page-login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    addMessage('error', 'Format email tidak valid.');
    header('Location: ../page-login.php');
    exit;
}

if (strlen($password) < 8) {
    addMessage('error', 'Kata sandi harus memiliki minimal 8 karakter.');
    header('Location: ../page-login.php');
    exit;
}

try {
    $pdo = getDb();

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        addMessage('error', 'Email sudah digunakan. Silakan gunakan email lain atau masuk.');
        header('Location: ../page-login.php');
        exit;
    }

    // Insert new user with default role 'pelanggan'
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (:name, :email, :password, :role, :created_at, :updated_at)');
    $stmt->execute([
        'name' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'customer',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
} catch (PDOException $e) {
    addMessage('error', 'Koneksi database gagal. Silakan coba lagi nanti.');
    header('Location: ../page-login.php');
    exit;
}

unset($_SESSION['auth_old']);

addMessage('success', 'Registrasi berhasil. Silakan masuk dengan kredensial Anda.');
header('Location: ../page-login.php');
exit;
