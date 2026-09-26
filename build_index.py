import os
import glob
import re

BASE_DIR = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM"
FRONTEND_DIR = os.path.join(BASE_DIR, "FrontEnd-CRM", "autoexpert-php")
OUTPUT_DIR = os.path.join(BASE_DIR, "crm-portfolio-live")

def get_mitra_html():
    mitra_dir = os.path.join(OUTPUT_DIR, "images", "mitra", "mitra")
    files = glob.glob(os.path.join(mitra_dir, "*.png"))
    files.sort(key=lambda x: os.path.basename(x).lower())

    partners = []
    for f in files:
        b = os.path.splitext(os.path.basename(f))[0]
        b = re.sub(r'^(logo[-_]?)', '', b, flags=re.I)
        b = re.sub(r'[-_]+', ' ', b).strip()
        name = b.title() if b else "Mitra"
        partners.append({"name": name, "logo": f"images/mitra/mitra/{os.path.basename(f)}"})

    half = (len(partners) + 1) // 2
    row1 = partners[:half]
    row2 = partners[half:]

    def render_swiper(items, row_class):
        loop_items = items * 2 if len(items) < 12 else items
        slides = ""
        for it in loop_items:
            slides += f'''
            <div class="swiper-slide">
                <article class="mitra-card">
                    <div class="mitra-card__media">
                        <img loading="lazy" src="{it['logo']}" alt="{it['name']} logo">
                    </div>
                </article>
            </div>'''
        return f'''
        <div class="mitra-section__row">
            <div class="swiper mitra-swiper {row_class}">
                <div class="swiper-wrapper">
                    {slides}
                </div>
            </div>
        </div>'''

    return f'''
<section id="team" class="mitra-section">
    <div class="auto-container">
        <div class="mitra-section__header">
            <h2 class="mitra-section__title">MITRA INTERNASIONAL</h2>
        </div>
        {render_swiper(row1, "mitra-swiper--row1")}
        {render_swiper(row2, "mitra-swiper--row2")}
    </div>
</section>
'''

def get_klien_html():
    client_dir = os.path.join(OUTPUT_DIR, "images", "client", "client")
    files = glob.glob(os.path.join(client_dir, "*.png"))
    files.sort(key=lambda x: os.path.basename(x).lower())

    partners = []
    for f in files:
        b = os.path.splitext(os.path.basename(f))[0]
        b = re.sub(r'^(logo[-_]?)', '', b, flags=re.I)
        b = re.sub(r'[-_]+', ' ', b).strip()
        name = b.title() if b else "Klien"
        partners.append({"name": name, "logo": f"images/client/client/{os.path.basename(f)}"})

    half = (len(partners) + 1) // 2
    row1 = partners[:half]
    row2 = partners[half:]

    def render_swiper(items, row_class):
        loop_items = items * 2 if len(items) < 12 else items
        slides = ""
        for it in loop_items:
            slides += f'''
            <div class="swiper-slide">
                <article class="mitra-card">
                    <div class="mitra-card__media">
                        <img loading="lazy" src="{it['logo']}" alt="{it['name']} logo">
                    </div>
                </article>
            </div>'''
        return f'''
        <div class="mitra-section__row">
            <div class="swiper mitra-swiper {row_class}">
                <div class="swiper-wrapper">
                    {slides}
                </div>
            </div>
        </div>'''

    return f'''
<section id="testimonial" class="mitra-section klien-section-custom">
    <div class="auto-container">
        <div class="mitra-section__header">
            <h2 class="mitra-section__title">KLIEN KORPORASI</h2>
        </div>
        {render_swiper(row1, "klien-swiper--row1")}
        {render_swiper(row2, "klien-swiper--row2")}
    </div>
</section>
'''

def build_index():
    print("Regenerating index.html with Swiper animated logos...")

    contact_path = os.path.join(FRONTEND_DIR, "parts", "home9", "contact.php")
    contact_html = open(contact_path, encoding="utf-8").read() if os.path.exists(contact_path) else ""

    funfact_path = os.path.join(FRONTEND_DIR, "parts", "home9", "funfact.php")
    funfact_html = open(funfact_path, encoding="utf-8").read() if os.path.exists(funfact_path) else ""

    services_path = os.path.join(FRONTEND_DIR, "parts", "home9", "services.php")
    services_html = open(services_path, encoding="utf-8").read() if os.path.exists(services_path) else ""

    banner_html = '''
<section id="home" class="banner-section-three">
	<div class="banner-carousel owl-carousel owl-theme">
		<div class="slide-item" style="position:relative;">
			<div class="video-background">
				<video class="w-100 h-100" autoplay muted loop playsinline preload="auto">
					<source src="videos/hitam_1min.mp4" type="video/mp4">
				</video>
			</div>
			<div class="overlay-2"></div>
			<div class="auto-container">
				<div class="content-box">
					<span class="sub-title animate-2 titleantara">SELAMAT DATANG DI ANTARA</span>
					<h1 class="title animate-3">Berita terkini<br class="d-none d-md-block">dari ruang redaksi ANTARA</h1>
					<div class="text animate-4">Ikuti breaking news, laporan mendalam, dan analisis terpercaya yang dikurasi oleh jurnalis profesional ANTARA. Kami hadir 24/7 menghadirkan informasi akurat dan berimbang untuk Anda.</div>
					<div class="btn-box animate-4">
						<a href="#about" class="theme-btn btn-style-one hvr-light"><span class="btn-title">Baca Selengkapnya</span></a>
						<div class="hero-call-action">
							<span class="hero-call-action__icon"><i class="fa fa-phone"></i></span>
							<a href="tel:+62213842591" class="hero-call-action__btn">
								<span class="hero-call-action__label">Telepon</span>
								<span class="hero-call-action__number">021-3842591</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slide-item" style="position:relative;">
			<div class="video-background">
				<video class="w-100 h-100" autoplay muted loop playsinline preload="auto">
					<source src="videos/hitam_1min.mp4" type="video/mp4">
				</video>
			</div>
			<div class="overlay-2"></div>
			<div class="auto-container">
				<div class="content-box">
					<span class="sub-title animate-2 titleantara">JURNALIS ANTARA</span>
					<h1 class="title animate-3">Liputan eksklusif<br class="d-none d-md-block">dari seluruh Nusantara</h1>
					<div class="text animate-4">Rasakan pengalaman membaca yang menyeluruh melalui liputan foto, video, dan teks yang dikirim langsung dari 34 biro ANTARA di Indonesia.</div>
					<div class="btn-box animate-4">
						<a href="#contact" class="theme-btn btn-style-one hvr-light"><span class="btn-title">Hubungi Redaksi</span></a>
						<div class="hero-call-action">
							<span class="hero-call-action__icon"><i class="fa fa-phone"></i></span>
							<a href="tel:+62213842591" class="hero-call-action__btn">
								<span class="hero-call-action__label">Telepon</span>
								<span class="hero-call-action__number">021-3842591</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<style>
    #home.banner-section-three .overlay-2::before {
        background-image: linear-gradient(89deg, rgba(8, 17, 33, 0.82) 0%, rgba(8, 17, 33, 0.55) 45%, rgba(8, 17, 33, 0.28) 100%);
    }
    #home.banner-section-three .content-box {
        padding: 220px 0 210px;
    }
    @media (max-width: 1199px) {
        #home.banner-section-three .content-box {
            padding: 160px 0 150px;
        }
    }
</style>
'''

    news_html = '''
<section id="news" class="news-section-two">
    <div class="auto-container">
        <div class="sec-title-outer">
            <div class="sec-title light text-center">
                <span class="sub-title titleantara titleantara--inline"><img src="images/icons/titleantara.png" alt="Logo ANTARA" class="titleantara__logo">Warta Terkini</span>
                <h2 class="words-slide-up text-split">Artikel & Berita</h2>
                <div class="sec-title-divider"></div>
            </div>
        </div>
        <div class="row">
            <div class="news-block-two col-lg-4 col-md-6 wow fadeInUp">
                <div class="inner-box">
                    <div class="image-box">
                        <figure class="image"><a href="#"><img src="images/resource/news1-1.jpg" alt="News"></a></figure>
                        <span class="date"><strong>12</strong>Okt 2025</span>
                    </div>
                    <div class="content-box">
                        <ul class="post-info">
                            <li><i class="fa fa-user"></i> Redaksi Antara</li>
                            <li><i class="fa fa-comments"></i> Komentar</li>
                        </ul>
                        <h4 class="title"><a href="#">Transformasi Ekosistem Digital Berita Nasional Bersama LKBN ANTARA</a></h4>
                        <a href="#" class="read-more">Baca Selengkapnya <i class="fa fa-long-arrow-alt-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="news-block-two col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="inner-box">
                    <div class="image-box">
                        <figure class="image"><a href="#"><img src="images/resource/news1-2.jpg" alt="News"></a></figure>
                        <span class="date"><strong>11</strong>Okt 2025</span>
                    </div>
                    <div class="content-box">
                        <ul class="post-info">
                            <li><i class="fa fa-user"></i> Redaksi Antara</li>
                            <li><i class="fa fa-comments"></i> Komentar</li>
                        </ul>
                        <h4 class="title"><a href="#">Sorotan Foto & Multimedia: Ragam Inovasi Pemberitaan Nusantara</a></h4>
                        <a href="#" class="read-more">Baca Selengkapnya <i class="fa fa-long-arrow-alt-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="news-block-two col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                <div class="inner-box">
                    <div class="image-box">
                        <figure class="image"><a href="#"><img src="images/resource/news1-3.jpg" alt="News"></a></figure>
                        <span class="date"><strong>10</strong>Okt 2025</span>
                    </div>
                    <div class="content-box">
                        <ul class="post-info">
                            <li><i class="fa fa-user"></i> Redaksi Antara</li>
                            <li><i class="fa fa-comments"></i> Komentar</li>
                        </ul>
                        <h4 class="title"><a href="#">Kerja Sama Strategis Distribusi Konten Berita dengan Kantor Berita Internasional</a></h4>
                        <a href="#" class="read-more">Baca Selengkapnya <i class="fa fa-long-arrow-alt-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
'''

    mitra_html = get_mitra_html()
    klien_html = get_klien_html()

    template = """<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<title>ANTARA - Berita Terkini dan Terpercaya | Portfolio Demo</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/swiper.min.css" rel="stylesheet">
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="favicon.png">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link rel="stylesheet" href="demo-bar/demo-bar.css">
	<style>
		.preloader { display: none !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }

		/* MITRA & KLIEN Swiper Styling matching original PHP */
		.mitra-section {
			position: relative;
			padding: 72px 0 36px;
			overflow: hidden;
			background: linear-gradient(180deg, #ffffff 0%, #f6f8fd 100%);
		}
		.klien-section-custom {
			padding-top: 36px;
			padding-bottom: 72px;
			background: linear-gradient(180deg, #f6f8fd 0%, #ffffff 100%);
		}
		.mitra-section__header {
			position: relative;
			text-align: center;
			margin-bottom: 40px;
			z-index: 2;
		}
		.mitra-section__title {
			margin: 0;
			font-size: clamp(22px, 4vw, 32px);
			font-weight: 800;
			letter-spacing: 0.08em;
			color: #0f224b;
			text-transform: uppercase;
			display: inline-flex;
			flex-direction: column;
			align-items: center;
			gap: 10px;
		}
		.mitra-section__title::after {
			content: "";
			width: 60px;
			height: 3px;
			border-radius: 999px;
			background: #D70006;
		}
		.mitra-section__row {
			position: relative;
			z-index: 2;
			margin-bottom: 24px;
		}
		.mitra-section__row:last-of-type {
			margin-bottom: 0;
		}
		.mitra-swiper {
			width: 100%;
			padding: 6px 4px;
		}
		.mitra-swiper .swiper-wrapper {
			align-items: stretch;
			transition-timing-function: linear !important;
		}
		.mitra-swiper .swiper-slide {
			height: auto;
			display: flex;
			align-items: stretch;
		}
		.mitra-card {
			position: relative;
			background: #ffffff;
			border-radius: 20px;
			box-shadow: 0 10px 24px rgba(15, 34, 75, 0.07);
			border: 1px solid rgba(214, 224, 244, 0.65);
			padding: 14px 24px;
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 86px;
			height: 86px;
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}
		.mitra-card:hover {
			transform: translateY(-4px);
			box-shadow: 0 16px 32px rgba(15, 34, 75, 0.12);
		}
		.mitra-card__media {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			height: 100%;
		}
		.mitra-card__media img {
			display: block;
			max-width: 130px;
			max-height: 46px;
			width: auto;
			height: auto;
			object-fit: contain;
			filter: saturate(1.05);
		}
		@media (max-width: 767px) {
			.mitra-card {
				min-height: 72px;
				height: 72px;
				padding: 10px 16px;
			}}
			.mitra-card__media img {
				max-width: 105px;
				max-height: 38px;
			}
		}
	</style>
</head>
<body>
<div class="page-wrapper">

	<!-- Header -->
	<header class="main-header header-style-eight">
		<div class="header-lower">
			<div class="inner-container"> 
				<div class="main-box">
					<div class="mobile-nav-toggler"><span class="icon lnr-icon-bars"></span></div>
					<div class="logo-box">
						<div class="logo">
							<a href="index.html"><img src="images/antaradark1.png" alt="ANTARA" class="antara-logo"></a>
						</div>
					</div>
					<div class="nav-outer">
						<nav class="nav main-menu">
							<ul class="navigation clearfix">
								<li><a id="scroll-to-top-link" href="index.html">Beranda</a></li>
								<li><a href="#services">Produk</a></li>
								<li><a href="#news">Artikel</a></li>
								<li><a href="#team">Mitra</a></li>
								<li><a href="#testimonial">Klien</a></li>
								<li><a href="#about">Tentang Kami</a></li>
								<li><a href="#contact">Kontak</a></li>
							</ul>
						</nav>
					</div>
					<div class="outer-box"> 
						<span class="divider"></span>
						<a href="login.html" class="theme-btn btn-style-one header-login-btn">
							<span class="btn-title">Masuk / Login</span>
						</a>
						<a href="tel:+62213842591" class="info-btn-two">
							<i class="icon fa fa-phone"></i>
							<small>Telepon</small>
							<strong>021-3842591</strong>
						</a>
					</div>
				</div>
			</div>
		</div>
		<!-- Sticky Header -->
		<div class="sticky-header">
			<div class="auto-container">
				<div class="inner-container"> 
					<div class="mobile-nav-toggler"><span class="icon lnr-icon-bars"></span></div>
					<a href="index.html"><img src="images/antara.png" alt="ANTARA" class="antara-logo antara-logo--sticky"></a>
					<div class="nav-outer"> 
						<nav class="main-menu">
							<div class="navbar-collapse show collapse clearfix">
								<ul class="navigation clearfix"></ul>
							</div>
						</nav>
					</div>
					<div class="sticky-header__actions">
						<a href="login.html" class="theme-btn btn-style-one header-login-btn header-login-btn--sticky">
							<span class="btn-title">Masuk / Login</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>

	__BANNER__
	__SERVICES__
	__NEWS__
	__MITRA__
	__KLIEN__
	__FUNFACT__
	__CONTACT__

	<!-- Footer -->
	<footer class="main-footer footer-style-eight">
		<div class="widgets-section">
			<div class="auto-container">
				<div class="row">
					<div class="footer-column col-lg-4 col-sm-6">
						<div class="footer-widget about-widget">
							<h5 class="about-title">Tentang ANTARA</h5>
							<div class="text">Lembaga Kantor Berita Nasional ANTARA merupakan kantor berita resmi Indonesia yang menyediakan informasi terpercaya, akurat, dan berimbang ke seluruh penjuru dunia.</div>
						</div>
					</div>
					<div class="footer-column col-lg-4 col-sm-6">
						<div class="footer-widget contact-widget">
							<h5 class="widget-title">Kontak Redaksi</h5>
							<div class="widget-content">
								<div class="text">Jalan Antara Kav 53-61, Pasar Baru, Jakarta Pusat 10710</div>
								<ul class="contact-info">
									<li><i class="fa fa-envelope"></i><a href="mailto:customer.care@antara.id">customer.care@antara.id</a></li>
									<li><i class="fas fa-phone"></i><a href="tel:+62213842591">021-3842591 / 021-22395579</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="footer-column col-lg-4 col-sm-6">
						<div class="footer-widget">
							<h5 class="widget-title">Akses Portofolio</h5>
							<p>Demo interaktif sistem manajemen relasi pelanggan (CRM) dan portal kemitraan digital.</p>
							<a href="login.html" class="theme-btn btn-style-one"><span class="btn-title">Uji Coba Login</span></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="auto-container">
				<div class="inner-container">
					<div class="copyright-text">&copy; 2026 ANTARA | Proyek Magang CRM & Digital Media Portal</div>
				</div>
			</div>
		</div>
	</footer>

</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>

<script src="js/jquery.js"></script> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.fancybox.js"></script>
<script src="js/swiper.min.js"></script>
<script src="js/wow.js"></script>
<script src="js/appear.js"></script>
<script src="js/owl.js"></script>
<script src="js/script.js"></script>
<script src="demo-bar/demo-bar.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper === 'undefined') return;

    // Smooth continuous marquee slide left
    const baseOptionsLeft = {
        slidesPerView: 2,
        spaceBetween: 20,
        loop: true,
        speed: 4500,
        allowTouchMove: false,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: false
        },
        breakpoints: {
            576: { slidesPerView: 3, spaceBetween: 20 },
            768: { slidesPerView: 4, spaceBetween: 24 },
            1200: { slidesPerView: 5, spaceBetween: 28 }
        }
    };

    // Smooth continuous marquee slide right (reverse)
    const baseOptionsRight = Object.assign({}, baseOptionsLeft, {
        speed: 5200,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: false,
            reverseDirection: true
        }
    });

    // Initialize MITRA sliders
    if (document.querySelector('.mitra-swiper--row1')) {
        new Swiper('.mitra-swiper--row1', baseOptionsLeft);
    }
    if (document.querySelector('.mitra-swiper--row2')) {
        new Swiper('.mitra-swiper--row2', baseOptionsRight);
    }

    // Initialize KLIEN sliders
    if (document.querySelector('.klien-swiper--row1')) {
        new Swiper('.klien-swiper--row1', baseOptionsLeft);
    }
    if (document.querySelector('.klien-swiper--row2')) {
        new Swiper('.klien-swiper--row2', baseOptionsRight);
    }
});
</script>
</body>
</html>
"""

    full_html = template.replace("__BANNER__", banner_html)\
                        .replace("__SERVICES__", services_html)\
                        .replace("__NEWS__", news_html)\
                        .replace("__MITRA__", mitra_html)\
                        .replace("__KLIEN__", klien_html)\
                        .replace("__FUNFACT__", funfact_html)\
                        .replace("__CONTACT__", contact_html)

    target = os.path.join(OUTPUT_DIR, "index.html")
    with open(target, "w", encoding="utf-8") as f:
        f.write(full_html)
    print("Regenerated:", target)

build_index()
