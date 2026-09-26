<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['auth_user']);
unset($_SESSION['auth_pending']);
unset($_SESSION['reset_pending']);
unset($_SESSION['reset_old']);
$_SESSION['auth_messages'][] = [
    'type' => 'success',
    'text' => 'Anda telah keluar dari akun.',
];

header('Location: ../page-login.php');
exit;
