<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$head_title = "ANTARA - Hubungi Kami";
$header_outer_box_layout = 'login-call';
$page_title = "Hubungi Kami";
$page_sub_title = "Kontak";
?>
<?php require_once('parts/header/head.php'); ?>

<div class="page-wrapper">

    <?php require_once('parts/preloader.php'); ?>

    <?php require_once('parts/header/header.php'); ?>

    <section class="contact-hero">
        <div class="contact-hero__video">
            <video
                src="videos/hitam_1min.mp4"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
            ></video>
        </div>
        <div class="contact-hero__content">
            <div class="auto-container">
                <ul class="contact-hero__breadcrumb">
                    <li><a href="#">Beranda</a></li>
                    <li>Hubungi Kami</li>
                </ul>
                <h1 class="contact-hero__title">Hubungi ANTARA Digital Media</h1>
                <p class="contact-hero__subtitle">Sampaikan kebutuhan distribusi konten, lisensi multimedia, atau pertanyaan kemitraan. Tim kami akan merespons secepatnya.</p>
            </div>
        </div>
    </section>

    <?php
        require_once('parts/contact/contact.php');
        require_once('parts/contact/map.php');
    ?>

    <?php require_once('parts/footer/footer.php'); ?>
</div>

<?php require_once('parts/footer/footer-js.php'); ?>

<style>
    .contact-hero__content { position: absolute; inset: 0; z-index: 2; display: flex; align-items: flex-start; justify-content: center; padding: 180px 0 100px; color: #fff; pointer-events: none; }
    .contact-hero__content .auto-container { pointer-events: auto; display: flex; flex-direction: column; gap: 24px; }
    .contact-hero__breadcrumb { list-style: none; margin: 0; padding: 12px 24px; display: inline-flex; align-items: center; gap: 16px; border-radius: 36px; background: rgba(244, 246, 252, 0.92); border: 1px solid rgba(15, 23, 42, 0.16); font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; color: #0f1d3a; }
    .contact-hero__breadcrumb li { position: relative; color: #0f1d3a; font-weight: 600; }
    .contact-hero__breadcrumb li + li::before { content: ">"; margin-right: 16px; color: rgba(15, 29, 58, 0.45); font-weight: 400; }
    .contact-hero__breadcrumb a { color: rgba(15, 29, 58, 0.74); }
    .contact-hero__breadcrumb a:hover { color: #0f1d3a; }
    .contact-hero__title { font-size: clamp(34px, 5vw, 56px); font-weight: 700; margin-bottom: 12px; }
    .contact-hero__subtitle { max-width: 640px; font-size: 16px; color: rgba(232, 239, 255, 0.85); margin: 0; }
    @media (max-width: 767.98px) {
        .contact-hero__content { align-items: center; text-align: center; padding: 120px 15px 80px; }
        .contact-hero__breadcrumb { list-style: none; margin: 0; padding: 12px 24px; display: inline-flex; align-items: center; gap: 16px; border-radius: 36px; background: rgba(244, 246, 252, 0.92); border: 1px solid rgba(15, 23, 42, 0.16); font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; color: #0f1d3a; }
        .contact-hero__subtitle { margin: 0 auto; }
    }
    .contact-hero__title { color: #ffffff; }

    .contact-hero {
        position: relative;
        min-height: 520px;
        overflow: hidden;
        background-color: #0F172A;
    }
    .contact-hero__video {
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
    }
    .contact-hero__video video {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        transform: translate(-50%, -50%);
        object-fit: cover;
        pointer-events: none;
        border: 0;
        filter: brightness(0.4) saturate(1);
    }
    .contact-hero::before {
        position: absolute;
        content: "";
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.28) 0%, rgba(15, 23, 42, 0.62) 100%);
        z-index: 1;
    }
    .page-contact .contact-details { padding-top: 60px; margin-top: 0; }
    .page-contact .contact-details .sec-title { margin-bottom: 30px; }
</style>
