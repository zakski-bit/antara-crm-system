<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Config;
use PDO;

class TokenLoginController
{
    /**
     * Handle token-based login from the frontend.
     * GET /auth/token-login?token=xxx
     */
    public function handle(Request $request): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '') {
            $this->redirectToFrontendLogin('Token tidak ditemukan.');
            return;
        }

        try {
            $pdo = \App\Core\Database::connection();

            // Find valid, non-expired token
            $stmt = $pdo->prepare(
                'SELECT t.id AS token_id, t.user_id, u.name, u.email, u.role
                 FROM auth_tokens t
                 JOIN users u ON u.id = t.user_id
                 WHERE t.token = :token AND t.expires_at > NOW()
                 LIMIT 1'
            );
            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $this->redirectToFrontendLogin('Token tidak valid atau sudah kedaluwarsa.');
                return;
            }

            // Delete the token (one-time use)
            $pdo->prepare('DELETE FROM auth_tokens WHERE id = :id')
                ->execute(['id' => $result['token_id']]);

            // Create the backend session (same as AuthController::login)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_role'] = $result['role'] ?? 'pelanggan';
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];

            // Redirect to dashboard
            $basePath = trim(Config::get('app.base_url', ''), '/');
            $dashboardPath = '/dashboard/pelanggan';
            if ($basePath !== '') {
                $dashboardPath = '/' . $basePath . $dashboardPath;
            }

            header('Location: ' . $dashboardPath, true, 302);
            exit;

        } catch (\Throwable $e) {
            $this->redirectToFrontendLogin('Terjadi kesalahan. Silakan coba lagi.');
            return;
        }
    }

    private function redirectToFrontendLogin(string $message = ''): void
    {
        $url = 'http://localhost:8082/page-login.php';
        if ($message !== '') {
            $url .= '?error=' . urlencode($message);
        }
        header('Location: ' . $url, true, 302);
        exit;
    }
}
