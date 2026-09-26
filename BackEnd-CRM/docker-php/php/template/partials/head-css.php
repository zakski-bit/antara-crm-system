<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

$assetBaseConfig = '';
if (class_exists('\App\Core\Config')) {
    $assetBaseConfig = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
$assetBaseUrl = $assetBaseConfig === '' ? '/assets' : '/' . $assetBaseConfig . '/assets';
$documentBase = $assetBaseConfig === '' ? '/' : '/' . $assetBaseConfig . '/';
$faviconVersion = '20260304-antara-fix2';
$faviconFile = 'img/logotab.png';
$asset = static function (string $path) use ($assetBaseUrl): string {
	return htmlspecialchars($assetBaseUrl . '/' . ltrim($path, '/'), ENT_QUOTES, 'UTF-8');
};
?>

	<base href="<?= htmlspecialchars($documentBase, ENT_QUOTES, 'UTF-8'); ?>">

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?= urlencode($faviconVersion); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v=<?= urlencode($faviconVersion); ?>">
	<link rel="icon" type="image/png" sizes="192x192" href="<?= $asset($faviconFile); ?>?v=<?= urlencode($faviconVersion); ?>">
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=<?= urlencode($faviconVersion); ?>">

	<!-- Apple Touch Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?= $asset($faviconFile); ?>?v=<?= urlencode($faviconVersion); ?>">
	<script>
	(function () {
		var ver = "<?= htmlspecialchars($faviconVersion, ENT_QUOTES, 'UTF-8'); ?>";
		var icons = [
			{ rel: "icon", type: "image/x-icon", href: "/favicon.ico?v=" + ver },
			{ rel: "shortcut icon", type: "image/x-icon", href: "/favicon.ico?v=" + ver },
			{ rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon.png?v=" + ver },
			{ rel: "icon", type: "image/png", sizes: "192x192", href: "<?= $asset($faviconFile); ?>?v=" + ver }
		];
		var head = document.head || document.getElementsByTagName("head")[0];
		icons.forEach(function (meta) {
			var link = document.createElement("link");
			link.rel = meta.rel;
			link.href = meta.href;
			if (meta.type) link.type = meta.type;
			if (meta.sizes) link.sizes = meta.sizes;
			head.appendChild(link);
		});
	})();
	</script>

<?php if ($page !== 'login.php' && $page !== 'layout-horizontal.php' && $page !== 'layout-detached.php' && $page !== 'layout-modern.php' && $page !== 'layout-horizontal-overlay.php' && $page !== 'layout-hovered.php' && $page !== 'layout-box.php' && $page !== 'layout-horizontal-single.php' && $page !== 'layout-horizontal-box.php' && $page !== 'layout-horizontal-fullwidth.php' && $page !== 'layout-stacked.php' && $page !== 'layout-horizontal-sidemenu.php' && $page !== 'layout-vertical-transparent.php' && $page !== 'layout-without-header.php' && $page !== 'login-2.php' && $page !== 'login-3.php' && $page !== 'register.php' && $page !== 'register-2.php' && $page !== 'register-3.php' && $page !== 'forgot-password.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password-3.php' && $page !== 'reset-password.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password-3.php' && $page !== 'email-verification.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification-3.php' && $page !== 'lock-screen.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'coming-soon.php' && $page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'two-step-verification.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification-3.php' && $page !== 'layout-rtl.php' && $page !== 'layout-dark.php' && $page !== 'job-grid-2.php' && $page !== 'job-list-2.php' && $page !== 'job-details.php') { ?>
    <!-- Theme Script js -->
    <script src="<?= $asset('js/theme-script.js'); ?>"></script>
<?php } ?>

<?php if ($page !== 'layout-rtl.php') { ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= $asset('css/bootstrap.min.css'); ?>">
<?php } ?>

<?php if ($page === 'layout-rtl.php') { ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= $asset('css/bootstrap.rtl.min.css'); ?>">
<?php } ?>

<?php if ($page === 'employee-salary.php' || $page === 'project-details.php' || $page === 'gallery.php' || $page === 'plugins.php' || $page === 'search-result.php' || $page === 'social-feed.php') { ?>
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/fancybox/jquery.fancybox.min.css'); ?>">
<?php } ?>

<!-- Feather CSS -->
<link rel="stylesheet" href="<?= $asset('plugins/icons/feather/feather.css'); ?>">

<?php if ($page === 'icon-bootstrap.php') { ?>
    <!-- Bootstrap Icon CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/bootstrap/bootstrap-icons.min.css'); ?>">
<?php } ?>

<!-- Tabler Icon CSS -->
<link rel="stylesheet" href="<?= $asset('plugins/tabler-icons/tabler-icons.css'); ?>">

<?php if ($page === 'icon-remix.php') { ?>
    <!-- Remix Icon CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/remix/fonts/remixicon.css'); ?>">
<?php } ?>

<?php if ($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') { ?>
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/select2/css/select2.min.css'); ?>">
<?php } ?>

<?php if ($page === 'maps-leaflet.php') { ?>
    <!-- Leaflet Maps CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/leaflet/leaflet.css'); ?>">
<?php } ?>

<?php if ($page === 'maps-vector.php') { ?>
    <!-- Jsvector Maps -->
    <link rel="stylesheet" href="<?= $asset('plugins/jsvectormap/css/jsvectormap.min.css'); ?>">
<?php } ?>

<?php if ($page === 'ui-scrollbar.php') { ?>
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/scrollbar/scroll.min.css'); ?>">
<?php } ?>

<?php if ($page === 'ui-stickynote.php') { ?>
    <!-- Sticky CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/stickynote/sticky.css'); ?>">
<?php } ?>

<!-- Fontawesome CSS -->
<link rel="stylesheet" href="<?= $asset('plugins/fontawesome/css/fontawesome.min.css'); ?>">
<link rel="stylesheet" href="<?= $asset('plugins/fontawesome/css/all.min.css'); ?>">

<?php if ($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') { ?>
    <!-- Color Picker Css -->
    <link rel="stylesheet" href="<?= $asset('plugins/flatpickr/flatpickr.min.css'); ?>">
    <link rel="stylesheet" href="<?= $asset('plugins/@simonwep/pickr/themes/nano.min.css'); ?>">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/daterangepicker/daterangepicker.css'); ?>">
<?php } ?>

<?php if ($page === 'email-reply.php' || $page === 'email.php' || $page === 'notes.php' || $page === 'plugin.php' || $page === 'project-details.php' || $page === 'social-feed.php' || $page === 'task-details.php') { ?>
    <!-- Owl carousel CSS -->
    <link rel="stylesheet" href="<?= $asset('css/owl.carousel.min.css'); ?>">
<?php } ?>

<?php if ($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') { ?>
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="<?= $asset('css/dataTables.bootstrap5.min.css'); ?>">
    <link rel="stylesheet" href="<?= $asset('css/buttons.bootstrap5.min.css'); ?>">
<?php } ?>

<?php if ($page === 'add-invoices.php' || $page === 'edit-invoices.php' || $page === 'file-manager.php' || $page === 'group-video-call.php' || $page === 'invoice-details.php' || $page === 'invoices.php' || $page === 'manage-jobs.php' || $page === 'maps-vector.php' || $page === 'payslip.php' || $page === 'promotion.php' || $page === 'resignation.php' || $page === 'termination.php') { ?>
    <!-- Player CSS -->
    <link rel="stylesheet" href="<?= $asset('css/plyr.css'); ?>">
<?php } ?>

<?php if ($page === 'add-invoices.php' || $page === 'edit-invoices.php' || $page === 'file-manager.php' || $page === 'group-video-call.php' || $page === 'invoice-details.php' || $page === 'invoices.php' || $page === 'manage-jobs.php' || $page === 'maps-vector.php' || $page === 'payslip.php' || $page === 'promotion.php' || $page === 'resignation.php' || $page === 'termination.php') { ?>
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="<?= $asset('plugins/owlcarousel/owl.carousel.min.css'); ?>">
<?php } ?>


<?php if ($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') { ?>
    <!-- Summernote CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/summernote/summernote-lite.min.css'); ?>">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="<?= $asset('css/bootstrap-datetimepicker.min.css'); ?>">
<?php } ?>

<?php if ($page === 'ui-drag-drop.php' || $page === 'form-pickers.php' || $page === 'icon-bootstrap.php' || $page === 'icon-remix.php' || $page === 'icon-tabler.php' || $page === 'maps-leaflet.php' || $page === 'maps-vector.php' || $page === 'plugin.php' || $page === 'ui-clipboard.php' || $page === 'ui-sortable.php' || $page === 'ui-swiperjs.php') { ?>
    <!-- Dragula CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/dragula/css/dragula.min.css'); ?>">
<?php } ?>

<?php if ($page === 'ui-swiperjs.php' || $page === 'maps-google.php' || $page === 'ui-sortable.php') { ?>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/swiper/swiper-bundle.min.css'); ?>">
<?php } ?>

<?php if ($page === 'chart-c3.php' || $page === 'plugin.php') { ?>
    <!-- ChartC3 CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/c3-chart/c3.min.css'); ?>">
<?php } ?>

<?php if ($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') { ?>
    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'); ?>">
<?php } ?>

<?php if ($page === 'job-grid-2.php' || $page === 'job-list-2.php' || $page === 'plugin.php' || $page === 'ui-rangeslider.php' || $page === 'ui-rating.php') { ?>
    <!-- Rangeslider CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/ion-rangeslider/css/ion.rangeSlider.min.css'); ?>">
<?php } ?>

<?php if ($page === 'ui-lightbox.php') { ?>
    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/lightbox/glightbox.min.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-ionic.php' || $page === 'plugin.php') { ?>
    <!-- Ionic CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/ionic/ionicons.css'); ?>">
<?php } ?>

<?php if ($page === 'chart-morris.php') { ?>
    <!-- Morris CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/morris/morris.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-material.php') { ?>
    <!-- Material CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/material/materialdesignicons.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-pe7.php') { ?>
    <!-- Pe7 CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/pe7/pe-icon-7.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-simpleline.php' || $page === 'plugin.php') { ?>
    <!-- Material CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/simpleline/simple-line-icons.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-themify.php') { ?>
    <!-- Pe7 CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/themify/themify.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-typicon.php') { ?>
    <!-- Typicons CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/typicons/typicons.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-flag.php') { ?>
    <!-- Flags CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/flags/flags.css'); ?>">
<?php } ?>

<?php if ($page === 'icon-weather.php') { ?>
    <!-- Weather CSS -->
    <link rel="stylesheet" href="<?= $asset('plugins/icons/weather/weathericons.css'); ?>">
<?php } ?>

<?php if ($page === 'form-pickers.php') { ?>
    <link rel="stylesheet" href="<?= $asset('plugins/flatpickr/flatpickr.css'); ?>" />
    <link rel="stylesheet" href="<?= $asset('plugins/bootstrap-datepicker/bootstrap-datepicker.css'); ?>" />
    <link rel="stylesheet" href="<?= $asset('plugins/jquery-timepicker/jquery-timepicker.css'); ?>" />
    <link rel="stylesheet" href="<?= $asset('plugins/pickr/pickr-themes.css'); ?>" />
<?php } ?>

<!-- Main CSS -->
<link rel="stylesheet" href="<?= $asset('css/style.css'); ?>">

<!-- ANTARA Brand Theme Overrides — match frontend palette -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
<style>
    /* ── Hide floating customizer gear ── */
    .customizer-links,
    .customizer-links .sticky-sidebar {
        display: none !important;
    }

    /* ── Root color overrides to match frontend ── */
    :root {
        --primary: #D70006;
        --primary-hover: #B50005;
        --primary-light: rgba(215, 0, 6, 0.1);
        --secondary: #24201F;
        --dark: #24201F;
        --body-font: 'Outfit', sans-serif;
        --title-font: 'Roboto', sans-serif;
    }

    /* ── Typography ── */
    body {
        font-family: 'Outfit', sans-serif !important;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .page-header h3,
    .page-header h4 {
        font-family: 'Roboto', sans-serif !important;
    }

    /* ── Sidebar: dark background matching frontend ── */
    .sidebar {
        background-color: #1a1816 !important;
    }

    .sidebar .sidebar-menu>ul>li .menu-title span {
        color: rgba(255, 255, 255, 0.45) !important;
    }

    .sidebar .sidebar-menu>ul>li a {
        color: rgba(255, 255, 255, 0.78) !important;
    }

    .sidebar .sidebar-menu>ul>li a:hover,
    .sidebar .sidebar-menu>ul>li a.active {
        color: #fff !important;
        background: rgba(215, 0, 6, 0.15) !important;
    }

    .sidebar .sidebar-menu>ul>li a.active i,
    .sidebar .sidebar-menu>ul>li a:hover i {
        color: #D70006 !important;
    }

    .sidebar .sidebar-menu>ul>li a i {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    /* ── Sidebar logo area ── */
    .sidebar-logo,
    .logo-small {
        background-color: #24201F !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    /* ── Topbar / Header ── */
    .header,
    .header .main-header {
        background: #2F3E56 !important;
        border-bottom: 2px solid #233046 !important;
    }

    .header .header-user,
    .header .header-user .user-menu.nav-list,
    .header .header-user .user-menu.nav-list .d-flex {
        background: transparent !important;
    }

    /* Topbar content color: force white */
    .header #header-search,
    .header #header-search .header-welcome-message,
    .header #header-search .welcome-english,
    .header #header-search .input-icon-addon,
    .header #header-search .input-group-text,
    .header #header-search .btn-menubar,
    .header #header-search .btn-menubar i,
    .header #header-search .ti,
    .header .header-user .user-menu .btn-menubar,
    .header .header-user .user-menu .btn-menubar i,
    .header .header-user .user-menu .notification_item>a,
    .header .header-user .user-menu .notification_item>a i {
        color: #ffffff !important;
    }

    .header #header-search .form-control {
        color: #ffffff !important;
        background: transparent !important;
        border-color: rgba(255, 255, 255, 0.32) !important;
    }

    .header #header-search .form-control::placeholder {
        color: rgba(255, 255, 255, 0.86) !important;
    }

    .header #header-search .input-group-text,
    .header #header-search .input-icon-addon {
        border-color: rgba(255, 255, 255, 0.32) !important;
        background: transparent !important;
    }

    /* Keep dropdown panel content readable (do not force white in popup) */
    .header .header-user .user-menu .dropdown-menu,
    .header .header-user .user-menu .dropdown-menu .dropdown-item,
    .header .header-user .user-menu .dropdown-menu p,
    .header .header-user .user-menu .dropdown-menu h4,
    .header .header-user .user-menu .dropdown-menu h5,
    .header .header-user .user-menu .dropdown-menu span,
    .header .header-user .user-menu .dropdown-menu i {
        color: inherit !important;
    }

    .header .logo img {
        max-height: 40px !important;
        width: auto !important;
    }

    .header .dark-logo img {
        max-height: 46px !important;
        width: auto !important;
    }

    /* Desktop: use dark logo as primary logo */
    .header .header-left .logo {
        display: none !important;
    }

    .header .header-left .dark-logo {
        display: inline-block !important;
        line-height: 50px !important;
    }

    .header-welcome-message {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 0 6px;
        border: 0;
        border-radius: 0;
        background: transparent;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }

    .header-welcome-message .welcome-english {
        font-style: italic;
    }

    @media (max-width: 1199.98px) {
        .header-welcome-message {
            max-width: 420px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    @media (max-width: 991.98px) {
        .header .logo img,
        .header .dark-logo img {
            max-height: 32px !important;
        }

        /* Mobile: use logo-antara.png */
        .header .header-left .logo {
            display: inline-block !important;
        }

        .header .header-left .dark-logo {
            display: none !important;
        }

        .header-welcome-message {
            display: none;
        }
    }

    /* Sidebar logo: force single logo path (normal vs mini) */
    @media (min-width: 992px) {
        .sidebar .sidebar-logo .logo {
            display: block !important;
        }

        .sidebar .sidebar-logo .dark-logo {
            display: none !important;
        }

        .mini-sidebar .sidebar .sidebar-logo .logo {
            display: none !important;
        }

        .mini-sidebar .sidebar .sidebar-logo .logo-small {
            display: block !important;
        }

        .mini-sidebar.expand-menu .sidebar .sidebar-logo .logo {
            display: block !important;
        }

        .mini-sidebar.expand-menu .sidebar .sidebar-logo .logo-small {
            display: none !important;
        }
    }

    /* ── Buttons — primary use ANTARA red ── */
    .btn-primary,
    .theme-btn.btn-style-one {
        background-color: #D70006 !important;
        border-color: #D70006 !important;
        color: #fff !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .theme-btn.btn-style-one:hover {
        background-color: #B50005 !important;
        border-color: #B50005 !important;
    }

    /* ── Links ── */
    a.text-primary,
    .breadcrumb-item a {
        color: #D70006 !important;
    }

    /* ── Active / accent indicators ── */
    .badge.bg-primary,
    .nav-pills .nav-link.active {
        background-color: #D70006 !important;
    }

    .nav-pills .nav-link:not(.active):hover {
        color: #D70006 !important;
    }

    /* ── Sidebar scrollbar ── */
    .sidebar ::-webkit-scrollbar-thumb {
        background: rgba(215, 0, 6, 0.3) !important;
        border-radius: 4px;
    }

    /* ── Card headers with brand accent ── */
    .card .card-header {
        border-bottom-color: rgba(215, 0, 6, 0.12) !important;
    }

    /* ── Page title breadcrumb active ── */
    .breadcrumb-item.active {
        color: #D70006 !important;
    }

    /* ── DataTable / Pagination ── */
    .page-item.active .page-link {
        background-color: #D70006 !important;
        border-color: #D70006 !important;
    }

    /* ── Toggle / Switch active state ── */
    .form-check-input:checked {
        background-color: #D70006 !important;
        border-color: #D70006 !important;
    }

    /* ── Progress bars ── */
    .progress-bar {
        background-color: #D70006 !important;
    }

    /* ── Footer ── */
    .footer {
        border-top: 1px solid rgba(215, 0, 6, 0.12) !important;
    }
</style>
