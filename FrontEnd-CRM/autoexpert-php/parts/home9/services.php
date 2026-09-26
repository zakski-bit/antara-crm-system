<section id="services" class="services-section-nine">
	<div class="bg bg-video">
		<video
			src="videos/hitam_1min.mp4"
			autoplay
			muted
			loop
			playsinline
			preload="auto"
		></video>
	</div>
	<div class="auto-container">
		<div class="sec-title-outer">
			<div class="sec-title light text-center">
				<span class="sub-title titleantara titleantara--inline"><img src="images/icons/titleantara.png" alt="Logo ANTARA" class="titleantara__logo">Layanan ANTARA</span>
				<h2 class="words-slide-up text-split">Produk</h2>
				<div class="sec-title-divider"></div>
			</div>
		</div>
		<div class="product-grid">
					<div class="product-card">
						<div class="product-card__inner">
							<img src="images/resource/products/logo-antara-news.png" alt="ANTARANEWS.com">
						</div>
					</div>
					<div class="product-card">
						<div class="product-card__inner">
							<img src="images/resource/products/expert-group.png" alt="ANTARAFOTO">
						</div>
					</div>
					<div class="product-card">
						<div class="product-card__inner">
							<img src="images/resource/products/logo-antara-tv.png" alt="ANTARATV">
						</div>
					</div>
					<div class="product-card">
						<div class="product-card__inner">
							<img src="images/resource/products/logo-branda.png" alt="BRANDA">
						</div>
					</div>
		</div>
		<div class="product-cta text-center">
			<a href="shop-products.php#product-showcase" class="theme-btn btn-style-one hvr-light">
				<span class="btn-title">Lihat Selengkapnya</span>
			</a>
		</div>
	</div>
</section>

<style>
    #services.services-section-nine {
        position: relative;
        padding: 76px 0 56px;
        border-bottom: 18px solid #ffffff;
    }

    #services.services-section-nine::before,
    #services.services-section-nine::after {
        content: "";
        position: absolute;
        left: 0;
        width: 100%;
        height: 36px;
        pointer-events: none;
        z-index: 1;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 100%);
    }

    #services.services-section-nine::before {
        display: none;
    }

    #services.services-section-nine::after {
        bottom: 0;
        transform: scaleY(-1);
    }

    #services.services-section-nine .auto-container {
        position: relative;
        z-index: 2;
    }

    #services.services-section-nine .bg.bg-video::before,
    #services.services-section-nine .bg-image2::before {
        background: linear-gradient(180deg, rgba(11, 23, 39, 0.45) 0%, rgba(11, 23, 39, 0.22) 24%, rgba(255, 255, 255, 0.88) 100%);
        z-index: 1;
    }

    #services.services-section-nine .bg.bg-video::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0) 58%, rgba(255, 255, 255, 0.45) 100%);
        pointer-events: none;
        z-index: 2;
    }

    #services.services-section-nine .bg.bg-video video {
        position: relative;
        z-index: 0;
        filter: brightness(1.05) saturate(1.02);
    }

    @media (max-width: 991px) {
        #services.services-section-nine {
            padding: 64px 0 44px;
            border-width: 14px;
        }

        #services.services-section-nine::before,
        #services.services-section-nine::after {
            height: 28px;
        }
    }
</style>
