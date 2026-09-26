<?php ob_start(); ?>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Berita & Liputan</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Redaksi
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Berita &amp; Liputan</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_blog" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Tambah Berita</a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <?php
                require_once __DIR__ . '/../partials/rss-news.php';

                $feedCatalog = getAntaraFeedsCatalog();
                $feedRegistry = getAntaraFeedRegistry();
                $requestedFeed = isset($_GET['feed']) ? trim((string) $_GET['feed']) : 'terkini';

                if ($requestedFeed === '' || !isset($feedRegistry[$requestedFeed])) {
                    $requestedFeed = 'terkini';
                }

                $selectedFeed = $feedRegistry[$requestedFeed];
                $newsFeed = getAntaraNewsFeed($requestedFeed, 18);
                $feedError = $newsFeed['error'];
                $feedItems = $newsFeed['items'];
                $feedMeta = $newsFeed['meta'];
                $placeholderImage = 'assets/img/blogs/blog-01.jpg';
                $totalFeeds = count($feedRegistry);
                $portalUrl = 'https://www.antaranews.com';
                $channelLink = $feedMeta['link'] ?? $portalUrl;
                $lastUpdatedFormatted = $feedMeta['updated_at_formatted'] ?? null;
                $lastUpdatedIso = $feedMeta['updated_at_iso'] ?? null;
                $selectedGroup = $selectedFeed['group'] ?? '';
                $selectedDescription = $selectedFeed['description'] ?? '';
                $feedSlides = array_chunk($feedItems, 3);
                $totalSlides = count($feedSlides);
                $primaryStory = $feedItems[0] ?? null;
                $heroImage = $primaryStory !== null && $primaryStory['image'] !== '' ? $primaryStory['image'] : $placeholderImage;
                $heroTitle = $primaryStory['title'] ?? ($feedMeta['title'] ?? $selectedFeed['title']);
                $heroLink = $primaryStory['link'] ?? ($feedMeta['link'] ?? $portalUrl);
                if ($primaryStory !== null && $primaryStory['excerpt'] !== '') {
                    $heroExcerpt = $primaryStory['excerpt'];
                } elseif ($selectedDescription !== '') {
                    $heroExcerpt = $selectedDescription;
                } elseif (!empty($feedMeta['description'])) {
                    $heroExcerpt = $feedMeta['description'];
                } else {
                    $heroExcerpt = 'Terkini dari kanal ANTARA News pilihan Anda.';
                }

                $currentUri = $_SERVER['REQUEST_URI'] ?? '';
                $currentPath = parse_url($currentUri, PHP_URL_PATH) ?: ($_SERVER['PHP_SELF'] ?? '/blogs.php');
                if ($currentPath === '') {
                    $currentPath = '/blogs.php';
                }

                $baseQueryParams = [];
                foreach ($_GET as $paramKey => $paramValue) {
                    if ($paramKey === 'feed') {
                        continue;
                    }
                    if (is_scalar($paramValue)) {
                        $baseQueryParams[$paramKey] = (string) $paramValue;
                    }
                }
            ?>

            <div class="row">
                <div class="col-xxl-3 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h6 class="mb-1">Kanal RSS ANTARA</h6>
                                    <p class="text-muted fs-12 mb-0">Sinkronisasi cepat dari portal utama ANTARA.</p>
                                </div>
                                <span class="badge bg-light text-body-secondary"><?php echo (int) $totalFeeds; ?> feed</span>
                            </div>
                            <div class="mb-3 d-xl-none">
                                <form method="get" action="<?php echo htmlspecialchars($currentPath); ?>" class="d-flex gap-2 align-items-center">
                                    <label for="feed-select" class="visually-hidden">Pilih kanal</label>
                                    <select id="feed-select" name="feed" class="form-select" onchange="this.form.submit()">
                                        <?php foreach ($feedCatalog as $category) : ?>
                                            <optgroup label="<?php echo htmlspecialchars($category['label']); ?>">
                                                <?php foreach ($category['feeds'] as $slug => $feed) : ?>
                                                    <option value="<?php echo htmlspecialchars($slug); ?>" <?php echo $slug === $requestedFeed ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($feed['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                    <noscript>
                                        <button type="submit" class="btn btn-primary">Terapkan</button>
                                    </noscript>
                                </form>
                            </div>
                            <div class="d-none d-xl-block feed-catalog-scroll" style="max-height: 520px; overflow-y: auto;">
                                <div class="accordion accordion-flush" id="feedCatalogAccordion" data-feed-catalog>
                                    <?php foreach ($feedCatalog as $categoryKey => $category) : ?>
                                        <?php
                                            $categorySlug = preg_replace('/[^a-z0-9]+/i', '-', (string) $categoryKey);
                                            $headingId = 'feed-heading-' . $categorySlug;
                                            $collapseId = 'feed-collapse-' . $categorySlug;
                                            $isCategoryActive = array_key_exists($requestedFeed, $category['feeds']);
                                        ?>
                                        <div class="accordion-item mb-2" data-feed-category data-category-label="<?php echo htmlspecialchars($category['label']); ?>">
                                            <h2 class="accordion-header" id="<?php echo htmlspecialchars($headingId); ?>">
                                                <button class="accordion-button <?php echo $isCategoryActive ? '' : 'collapsed'; ?> py-2" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo htmlspecialchars($collapseId); ?>" aria-expanded="<?php echo $isCategoryActive ? 'true' : 'false'; ?>" aria-controls="<?php echo htmlspecialchars($collapseId); ?>">
                                                    <span class="fw-semibold fs-12 text-uppercase text-muted"><?php echo htmlspecialchars($category['label']); ?></span>
                                                </button>
                                            </h2>
                                            <div id="<?php echo htmlspecialchars($collapseId); ?>" class="accordion-collapse collapse <?php echo $isCategoryActive ? 'show' : ''; ?>" data-bs-parent="#feedCatalogAccordion">
                                                <div class="accordion-body pt-0 pb-3">
                                                    <div class="list-group list-group-flush shadow-none">
                                                        <?php foreach ($category['feeds'] as $slug => $feed) : ?>
                                                            <?php
                                                                $isActive = $slug === $requestedFeed;
                                                                $feedTitle = $feed['title'];
                                                                $feedDescription = $feed['description'] ?? '';
                                                                $feedParams = $baseQueryParams;
                                                                $feedParams['feed'] = $slug;
                                                                $feedHref = $currentPath;
                                                                $feedQuery = http_build_query($feedParams);
                                                                if ($feedQuery !== '') {
                                                                    $feedHref .= '?' . $feedQuery;
                                                                }
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($feedHref); ?>" class="list-group-item list-group-item-action rounded <?php echo $isActive ? 'active border border-primary' : 'border-0'; ?> mb-2" data-feed-item data-feed-slug="<?php echo htmlspecialchars($slug); ?>" data-feed-title="<?php echo htmlspecialchars($feedTitle); ?>" data-feed-description="<?php echo htmlspecialchars($feedDescription); ?>">
                                                                <span class="d-block fw-medium"><?php echo htmlspecialchars($feedTitle); ?></span>
                                                                <?php if ($feedDescription !== '') : ?>
                                                                    <small class="feed-item-desc">
                                                                        <?php echo htmlspecialchars($feedDescription); ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-9 col-xl-8">
                    <style>
                        .feed-catalog-scroll .list-group-item {
                            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
                        }

                        .feed-catalog-scroll .list-group-item.active {
                            background-color: rgba(255, 115, 62, 0.08);
                            color: #d9480f;
                            border-color: rgba(255, 115, 62, 0.35);
                        }

                        .feed-catalog-scroll .list-group-item.active .fw-medium {
                            color: inherit;
                        }

                        .feed-catalog-scroll .list-group-item .feed-item-desc {
                            color: #6c757d;
                            transition: color 0.2s ease, opacity 0.2s ease;
                        }

                        .feed-catalog-scroll .list-group-item.active .feed-item-desc {
                            color: #d9480f;
                            opacity: 0.75;
                        }

                        .antara-feed-carousel {
                            position: relative;
                            overflow: hidden;
                            border-radius: 16px;
                            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
                            background-color: #fff;
                        }

                        .antara-feed-hero {
                            position: relative;
                            border-radius: 16px 16px 0 0;
                            overflow: hidden;
                            min-height: 180px;
                            background-size: cover;
                            background-position: center;
                            color: #fff;
                        }

                        .antara-feed-hero::before {
                            content: '';
                            position: absolute;
                            inset: 0;
                            background: linear-gradient(135deg, rgba(15, 23, 42, 0.65) 0%, rgba(15, 23, 42, 0.25) 70%);
                            backdrop-filter: blur(2px);
                        }

                        .antara-feed-hero-content {
                            position: relative;
                            z-index: 1;
                        }

                        .antara-feed-hero small {
                            opacity: 0.8;
                        }

                        .antara-feed-controls {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 0.75rem;
                            align-items: center;
                            justify-content: space-between;
                        }

                        .antara-feed-track {
                            position: relative;
                            margin-top: 0;
                            background-color: #fff;
                            overflow: hidden;
                            min-height: 280px;
                        }

                        .antara-feed-skeleton {
                            padding: 1.5rem 1.25rem;
                            display: grid;
                            gap: 1rem;
                        }

                        .antara-feed-skeleton.is-hidden {
                            display: none;
                        }

                        .antara-feed-skeleton-item {
                            display: grid;
                            grid-template-columns: 100px 1fr;
                            gap: 1rem;
                            align-items: center;
                        }

                        .antara-feed-skeleton-thumb,
                        .antara-feed-skeleton-line,
                        .antara-feed-skeleton-line-sm {
                            background: linear-gradient(90deg, rgba(226, 232, 240, 0.4), rgba(203, 213, 225, 0.8), rgba(226, 232, 240, 0.4));
                            background-size: 200% 100%;
                            animation: antaraShimmer 1.6s infinite;
                            border-radius: 0.75rem;
                        }

                        .antara-feed-skeleton-thumb {
                            height: 72px;
                        }

                        .antara-feed-skeleton-line {
                            height: 16px;
                            border-radius: 999px;
                        }

                        .antara-feed-skeleton-line-sm {
                            height: 12px;
                            border-radius: 999px;
                        }

                        @keyframes antaraShimmer {
                            0% {
                                background-position: 200% 0;
                            }
                            100% {
                                background-position: -200% 0;
                            }
                        }

                        .antara-feed-slide {
                            position: absolute;
                            inset: 0;
                            padding: 1.5rem 1.5rem 1.75rem;
                            opacity: 0;
                            transform: translateX(12%);
                            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.45s ease;
                            pointer-events: none;
                            will-change: transform, opacity;
                        }

                        .antara-feed-slide.is-active {
                            pointer-events: auto;
                        }

                        .antara-feed-carousel .carousel-nav {
                            width: 36px;
                            height: 36px;
                            border-radius: 50%;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
                            background-color: #fff;
                        }

                        .antara-feed-carousel .carousel-nav:hover:not(:disabled) {
                            transform: translateY(-1px);
                            background-color: #f1f5f9;
                        }

                        .antara-feed-carousel .carousel-nav:disabled {
                            opacity: 0.35;
                            pointer-events: none;
                        }

                        .antara-feed-dots {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.4rem;
                            padding: 0 1.5rem 1.5rem;
                        }

                        .antara-feed-dot {
                            width: 10px;
                            height: 10px;
                            border-radius: 50%;
                            border: 0;
                            background-color: #cbd5f5;
                            opacity: 0.6;
                            transition: transform 0.2s ease, opacity 0.2s ease, background-color 0.2s ease;
                        }

                        .antara-feed-dot.is-active {
                            background-color: #4f46e5;
                            opacity: 1;
                            transform: scale(1.25);
                        }

                        .antara-feed-dot:hover {
                            opacity: 0.9;
                        }

                        .antara-feed-card {
                            position: relative;
                            transition: transform 0.2s ease, box-shadow 0.2s ease;
                        }

                        .antara-feed-card:hover {
                            transform: translateY(-4px);
                            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.12);
                        }

                        .antara-feed-card-meta {
                            font-size: 0.75rem;
                            letter-spacing: 0.02em;
                            text-transform: uppercase;
                            color: #6366f1;
                            font-weight: 600;
                        }

                        @media (max-width: 991.98px) {
                            .antara-feed-slide {
                                padding-inline: 1rem;
                            }
                        }

                        @media (max-width: 767.98px) {
                            .antara-feed-slide .col-md-6 {
                                flex: 0 0 50%;
                                max-width: 50%;
                            }
                        }

                        @media (max-width: 575.98px) {
                            .antara-feed-slide .col-md-6 {
                                flex: 0 0 100%;
                                max-width: 100%;
                            }
                        }
                    </style>
                    <div class="antara-feed-carousel mb-4" data-feed-carousel data-total-slides="<?php echo (int) $totalSlides; ?>">
                        <div class="antara-feed-hero p-4 p-xl-5" data-feed-hero data-default-excerpt="<?php echo htmlspecialchars($heroExcerpt); ?>" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.35) 55%), url('<?php echo htmlspecialchars($heroImage); ?>');">
                            <div class="antara-feed-hero-content">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-light text-dark fw-medium px-2 py-1">Live Feed</span>
                                    <?php if ($selectedGroup !== '') : ?>
                                        <span class="badge bg-primary text-white fw-medium px-2 py-1"><?php echo htmlspecialchars($selectedGroup); ?></span>
                                    <?php endif; ?>
                                    <small><?php echo htmlspecialchars($lastUpdatedFormatted ?? 'Memuat pembaruan...'); ?> WIB</small>
                                </div>
                                <h3 class="fw-semibold mb-2">
                                    <a href="<?php echo htmlspecialchars($heroLink); ?>" class="text-white text-decoration-none" target="_blank" rel="noopener" data-feed-hero-link data-feed-hero-title>
                                        <?php echo htmlspecialchars($heroTitle); ?>
                                    </a>
                                </h3>
                                <p class="mb-4 text-white-50" data-feed-hero-text>
                                    <?php echo htmlspecialchars($heroExcerpt); ?>
                                </p>
                                <div class="antara-feed-controls">
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="<?php echo htmlspecialchars($channelLink); ?>" class="btn btn-light text-dark d-inline-flex align-items-center" target="_blank" rel="noopener">
                                            <i class="ti ti-external-link me-2"></i>Kunjungi Kanal
                                        </a>
                                        <a href="<?php echo htmlspecialchars($feedMeta['feed_url'] ?? $portalUrl); ?>" class="btn btn-outline-light d-inline-flex align-items-center" target="_blank" rel="noopener">
                                            <i class="ti ti-rss me-2"></i>Lihat RSS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($feedError) : ?>
                <div class="card border border-dashed mb-0 mx-3">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="ti ti-rss text-primary fs-32"></i>
                        </div>
                        <h5 class="mb-2">Feed tidak tersedia</h5>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($feedError); ?> Silakan coba lagi beberapa saat lagi.</p>
                    </div>
                </div>
            <?php else : ?>
                <div class="antara-feed-carousel-header d-flex align-items-center justify-content-between px-3 pt-3">
                        <h6 class="mb-0 text-uppercase text-muted fw-semibold fs-12">Sorotan Kanal</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-muted fs-12" data-feed-indicator>
                                <span><?php echo $totalSlides > 0 ? 1 : 0; ?></span>/<span><?php echo (int) $totalSlides; ?></span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light border carousel-nav carousel-prev" type="button" data-scroll="prev" aria-label="Slide sebelumnya">
                                <i class="ti ti-chevron-left"></i>
                            </button>
                                <button class="btn btn-sm btn-light border carousel-nav carousel-next" type="button" data-scroll="next" aria-label="Slide berikutnya">
                                <i class="ti ti-chevron-right"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                    <div class="antara-feed-skeleton" data-feed-skeleton>
                        <?php for ($s = 0; $s < 3; $s++) : ?>
                            <div class="antara-feed-skeleton-item">
                                <div class="antara-feed-skeleton-thumb"></div>
                                <div class="d-grid gap-2">
                                    <div class="antara-feed-skeleton-line"></div>
                                    <div class="antara-feed-skeleton-line-sm" style="width: 60%;"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="antara-feed-track" data-feed-track>
                        <?php foreach ($feedSlides as $slideIndex => $slideItems) : ?>
                            <?php
                                $slideHero = $slideItems[0] ?? null;
                                $slideHeroImage = $slideHero !== null && $slideHero['image'] !== '' ? $slideHero['image'] : $placeholderImage;
                                $slideHeroTitle = $slideHero['title'] ?? $heroTitle;
                                $slideHeroLink = $slideHero['link'] ?? $heroLink;
                                $slideHeroExcerpt = $slideHero['excerpt'] ?? '';
                            ?>
                            <div class="antara-feed-slide <?php echo $slideIndex === 0 ? 'is-active' : ''; ?>" data-feed-slide data-slide-index="<?php echo (int) $slideIndex; ?>" data-hero-image="<?php echo htmlspecialchars($slideHeroImage); ?>" data-hero-title="<?php echo htmlspecialchars($slideHeroTitle); ?>" data-hero-link="<?php echo htmlspecialchars($slideHeroLink); ?>" data-hero-excerpt="<?php echo htmlspecialchars($slideHeroExcerpt); ?>">
                                <div class="row g-3">
                                    <?php foreach ($slideItems as $news) : ?>
                                        <?php
                                            $newsImage = $news['image'] !== '' ? $news['image'] : $placeholderImage;
                                            $isExternalImage = preg_match('/^https?:\/\//i', $newsImage) === 1;
                                        ?>
                                        <div class="col-xxl-4 col-lg-4 col-md-6">
                                            <div class="card h-100 antara-feed-card" data-feed-card data-feed-url="<?php echo htmlspecialchars($news['link']); ?>">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="img-sec w-100 position-relative mb-3">
                                                        <a href="<?php echo htmlspecialchars($news['link']); ?>" target="_blank" rel="noopener">
                                                            <img src="<?php echo htmlspecialchars($newsImage); ?>" class="img-fluid rounded w-100" alt="<?php echo htmlspecialchars($news['title']); ?>">
                                                        </a>
                                                        <div>
                                                            <span class="trend-tag badge bg-info-transparent fs-10 fw-medium">Berita Feed</span>
                                                            <?php if ($isExternalImage) : ?>
                                                                <span class="badge badge-success dot-icon"><i class="ti ti-point-filled"></i> ANTARA</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap row-gap-2">
                                                        <div class="d-flex align-items-center text-muted fs-12">
                                                            <i class="ti ti-calendar me-1"></i>
                                                            <?php if ($news['pub_date_iso'] !== '') : ?>
                                                                <time datetime="<?php echo htmlspecialchars($news['pub_date_iso']); ?>">
                                                                    <?php echo htmlspecialchars($news['pub_date_formatted']); ?> WIB
                                                                </time>
                                                            <?php else : ?>
                                                                <span><?php echo htmlspecialchars($news['pub_date_formatted']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <a href="<?php echo htmlspecialchars($news['link']); ?>" class="link-default d-inline-flex align-items-center" target="_blank" rel="noopener">
                                                            <i class="ti ti-external-link me-1"></i>Baca
                                                        </a>
                                                    </div>
                                                    <div class="border-bottom mb-3">
                                                        <span class="antara-feed-card-meta d-block mb-2">
                                                            <?php echo htmlspecialchars($feedMeta['title'] ?? 'ANTARA News'); ?>
                                                        </span>
                                                        <h5 class="mb-2">
                                                            <a href="<?php echo htmlspecialchars($news['link']); ?>" class="fs-16 fw-medium" target="_blank" rel="noopener">
                                                                <?php echo htmlspecialchars($news['title']); ?>
                                                            </a>
                                                        </h5>
                                                        <?php if ($news['excerpt'] !== '') : ?>
                                                            <p class="fs-12 text-muted mb-0"><?php echo htmlspecialchars($news['excerpt']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-auto d-flex align-items-center justify-content-between text-muted fs-12">
                                                        <span><i class="ti ti-building-skyscraper me-1"></i><?php echo htmlspecialchars($selectedGroup !== '' ? $selectedGroup : 'ANTARA News'); ?></span>
                                                        <span><i class="ti ti-broadcast me-1"></i>Sinkron RSS</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($totalSlides > 1) : ?>
                        <div class="antara-feed-dots" data-feed-dots>
                            <?php foreach ($feedSlides as $slideIndex => $_) : ?>
                                <button type="button" class="antara-feed-dot <?php echo $slideIndex === 0 ? 'is-active' : ''; ?>" data-dot-index="<?php echo (int) $slideIndex; ?>" aria-label="Pilih slide <?php echo (int) ($slideIndex + 1); ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Content -->

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var AUTO_DELAY = 2000;

            document.querySelectorAll('[data-feed-carousel]').forEach(function (carousel) {
                var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-feed-slide]'));
                var track = carousel.querySelector('[data-feed-track]');
                var buttons = carousel.querySelectorAll('[data-scroll]');
                var indicator = carousel.querySelector('[data-feed-indicator]');
                var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-feed-dots] .antara-feed-dot'));
                var skeleton = carousel.querySelector('[data-feed-skeleton]');
                var hero = carousel.querySelector('[data-feed-hero]');
                var heroLink = carousel.querySelector('[data-feed-hero-link]');
                var heroTitleNode = carousel.querySelector('[data-feed-hero-title]');
                var heroText = carousel.querySelector('[data-feed-hero-text]');
                var defaultExcerpt = hero ? hero.getAttribute('data-default-excerpt') || '' : '';

                var currentIndex = 0;
                var autoplayTimer = null;
                var isHovered = false;
                var restartAutoplay = function () {};

                var hideSkeleton = function () {
                    if (skeleton && !skeleton.classList.contains('is-hidden')) {
                        skeleton.classList.add('is-hidden');
                    }
                };

                var updateHeroFromSlide = function (slide) {
                    if (!hero || !slide) {
                        return;
                    }
                    var heroImage = slide.getAttribute('data-hero-image');
                    var heroTitle = slide.getAttribute('data-hero-title') || (heroTitleNode ? heroTitleNode.textContent : '');
                    var heroTarget = slide.getAttribute('data-hero-link') || (heroLink ? heroLink.getAttribute('href') : '#');
                    var heroExcerpt = slide.getAttribute('data-hero-excerpt') || defaultExcerpt;

                    if (heroImage) {
                        hero.style.backgroundImage = "linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.35) 55%), url('" + heroImage + "')";
                    }
                    if (heroTitleNode) {
                        heroTitleNode.textContent = heroTitle;
                    }
                    if (heroLink) {
                        heroLink.setAttribute('href', heroTarget);
                    }
                    if (heroText) {
                        heroText.textContent = heroExcerpt || defaultExcerpt;
                    }
                };

                if (!slides.length) {
                    buttons.forEach(function (btn) { btn.disabled = true; });
                    if (indicator) {
                        var indicatorSpans = indicator.querySelectorAll('span');
                        if (indicatorSpans.length > 0) {
                            indicatorSpans[0].textContent = '0';
                        }
                    }
                    hideSkeleton();
                    return;
                }

                var updateIndicator = function () {
                    if (!indicator) {
                        return;
                    }
                    var indicatorSpans = indicator.querySelectorAll('span');
                    if (indicatorSpans.length > 0) {
                        indicatorSpans[0].textContent = (currentIndex + 1).toString();
                    }
                };

                var updateDots = function () {
                    dots.forEach(function (dot, index) {
                        dot.classList.toggle('is-active', index === currentIndex);
                    });
                };

                var applySlideLayout = function () {
                    if (!slides.length) {
                        return;
                    }

                    var activeHeight = 0;
                    slides.forEach(function (slide, index) {
                        var offset = index - currentIndex;
                        var isActive = offset === 0;
                        var translate = offset * 102;

                        slide.style.transform = 'translateX(' + translate + '%)';
                        slide.style.opacity = isActive ? '1' : '0';
                        slide.style.pointerEvents = isActive ? 'auto' : 'none';
                        slide.classList.toggle('is-active', isActive);

                        if (isActive) {
                            activeHeight = slide.scrollHeight;
                        }
                    });

                    if (track && activeHeight) {
                        track.style.height = activeHeight + 'px';
                    }
                };

                var updateButtons = function () {
                    buttons.forEach(function (button) {
                        if (button.dataset.scroll === 'prev') {
                            button.disabled = currentIndex === 0;
                        } else if (button.dataset.scroll === 'next') {
                            button.disabled = currentIndex >= slides.length - 1;
                        }
                    });
                };

                var updateSlides = function () {
                    applySlideLayout();
                    updateButtons();
                    updateIndicator();
                    updateDots();
                    updateHeroFromSlide(slides[currentIndex]);
                    hideSkeleton();
                };

                var goToSlide = function (index) {
                    if (index < 0 || index >= slides.length || index === currentIndex) {
                        return;
                    }
                    currentIndex = index;
                    updateSlides();
                    restartAutoplay();
                };

                buttons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        if (button.dataset.scroll === 'next') {
                            goToSlide(Math.min(slides.length - 1, currentIndex + 1));
                        } else {
                            goToSlide(Math.max(0, currentIndex - 1));
                        }
                    });
                });

                dots.forEach(function (dot) {
                    dot.addEventListener('click', function () {
                        var targetIndex = parseInt(dot.getAttribute('data-dot-index') || '0', 10);
                        if (!Number.isNaN(targetIndex)) {
                            goToSlide(targetIndex);
                        }
                    });
                });

                var stopAutoplay = function () {
                    if (autoplayTimer !== null) {
                        window.clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                };

                var startAutoplay = function () {
                    stopAutoplay();
                    if (slides.length <= 1 || document.hidden || isHovered) {
                        return;
                    }
                    autoplayTimer = window.setInterval(function () {
                        if (document.hidden || isHovered) {
                            return;
                        }
                        var nextIndex = currentIndex + 1;
                        if (nextIndex >= slides.length) {
                            nextIndex = 0;
                        }
                        goToSlide(nextIndex);
                    }, AUTO_DELAY);
                };

                restartAutoplay = function () {
                    if (slides.length <= 1) {
                        stopAutoplay();
                        return;
                    }
                    startAutoplay();
                };

                carousel.addEventListener('mouseenter', function () {
                    isHovered = true;
                    stopAutoplay();
                });

                carousel.addEventListener('mouseleave', function () {
                    isHovered = false;
                    startAutoplay();
                });

                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stopAutoplay();
                    } else {
                        startAutoplay();
                    }
                });

                window.requestAnimationFrame(function () {
                    updateSlides();
                    startAutoplay();
                });

                window.addEventListener('resize', function () {
                    applySlideLayout();
                });
            });
        });
    </script>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>
