<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authMessages = $_SESSION['auth_messages'] ?? [];
$resetOld = $_SESSION['reset_old'] ?? [];
$resetPending = $_SESSION['reset_pending'] ?? null;

unset($_SESSION['auth_messages'], $_SESSION['reset_old']);

/** @var array<string, mixed> $authConfig */
$authConfig = require __DIR__ . '/includes/auth-config.php';
$otpConfig = is_array($authConfig['otp'] ?? null) ? $authConfig['otp'] : [];
$otpLength = max(4, (int)($otpConfig['length'] ?? 6));

$pendingEmail = (string)(is_array($resetPending) ? ($resetPending['email'] ?? '') : '');
$maskedPendingEmail = $pendingEmail !== '' ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $pendingEmail) : '';
if (!is_string($maskedPendingEmail)) {
    $maskedPendingEmail = $pendingEmail;
}

$head_title = "ANTARA - Reset Password";
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
                    <li>Reset Kata Sandi</li>
                </ul>
            </div>

            <?php if (!empty($authMessages)): ?>
                <div class="auth-alerts">
                    <?php foreach ($authMessages as $message): ?>
                        <div class="auth-alert auth-alert--<?php echo ($message['type'] ?? '') === 'success' ? 'success' : 'error'; ?>">
                            <?php echo htmlspecialchars((string)($message['text'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row align-items-stretch g-5 login-section__grid">
                <div class="col-lg-6 login-section__info-column">
                    <div class="auth-info-box wow fadeInLeft">
                        <span class="sub-title">Pemulihan Akun</span>
                        <h2 class="title">Reset kata sandi dengan OTP email</h2>
                        <p>Untuk keamanan, reset kata sandi hanya diproses melalui OTP yang dikirim ke email akun real.</p>
                        <ul class="list-style-two">
                            <li><i class="fa fa-check-circle"></i> OTP berlaku terbatas waktu.</li>
                            <li><i class="fa fa-check-circle"></i> Kode OTP hanya bisa dipakai sekali.</li>
                            <li><i class="fa fa-check-circle"></i> Akun demo tidak diproses oleh fitur ini.</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6 login-section__form-column">
                    <?php if (is_array($resetPending) && !empty($pendingEmail)): ?>
                        <div class="auth-card wow fadeInRight">
                            <h3>Verifikasi OTP & Kata Sandi Baru</h3>
                            <p class="text-muted">
                                Masukkan OTP <?php echo (int)$otpLength; ?> digit yang dikirim ke
                                <strong><?php echo htmlspecialchars($maskedPendingEmail, ENT_QUOTES, 'UTF-8'); ?></strong>.
                            </p>

                            <form method="post" action="includes/password-reset-handler.php" class="default-form auth-form">
                                <input type="hidden" name="reset_step" value="verify_reset">

                                <div class="form-group">
                                    <label>Kode OTP</label>
                                    <input
                                        type="text"
                                        name="otp_code"
                                        inputmode="numeric"
                                        pattern="[0-9]{<?php echo (int)$otpLength; ?>}"
                                        maxlength="<?php echo (int)$otpLength; ?>"
                                        placeholder="Contoh: <?php echo str_repeat('0', (int)$otpLength); ?>"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Kata Sandi Baru</label>
                                    <input type="password" name="new_password" placeholder="Minimal 8 karakter (huruf & angka)" required>
                                </div>

                                <div class="form-group">
                                    <label>Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" name="confirm_password" placeholder="Ulangi kata sandi baru" required>
                                </div>

                                <div class="form-group mt-4">
                                    <button class="theme-btn btn-style-one w-100" type="submit">Simpan Kata Sandi Baru</button>
                                </div>
                            </form>

                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-3 mt-3">
                                <form method="post" action="includes/password-reset-handler.php" class="w-100">
                                    <input type="hidden" name="reset_step" value="resend_otp">
                                    <button class="theme-btn btn-style-one bg-dark w-100" type="submit">Kirim Ulang OTP</button>
                                </form>
                                <form method="post" action="includes/password-reset-handler.php" class="w-100">
                                    <input type="hidden" name="reset_step" value="cancel_reset">
                                    <button class="theme-btn btn-style-one w-100" type="submit">Batalkan</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-card wow fadeInRight">
                            <h3>Lupa Kata Sandi</h3>
                            <p class="text-muted">Masukkan email akun Anda. Kami akan kirim OTP untuk reset kata sandi.</p>

                            <form method="post" action="includes/password-reset-handler.php" class="default-form auth-form">
                                <input type="hidden" name="reset_step" value="request_otp">
                                <div class="form-group">
                                    <label>Alamat Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="contoh@antara.id"
                                        value="<?php echo htmlspecialchars((string)($resetOld['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                        required>
                                </div>
                                <div class="form-group mt-4">
                                    <button class="theme-btn btn-style-one w-100" type="submit">Kirim OTP Reset</button>
                                </div>
                            </form>

                            <div class="mt-3 text-center">
                                <a href="page-login.php" class="forgot">Kembali ke Login</a>
                            </div>
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

    .auth-card .form-group label {
        font-weight: 600;
        color: #0d1c2f;
    }

    .auth-card .form-group input {
        height: 52px;
        border-radius: 14px;
        border: 1px solid #d9dee6;
        padding: 0 20px;
    }
</style>
