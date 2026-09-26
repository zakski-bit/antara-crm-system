<?php 
    $head_title = "ANTARA - Berita Terkini dan Terpercaya | Products";
    $page_title = "Products";
    $page_sub_title = "Shop";
    $header_outer_box_layout = 'login-call';
    $body_class = 'shop-products-page';

    $main_products = [
        [
            'name'  => 'ANTARANEWS.com',
            'url'   => 'https://www.antaranews.com',
            'image' => 'images/resource/products/logo-antara-news.png',
        ],
        [
            'name'  => 'ANTARAFOTO',
            'url'   => 'https://www.antarafoto.com',
            'image' => 'images/resource/products/expert-group.png',
        ],
        [
            'name'  => 'ANTARATV',
            'url'   => 'https://www.antaranews.com/tv',
            'image' => 'images/resource/products/logo-antara-tv.png',
        ],
        [
            'name'  => 'BRANDA',
            'url'   => 'https://branda.antara.co.id',
            'image' => 'images/resource/products/logo-branda.png',
        ],
        [
            'name'  => 'IMCS Beyond News',
            'url'   => 'https://imcs.antara.co.id',
            'image' => 'images/resource/products/logo-imcs.png',
        ],
        [
            'name'  => 'Sekolah Jurnalistik ANTARA',
            'url'   => 'https://sja.antara.co.id',
            'image' => 'images/resource/products/logo-lpa.png',
        ],
        [
            'name'  => 'Layanan Analisa & Informasi',
            'url'   => 'https://www.antara.co.id',
            'image' => 'images/resource/products/logo-layanan-analisa-dan-informa.png',
        ],
        [
            'name'  => 'Hardware Rental',
            'url'   => '#',
            'image' => 'images/resource/products/logo-sewa-perangkat-keras.png',
        ],
        [
            'name'  => 'MICE',
            'url'   => '#',
            'image' => 'images/resource/products/logo-mice.png',
        ],
        [
            'name'  => 'Heritage Center',
            'url'   => '#',
            'image' => 'images/resource/products/logo-ahc.png',
        ],
    ];

    $product_sections = [
        [
            'title' => 'Produk Kemitraan',
            'items' => [
                [
                    'name'  => 'Bloomberg',
                    'url'   => 'https://www.bloomberg.com',
                    'image' => 'images/resource/products/logo-bloomberg.png',
                ],
                [
                    'name'  => 'Reuters',
                    'url'   => 'https://www.reuters.com',
                    'image' => 'images/resource/products/logo-reuters.png',
                ],
            ],
        ],
        [
            'title' => 'Anak Perusahaan',
            'items' => [
                [
                    'name'  => '1A ETP',
                    'url'   => '#',
                    'image' => 'images/resource/products/logo-etp.png',
                ],
            ],
        ],
        [
            'title' => 'Produk Afiliasi',
            'items' => [
                [
                    'name'  => 'HCM Ads Media',
                    'url'   => '#',
                    'image' => 'images/resource/products/logo-hcm-ads-media.png',
                ],
            ],
        ],
    ];

    if (!function_exists('render_shop_logo_card')) {
        /**
         * Render a single product logo card.
         */
        function render_shop_logo_card(array $item): void
        {
            $name = trim((string)($item['name'] ?? ''));

            if ($name === '') {
                return;
            }

            $url = trim((string)($item['url'] ?? ''));
            $image = trim((string)($item['image'] ?? ''));

            $is_placeholder = $url === '' || $url === '#';

            if ($is_placeholder) {
                $url = '#';
            }

            $is_external = preg_match('#^https?://#i', $url) === 1;

            $safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $safe_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safe_image = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');

            $class_names = 'shop-logo-card' . ($is_placeholder ? ' is-placeholder-link' : '');

            $attributes = sprintf(
                'class="%s" href="%s" aria-label="%s"',
                $class_names,
                $safe_url,
                $safe_name
            );

            if ($is_external) {
                $attributes .= ' target="_blank" rel="noopener"';
            }

            if ($is_placeholder) {
                $attributes .= ' aria-disabled="true"';
            }

            echo '<a ' . $attributes . '>';
            echo '<span class="shop-logo-card__media">';

            if ($safe_image !== '') {
                echo '<img src="' . $safe_image . '" alt="' . $safe_name . '" class="shop-logo-card__image">';
            } else {
                echo '<span class="shop-logo-card__text">' . $safe_name . '</span>';
            }

            echo '</span>';

            echo '<span class="shop-logo-card__hover" aria-hidden="true">';
            echo '<span class="shop-logo-card__hover-label">PRODUK</span>';
            echo '<span class="shop-logo-card__hover-title">' . $safe_name . '</span>';

            if (!$is_placeholder) {
                echo '<span class="shop-logo-card__hover-icon"><i class="far fa-arrow-up" aria-hidden="true"></i></span>';
            }

            echo '</span>';

            echo '</a>';
        }
    }
?>

<?php require_once('parts/header/header.php'); ?>

<style>
    .shop-products-page .shop-logo-video-shell {
        position: relative;
        min-height: 100vh;
        overflow: hidden;
        background-color: #030b1e;
    }

    .shop-products-page .shop-logo-video-shell__video {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 0;
    }

    .shop-products-page .shop-logo-video-shell__video video {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        transform: translate(-50%, -50%);
        object-fit: cover;
        pointer-events: none;
    }

    .shop-products-page .shop-logo-video-shell__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(7, 22, 40, 0.75) 0%, rgba(7, 22, 40, 0.58) 40%, rgba(219, 230, 249, 0.65) 100%);
        z-index: 1;
        pointer-events: none;
    }

    .shop-products-page .shop-logo-page-shell {
        position: relative;
        z-index: 2;
        padding: 0 0 150px;
    }

    .shop-products-page .shop-logo-page-shell .auto-container {
        max-width: 1600px;
        width: min(98vw, 1600px);
        position: relative;
        z-index: 2;
    }

    .shop-products-page .shop-logo-content {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(245, 248, 255, 0.96) 100%);
        border-radius: 32px;
        padding: 48px 56px;
        box-shadow: 0 18px 50px rgba(15, 34, 75, 0.18);
        border: 1px solid rgba(214, 224, 244, 0.7);
        position: relative;
        margin: 0 auto;
        width: 100%;
    }

    .shop-products-page .shop-products-breadcrumb {
        list-style: none;
        margin: clamp(120px, 18vh, 180px) auto 48px;
        padding: 12px 22px;
        display: inline-flex;
        align-items: center;
        gap: 16px;
        border-radius: 36px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(214, 224, 244, 0.5);
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #1a2b6d;
    }

    .shop-products-page .shop-products-breadcrumb li {
        position: relative;
        font-weight: 600;
        color: inherit;
    }

    .shop-products-page .shop-products-breadcrumb li + li::before {
        content: ">";
        margin-right: 16px;
        color: rgba(26, 43, 109, 0.45);
        font-weight: 400;
    }

    .shop-products-page .shop-products-breadcrumb li a {
        color: rgba(26, 43, 109, 0.75);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .shop-products-page .shop-products-breadcrumb li a:hover {
        color: #1a2b6d;
    }

    @media (max-width: 575px) {
        .shop-products-page .shop-products-breadcrumb {
            padding: 10px 18px;
            gap: 12px;
            font-size: 10px;
        }
    }

    .shop-products-page .shop-section-title {
        margin: 0;
        font-size: 22px;
        line-height: 1.3;
        font-weight: 600;
        color: #0f224b;
    }

    .shop-products-page .shop-logo-content {
        margin-top: 48px;
        display: flex;
        flex-direction: column;
        gap: 56px;
    }

    .shop-products-page .shop-logo-section {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .shop-products-page .shop-logo-grid {
        display: grid;
        gap: 24px;
    }

    .shop-products-page .shop-logo-grid--primary {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }

    .shop-products-page .shop-logo-section--others {
        gap: 32px;
    }

    .shop-products-page .shop-categories-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 32px;
    }

    .shop-products-page .shop-category-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .shop-products-page .shop-category-header {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    .shop-products-page .shop-category-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: #0f224b;
    }

    .shop-products-page .shop-category-underline {
        width: 74px;
        height: 3px;
        border-radius: 2px;
        background: #183d96;
    }

    .shop-products-page .shop-category-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: 1fr;
        align-content: flex-start;
    }

    .shop-products-page .shop-logo-section--others .shop-category-column:first-child {
        grid-column: span 2;
    }

    .shop-products-page .shop-logo-section--others .shop-category-column:first-child .shop-category-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    @media (max-width: 991px) {
        .shop-products-page .shop-logo-page-shell {
            padding: 0 0 140px;
        }

        .shop-products-page .shop-logo-content {
            padding: 48px;
            border-radius: 28px;
        }

        .shop-products-page .shop-logo-content {
            margin-top: 40px;
            gap: 48px;
        }

        .shop-products-page .shop-logo-section--others .shop-category-column:first-child {
            grid-column: auto;
        }

        .shop-products-page .shop-logo-grid--primary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .shop-products-page .shop-categories-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .shop-products-page .shop-products-breadcrumb {
            justify-content: center;
            margin-top: clamp(110px, 20vh, 160px);
        }
    }

    @media (max-width: 575px) {
        .shop-products-page .shop-logo-page-shell {
            padding: 0 0 120px;
        }

        .shop-products-page .shop-logo-content {
            padding: 32px 28px;
            border-radius: 24px;
        }

        .shop-products-page .shop-logo-content {
            margin-top: 32px;
            gap: 40px;
        }

        .shop-products-page .shop-logo-grid--primary,
        .shop-products-page .shop-categories-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .shop-products-page .shop-products-breadcrumb {
            margin-top: 110px;
        }

        .shop-products-page .shop-logo-section--others .shop-category-column:first-child {
            grid-column: auto;
        }

        .shop-products-page .shop-logo-section--others .shop-category-column:first-child .shop-category-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .shop-logo-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background-color: #ffffff;
        border: 1px solid #e5e9f2;
        border-radius: 20px;
        min-height: 0;
        height: clamp(140px, 14vw, 200px);
        padding: clamp(12px, 2vw, 20px) clamp(28px, 5vw, 44px);
        text-decoration: none;
        color: #0f224b;
        text-align: center;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 12px 28px rgba(15, 34, 75, 0.12);
        overflow: hidden;
        box-sizing: border-box;
        isolation: isolate;
    }

    .shop-logo-card:focus,
    .shop-logo-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 40px rgba(15, 34, 75, 0.24);
    }

    .shop-logo-card.is-placeholder-link {
        cursor: default;
    }

    .shop-logo-card.is-placeholder-link:focus,
    .shop-logo-card.is-placeholder-link:hover {
        transform: none;
        box-shadow: 0 12px 28px rgba(15, 34, 75, 0.12);
    }

    .shop-logo-card__media {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        transition: transform 0.35s ease, opacity 0.35s ease;
    }

    .shop-logo-card__image {
        display: block;
        width: auto;
        max-width: 100%;
        max-height: 100%;
        height: auto;
        object-fit: contain;
    }

    .shop-logo-card__text {
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #1a2b6d;
        text-align: center;
    }

    .shop-logo-card.is-placeholder-link .shop-logo-card__text {
        color: #97a2c4;
    }

    .shop-logo-card__hover {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        padding: 32px 28px;
        background: linear-gradient(180deg, rgba(12, 24, 48, 0.18) 0%, rgba(12, 24, 48, 0.32) 100%);
        color: #ffffff;
        text-align: center;
        transform: translateY(100%);
        transition: transform 0.35s ease;
        z-index: 2;
        pointer-events: none;
    }

    .shop-logo-card__hover-label,
    .shop-logo-card__hover-title,
    .shop-logo-card__hover-icon {
        opacity: 0;
        transform: translateY(18px);
        transition: transform 0.35s ease, opacity 0.35s ease;
    }

    .shop-logo-card__hover-label {
        font-size: 12px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.78);
    }

    .shop-logo-card__hover-title {
        font-size: clamp(18px, 2vw, 22px);
        font-weight: 600;
        line-height: 1.3;
        color: #ffffff;
        max-width: 90%;
    }

    .shop-logo-card__hover-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ffffff;
    }

    .shop-logo-card:hover .shop-logo-card__hover,
    .shop-logo-card:focus-visible .shop-logo-card__hover {
        transform: translateY(0);
    }

    .shop-logo-card:hover .shop-logo-card__media,
    .shop-logo-card:focus-visible .shop-logo-card__media {
        transform: scale(1.05);
        opacity: 0.18;
    }

    .shop-logo-card:hover .shop-logo-card__hover-label,
    .shop-logo-card:focus-visible .shop-logo-card__hover-label {
        opacity: 1;
        transform: translateY(0);
        transition-delay: 80ms;
    }

    .shop-logo-card:hover .shop-logo-card__hover-title,
    .shop-logo-card:focus-visible .shop-logo-card__hover-title {
        opacity: 1;
        transform: translateY(0);
        transition-delay: 160ms;
    }

    .shop-logo-card:hover .shop-logo-card__hover-icon,
    .shop-logo-card:focus-visible .shop-logo-card__hover-icon {
        opacity: 1;
        transform: translateY(0);
        transition-delay: 240ms;
    }

    .shop-logo-card:focus-visible {
        outline: 3px solid rgba(22, 104, 248, 0.45);
        outline-offset: 4px;
    }

    .shop-logo-card.is-placeholder-link .shop-logo-card__hover-icon {
        display: none;
    }
</style>

<div class="shop-logo-video-shell">
    <div class="shop-logo-video-shell__video">
        <video
            aria-hidden="true"
            tabindex="-1"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
        >
            <source src="videos/hitam_1min.mp4" type="video/mp4">
        </video>
    </div>
    <div class="shop-logo-video-shell__overlay"></div>

    <div class="shop-logo-page-shell">
        <div class="auto-container">
            <ul class="shop-products-breadcrumb">
                <li><a href="#">Beranda</a></li>
                <li>Produk</li>
            </ul>

            <div class="shop-logo-content" id="product-showcase">
                <section class="shop-logo-section shop-logo-section--primary">
                    <h2 class="shop-section-title">Produk</h2>
                    <div class="shop-logo-grid shop-logo-grid--primary">
                        <?php foreach ($main_products as $product_logo) : ?>
                            <?php render_shop_logo_card($product_logo); ?>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php if (!empty($product_sections)) : ?>
                    <section class="shop-logo-section shop-logo-section--others">
                        <div class="shop-categories-row">
                            <?php foreach ($product_sections as $section) : ?>
                                <?php
                                    $section_title = trim((string)($section['title'] ?? ''));
                                    $section_items = array_filter($section['items'] ?? [], 'is_array');
                                ?>
                                <?php if ($section_title !== '' && !empty($section_items)) : ?>
                                    <article class="shop-category-column">
                                        <header class="shop-category-header">
                                            <h3 class="shop-category-title"><?php echo htmlspecialchars($section_title, ENT_QUOTES, 'UTF-8'); ?></h3>
                                            <span class="shop-category-underline"></span>
                                        </header>
                                        <div class="shop-category-grid">
                                            <?php foreach ($section_items as $product_logo) : ?>
                                                <?php render_shop_logo_card($product_logo); ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </article>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var video = document.querySelector('.shop-logo-video-shell__video video');
    if (video) {
        // Attempt to play the video
        var playPromise = video.play();

        if (playPromise !== undefined) {
            playPromise.then(function() {
                // Automatic playback started!
            }).catch(function(error) {
                // Auto-play was prevented
                // We can try to play again on user interaction
                console.warn("Video autoplay was prevented. Trying to play on first interaction.", error);
                var playOnClick = function() {
                    video.play();
                    document.body.removeEventListener('click', playOnClick);
                    document.body.removeEventListener('touchstart', playOnClick);
                };
                document.body.addEventListener('click', playOnClick);
                document.body.addEventListener('touchstart', playOnClick);
            });
        }
    }
});
</script>

<?php require_once('parts/footer/footer.php'); ?>
