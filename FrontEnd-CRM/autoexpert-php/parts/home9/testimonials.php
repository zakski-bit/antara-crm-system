<?php
    $klien_directory = __DIR__ . '/../../images/client/client';
    $klien_pattern = $klien_directory . '/*.{png,jpg,jpeg,svg,webp}';
    $klien_files = glob($klien_pattern, GLOB_BRACE) ?: [];
    sort($klien_files, SORT_STRING | SORT_FLAG_CASE);

    $klien_partners = array_map(static function ($file) {
        $basename = pathinfo($file, PATHINFO_FILENAME);
        $name = preg_replace('/^(logo[-_]?)/i', '', $basename);
        $name = preg_replace('/[-_]+/', ' ', $name);
        $name = trim($name);
        $name = $name !== '' ? ucwords($name) : 'Klien';

        return [
            'name' => $name,
            'logo' => 'images/client/client/' . basename($file),
        ];
    }, $klien_files);

    if (empty($klien_partners)) {
        $klien_partners = [
            ['name' => 'Asianet', 'logo' => 'assets/img/mitra/asianet.png'],
            ['name' => 'Bernama', 'logo' => 'assets/img/mitra/bernama.png'],
            ['name' => 'Bloomberg', 'logo' => 'assets/img/mitra/bloomberg.png'],
            ['name' => 'EFE', 'logo' => 'assets/img/mitra/efe.png'],
            ['name' => 'TTXN', 'logo' => 'assets/img/mitra/ttxn.png'],
            ['name' => 'Xinhua', 'logo' => 'assets/img/mitra/xinhua.png'],
            ['name' => 'HCM Ads Media', 'logo' => 'assets/img/mitra/hcmads.png'],
            ['name' => 'Finsco', 'logo' => 'assets/img/mitra/finsco.png'],
        ];
    }

    $half = (int) ceil(count($klien_partners) / 2);
    $klien_row_1 = array_slice($klien_partners, 0, $half);
    $klien_row_2 = array_slice($klien_partners, $half);
?>

<section id="testimonial" class="mitra-section">
    <div class="bg pattern-4 bounce-y"></div>
    <div class="auto-container">
        <div class="mitra-section__header">
            <h2 class="mitra-section__title">KLIEN</h2>
        </div>

        <?php if (!empty($klien_row_1)) : ?>
            <div class="mitra-section__row">
                <div class="swiper mitra-swiper mitra-swiper--row1">
                    <div class="swiper-wrapper">
                        <?php foreach ($klien_row_1 as $partner) :
                            $name = htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8');
                            $logo = htmlspecialchars($partner['logo'], ENT_QUOTES, 'UTF-8');
                        ?>
                            <div class="swiper-slide">
                                <article class="mitra-card">
                                    <div class="mitra-card__media">
                                        <img loading="lazy" src="<?php echo $logo; ?>" alt="<?php echo $name; ?> logo">
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($klien_row_2)) : ?>
            <div class="mitra-section__row mitra-section__row--inverted">
                <div class="swiper mitra-swiper mitra-swiper--row2">
                    <div class="swiper-wrapper">
                        <?php foreach ($klien_row_2 as $partner) :
                            $name = htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8');
                            $logo = htmlspecialchars($partner['logo'], ENT_QUOTES, 'UTF-8');
                        ?>
                            <div class="swiper-slide">
                                <article class="mitra-card">
                                    <div class="mitra-card__media">
                                        <img loading="lazy" src="<?php echo $logo; ?>" alt="<?php echo $name; ?> logo">
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .mitra-section {
        position: relative;
        padding: 110px 0;
        overflow: hidden;
    }

    #testimonial.mitra-section {
        padding-top: 32px;
        padding-bottom: 72px;
        background: linear-gradient(180deg, #f6f8fd 0%, #ffffff 100%);
    }

    .mitra-section__header {
        position: relative;
        text-align: center;
        margin-bottom: 54px;
        z-index: 2;
    }

    .mitra-section__title {
        margin: 0;
        font-size: clamp(28px, 5vw, 40px);
        font-weight: 700;
        letter-spacing: 0.08em;
        color: var(--theme-color-dark, #0f224b);
        text-transform: uppercase;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .mitra-section__title::after {
        content: "";
        width: 90px;
        height: 3px;
        border-radius: 999px;
        background: var(--theme-color1, #e1001a);
    }

    .mitra-section__row {
        position: relative;
        z-index: 2;
        margin-bottom: 32px;
    }

    .mitra-section__row:last-of-type {
        margin-bottom: 0;
    }

    .mitra-swiper {
        width: 100%;
        padding: 8px 4px;
    }

    .mitra-swiper .swiper-wrapper {
        align-items: stretch;
    }

    .mitra-swiper .swiper-slide {
        height: auto;
        display: flex;
        align-items: stretch;
    }

    .mitra-card {
        position: relative;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 18px 36px rgba(15, 34, 75, 0.1);
        border: 1px solid rgba(214, 224, 244, 0.6);
        padding: clamp(18px, 2.6vw, 32px);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: clamp(110px, 9vw, 150px);
        transition: transform 0.35s ease, box-shadow 0.35s ease;
    }

    .mitra-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 42px rgba(15, 34, 75, 0.18);
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
        width: clamp(320px, 48vw, 840px);
        max-width: 100%;
        height: auto;
        max-height: clamp(90px, 12vw, 220px);
        aspect-ratio: 4 / 1;
        object-fit: contain;
        filter: saturate(1.1);
    }

    @media (max-width: 991px) {
        .mitra-section {
            padding: 90px 0;
        }

        .mitra-card__media img {
            width: clamp(260px, 60vw, 680px);
            max-height: clamp(80px, 14vw, 180px);
        }
    }

    @media (max-width: 575px) {
        .mitra-section {
            padding: 72px 0;
        }

        .mitra-section__header {
            margin-bottom: 40px;
        }

        .mitra-card {
            min-height: 120px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper === 'undefined') {
            return;
        }

        const baseOptions = {
            slidesPerView: 2,
            spaceBetween: 28,
            loop: true,
            speed: 6500,
            allowTouchMove: true,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: false
            },
            breakpoints: {
                768: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                }
            }
        };

        const topSelector = '#testimonial .mitra-swiper--row1';
        const bottomSelector = '#testimonial .mitra-swiper--row2';
        const topExists = document.querySelector(topSelector);

        if (topExists) {
            const topSlider = new Swiper(topSelector, baseOptions);

            const bottomExists = document.querySelector(bottomSelector);
            if (bottomExists) {
                const bottomOptions = Object.assign({}, baseOptions, {
                    speed: 7200,
                    autoplay: Object.assign({}, baseOptions.autoplay, {
                        reverseDirection: true,
                        delay: 0
                    })
                });

                const bottomSlider = new Swiper(bottomSelector, bottomOptions);
            }
        }
    });
</script>
