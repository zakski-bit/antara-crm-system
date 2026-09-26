<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth_messages = $_SESSION['auth_messages'] ?? [];
$auth_old = $_SESSION['auth_old'] ?? [];
$auth_user = $_SESSION['auth_user'] ?? null;
$auth_pending = $_SESSION['auth_pending'] ?? null;

unset($_SESSION['auth_messages'], $_SESSION['auth_old']);

$remembered_email = $_COOKIE['remembered_email'] ?? '';

$head_title = "ANTARA - Register/Login";
$header_outer_box_layout = 'login-call';
?>
<?php require_once('parts/header/head.php'); ?>

<div class="page-wrapper">

    <?php require_once('parts/preloader.php'); ?>

    <?php require_once('parts/header/header.php'); ?>

    <section class="login-section">
        <div class="login-section__video-bg">
            <video class="login-section__background-video" autoplay muted loop playsinline preload="auto">
                <source src="videos/warni_1min.mp4" type="video/mp4">
            </video>
        </div>
        <div class="auto-container login-section__content">
            <div class="login-section__heading wow fadeInDown">
                <ul class="login-section__breadcrumb">
                    <li><a href="#">Beranda</a></li>
                    <li>Masuk / Daftar</li>
                </ul>
            </div>
            <?php if (!empty($auth_messages)): ?>
                <div class="auth-alerts">
                    <?php foreach ($auth_messages as $message): ?>
                        <div class="auth-alert auth-alert--<?php echo $message['type'] === 'success' ? 'success' : 'error'; ?>">
                            <?php echo htmlspecialchars($message['text']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="row align-items-stretch g-5 login-section__grid">
                <div class="col-lg-6 login-section__info-column">
                    <div class="auth-info-box wow fadeInLeft">
                        <span class="sub-title">Jurnalisme Terpercaya</span>
                        <h2 class="title">Akses eksklusif ke dashboard kemitraan ANTARA</h2>
                        <p>Nikmati distribusi konten real-time, arsip berita lengkap, serta dukungan teknis untuk portal
                            dan media sosial Anda.</p>
                        <ul class="list-style-two">
                            <li><i class="fa fa-check-circle"></i> Statistik performa berita dan multimedia terbaru.
                            </li>
                            <li><i class="fa fa-check-circle"></i> Manajemen lisensi konten foto dan video ANTARA.</li>
                            <li><i class="fa fa-check-circle"></i> Notifikasi prioritas untuk breaking news nasional.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 login-section__form-column">
                    <?php if ($auth_user): ?>
                        <?php
                        // Generate a one-time auth token for seamless backend login
                        require_once __DIR__ . '/includes/db.php';
                        $backendUrl = 'http://localhost:8080/auth/token-login';
                        $tokenLink = '#';
                        try {
                            $pdo = getDb();
                            $token = bin2hex(random_bytes(32)); // 64 char hex token
                            $expiresAt = date('Y-m-d H:i:s', time() + 120); // 2 minutes
                    
                            // Clean up old expired tokens for this user
                            $pdo->prepare('DELETE FROM auth_tokens WHERE user_id = :uid OR expires_at < NOW()')
                                ->execute(['uid' => $auth_user['id']]);

                            // Insert new token
                            $pdo->prepare('INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (:uid, :token, :expires)')
                                ->execute([
                                    'uid' => $auth_user['id'],
                                    'token' => $token,
                                    'expires' => $expiresAt,
                                ]);

                            $tokenLink = $backendUrl . '?token=' . urlencode($token);
                        } catch (Exception $e) {
                            // Fallback: just link to backend login
                            $tokenLink = 'http://localhost:8080/login';
                        }
                        ?>
                        <div class="auth-card wow fadeInRight">
                            <h3>Hai, <?php echo htmlspecialchars($auth_user['full_name']); ?>!</h3>
                            <p class="text-muted">Anda telah masuk dengan email
                                <strong><?php echo htmlspecialchars($auth_user['email']); ?></strong>.
                            </p>
                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-3">
                                <a href="<?php echo htmlspecialchars($tokenLink); ?>"
                                    class="theme-btn btn-style-one w-100 text-center">Ke
                                    Menu Member</a>
                                <a href="includes/logout.php"
                                    class="theme-btn btn-style-one bg-dark w-100 text-center">Keluar</a>
                            </div>
                        </div>
                    <?php elseif (is_array($auth_pending) && !empty($auth_pending['email'])): ?>
                        <?php
                        $pendingEmail = (string)$auth_pending['email'];
                        $maskedEmail = preg_replace('/(^.).*(@.*$)/', '$1***$2', $pendingEmail);
                        if (!is_string($maskedEmail)) {
                            $maskedEmail = $pendingEmail;
                        }
                        ?>
                        <div class="auth-card wow fadeInRight">
                            <h3>Verifikasi OTP</h3>
                            <p class="text-muted">
                                Masukkan 6 digit kode OTP yang dikirim ke
                                <strong><?php echo htmlspecialchars($maskedEmail); ?></strong>.
                            </p>

                            <form method="post" action="includes/login-handler.php" class="default-form auth-form">
                                <input type="hidden" name="auth_step" value="verify_otp">
                                <div class="form-group">
                                    <label>Kode OTP</label>
                                    <input
                                        type="text"
                                        name="otp_code"
                                        placeholder="Contoh: 123456"
                                        inputmode="numeric"
                                        pattern="[0-9]{6}"
                                        maxlength="6"
                                        required>
                                </div>
                                <div class="form-group mt-4">
                                    <button class="theme-btn btn-style-one w-100" type="submit">Verifikasi OTP</button>
                                </div>
                            </form>

                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-3 mt-3">
                                <form method="post" action="includes/login-handler.php" class="w-100">
                                    <input type="hidden" name="auth_step" value="resend_otp">
                                    <button class="theme-btn btn-style-one bg-dark w-100" type="submit">Kirim Ulang OTP</button>
                                </form>
                                <form method="post" action="includes/login-handler.php" class="w-100">
                                    <input type="hidden" name="auth_step" value="cancel_otp">
                                    <button class="theme-btn btn-style-one w-100" type="submit">Ganti Akun</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-card wow fadeInRight">
                            <h3>Masuk ke akun Anda</h3>
                            <p class="text-muted">Gunakan email terdaftar atau masuk cepat dengan Google.</p>
                            <div class="social-login">
                                <a href="auth/google-login.php" class="theme-btn btn-style-one google-btn">
                                    <i class="fab fa-google"></i>
                                    <span>Masuk dengan Google</span>
                                </a>
                            </div>
                            <div class="divider"><span>atau</span></div>
                            <form method="post" action="includes/login-handler.php" class="default-form auth-form">
                                <input type="hidden" name="auth_step" value="password">
                                <div class="form-group">
                                    <label>Alamat Email</label>
                                    <input type="email" name="username" placeholder="contoh@antara.id" required
                                        value="<?php echo htmlspecialchars($auth_old['login_email'] ?? $remembered_email); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Kata Sandi</label>
                                    <input type="password" name="password" placeholder="Masukkan kata sandi" required>
                                </div>
                                <div class="form-options d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="form-check remember">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember-me"
                                            <?php echo $remembered_email ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="remember-me">Ingat saya</label>
                                    </div>
                                    <a href="page-reset-password.php" class="forgot">Lupa kata sandi?</a>
                                </div>
                                <div class="form-group mt-4">
                                    <button class="theme-btn btn-style-one w-100" type="submit" name="login">Masuk
                                        Sekarang</button>
                                </div>
                            </form>
                        </div>
                        <div class="auth-card secondary mt-4 wow fadeInRight" data-wow-delay=".1s">
                            <h4>Belum punya akun?</h4>
                            <p class="text-muted">Daftar untuk mendapatkan akses penuh ke layanan ANTARA Digital Media.</p>
                            <form method="post" action="includes/register-handler.php" class="default-form auth-form">
                                <div class="row gx-3">
                                    <div class="form-group col-md-12">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="full_name" placeholder="Nama Anda" required
                                            value="<?php echo htmlspecialchars($auth_old['full_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Alamat Email</label>
                                        <input type="email" name="email" placeholder="email@contoh.com" required
                                            value="<?php echo htmlspecialchars($auth_old['email'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Kata Sandi</label>
                                        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button class="theme-btn btn-style-one w-100" type="submit" name="register">Daftar
                                        Sekarang</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php require_once('parts/footer/footer.php'); ?>
</div>

<?php require_once('parts/footer/footer-js.php'); ?>

<style>
    body,
    .page-wrapper {
        background-color: #000;
    }

    .login-section {
        position: relative;
        background-size: cover;
        background-position: center;
        overflow: hidden;
        padding: 0;
        min-height: 100vh;
        background-color: #000;
    }

    .login-section__video-bg {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 0;
        background-color: #000;
    }

    .login-section__video-bg video {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        transform: translate(-50%, -50%);
        object-fit: cover;
        pointer-events: none;
        border: 0;
    }

    .login-section::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(7, 22, 40, 0.78);
        z-index: 1;
    }

    .login-section__content {
        position: relative;
        z-index: 2;
        min-height: 100vh;
        padding: clamp(120px, 18vh, 180px) 15px clamp(100px, 16vh, 160px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: clamp(32px, 5vw, 56px);
    }

    .login-section__heading {
        color: #ffffff;
        max-width: 740px;
    }

    .login-section__breadcrumb {
        list-style: none;
        margin: 0;
        padding: 12px 22px;
        display: inline-flex;
        align-items: center;
        gap: 16px;
        border-radius: 36px;
        background: rgba(244, 246, 252, 0.92);
        border: 1px solid rgba(15, 23, 42, 0.16);
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #14203d;
    }

    .login-section__breadcrumb li {
        position: relative;
        color: #14203d;
        font-weight: 600;
    }

    .login-section__breadcrumb li+li::before {
        content: ">";
        margin-right: 16px;
        color: rgba(20, 32, 61, 0.45);
        font-weight: 400;
    }

    .login-section__breadcrumb a {
        color: rgba(20, 32, 61, 0.74);
    }

    .login-section__breadcrumb a:hover {
        color: #14203d;
    }

    .login-section__grid>[class*="col-"] {
        display: flex;
    }

    .login-section__info-column {
        align-items: stretch;
    }

    .login-section__info-column .auth-info-box {
        background: rgba(8, 20, 38, 0.92);
        border: 1px solid rgba(20, 40, 72, 0.35);
        border-radius: 24px;
        padding: clamp(36px, 5vw, 48px);
        width: 100%;
        box-shadow: 0 28px 70px rgba(3, 12, 28, 0.42);
    }

    .login-section__form-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .auth-alerts {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .auth-alert {
        border-radius: 14px;
        padding: 16px 20px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .auth-alert--success {
        background: rgba(77, 194, 116, 0.18);
        border-color: rgba(77, 194, 116, 0.35);
        color: #d1ffe0;
    }

    .auth-alert--error {
        background: rgba(215, 0, 6, 0.25);
        border-color: rgba(215, 0, 6, 0.45);
        color: #ffe1e1;
    }

    .auth-info-box .sub-title {
        display: inline-block;
        font-size: 14px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 15px;
        color: #49a0ff;
    }

    .auth-info-box .title {
        color: #fff;
        font-size: 38px;
        line-height: 1.3;
        margin-bottom: 20px;
    }

    .auth-info-box p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 25px;
    }

    .auth-info-box .list-style-two li {
        color: #fff;
        font-size: 16px;
    }

    .auth-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 40px 36px;
        box-shadow: 0 30px 60px rgba(16, 30, 56, 0.15);
    }

    .auth-card h3,
    .auth-card h4 {
        margin-bottom: 12px;
    }

    .auth-card .text-muted {
        color: #576071;
    }

    .auth-card .form-group label {
        font-weight: 600;
        color: #0d1c2f;
    }

    .auth-card .form-group input {
        height: 52px;
        border-radius: 14px;
        border: 1px solid #d9dee6;
        padding: 0 20px;
        transition: all 0.3s ease;
        width: 100%;
        background: rgba(255, 255, 255, 0.98);
    }

    .auth-card .form-group textarea,
    .auth-card .form-group select {
        border-radius: 14px;
        border: 1px solid #d9dee6;
        padding: 12px 20px;
        width: 100%;
        background: rgba(255, 255, 255, 0.98);
        transition: all 0.3s ease;
    }

    .auth-card .form-group textarea:focus,
    .auth-card .form-group select:focus {
        border-color: #2457d6;
        box-shadow: 0 0 0 0.15rem rgba(36, 87, 214, 0.18);
    }

    .auth-card .form-group input:focus {
        border-color: #2457d6;
        box-shadow: 0 0 0 0.15rem rgba(36, 87, 214, 0.18);
    }

    .auth-card .form-options .forgot {
        font-weight: 600;
        color: #2457d6;
    }

    .auth-card .form-options .forgot:hover {
        text-decoration: underline;
    }

    .auth-card .theme-btn {
        border-radius: 12px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .social-login .google-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: center;
        background: #fff;
        color: #1a1a1a;
        border: 1px solid #d9dee6;
        box-shadow: 0 12px 24px rgba(16, 30, 56, 0.08);
    }

    .social-login .google-btn i {
        font-size: 20px;
        color: #ea4335;
    }

    .social-login .google-btn:hover,
    .social-login .google-btn:focus {
        background: #111827 !important;
        color: #ffffff !important;
        border-color: #0f172a !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
    }

    .social-login .google-btn:hover span,
    .social-login .google-btn:hover i,
    .social-login .google-btn:focus span,
    .social-login .google-btn:focus i {
        color: inherit !important;
    }

    .divider {
        position: relative;
        text-align: center;
        margin: 30px 0;
    }

    .divider::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 100%;
        height: 1px;
        background: #e3e7ef;
    }

    .divider span {
        position: relative;
        background: #fff;
        padding: 0 14px;
        font-weight: 600;
        color: #8690a3;
    }

    .auth-card.secondary {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(36, 87, 214, 0.1);
    }

    .auth-card.secondary .theme-btn {
        background: #0d1c2f;
    }

    .auth-card.secondary .theme-btn:hover {
        background: #12284a;
    }

    @media (max-width: 991px) {
        .login-section__content {
            padding: 120px 15px 90px;
        }

        .login-section__grid>[class*="col-"] {
            display: block;
        }

        .login-section__heading {
            text-align: center;
            margin: 0 auto;
        }

        .login-section__breadcrumb {
            list-style: none;
            margin: 0;
            padding: 12px 22px;
            display: inline-flex;
            align-items: center;
            gap: 16px;
            border-radius: 36px;
            background: rgba(244, 246, 252, 0.92);
            border: 1px solid rgba(15, 23, 42, 0.16);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #14203d;
        }

        .auth-info-box {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-info-box .title {
            font-size: 32px;
        }
    }

    @media (max-width: 575.98px) {
        .login-section__content {
            padding: 110px 15px 80px;
            gap: 32px;
        }

        .login-section__breadcrumb {
            list-style: none;
            margin: 0;
            padding: 12px 22px;
            display: inline-flex;
            align-items: center;
            gap: 16px;
            border-radius: 36px;
            background: rgba(244, 246, 252, 0.92);
            border: 1px solid rgba(15, 23, 42, 0.16);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #14203d;
        }

        .auth-card {
            padding: 32px 26px;
        }

        .auth-info-box .title {
            font-size: 28px;
        }
    }
</style>
