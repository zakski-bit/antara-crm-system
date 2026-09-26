<?php
$feedUrl = 'https://www.antaranews.com/rss/photo.xml';
$maxItems = 6;
$newsItems = [];
$monthMap = [
    1 => 'Jan',
    2 => 'Feb',
    3 => 'Mar',
    4 => 'Apr',
    5 => 'Mei',
    6 => 'Jun',
    7 => 'Jul',
    8 => 'Agu',
    9 => 'Sep',
    10 => 'Okt',
    11 => 'Nov',
    12 => 'Des',
];

libxml_use_internal_errors(true);
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'user_agent' => 'ANTARA News Widget/1.0 (+https://www.antaranews.com)',
    ],
]);
libxml_set_streams_context($context);

$rss = @simplexml_load_file($feedUrl, 'SimpleXMLElement', LIBXML_NOCDATA);
if ($rss !== false && isset($rss->channel->item)) {
    foreach ($rss->channel->item as $item) {
        if (count($newsItems) >= $maxItems) {
            break;
        }

        $dateString = (string) $item->pubDate;
        $date = DateTime::createFromFormat(DateTime::RSS, $dateString);
        if ($date === false) {
            try {
                $date = new DateTime($dateString);
            } catch (Exception $e) {
                $date = null;
            }
        }

        $day = $date ? $date->format('d') : '';
        $monthYear = $date ? (($monthMap[(int) $date->format('n')] ?? $date->format('M')) . ' ' . $date->format('Y')) : '';

        $image = '';
        $mediaChildren = $item->children('media', true);
        if ($mediaChildren && $mediaChildren->content) {
            $mediaAttr = $mediaChildren->content->attributes();
            if ($mediaAttr && isset($mediaAttr['url'])) {
                $image = (string) $mediaAttr['url'];
            }
        }
        if (!$image && isset($item->enclosure)) {
            $enclosureAttr = $item->enclosure->attributes();
            if ($enclosureAttr && isset($enclosureAttr['url'])) {
                $image = (string) $enclosureAttr['url'];
            }
        }
        if (!$image && isset($item->description)) {
            if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', (string) $item->description, $matches)) {
                $image = $matches[1];
            }
        }

        $newsItems[] = [
            'title' => trim((string) $item->title),
            'link' => (string) $item->link,
            'image' => $image ?: 'images/resource/news1-1.jpg',
            'day' => $day,
            'month_year' => $monthYear,
        ];
    }
}

if (empty($newsItems)) {
    // Fallback to existing static items if the feed is unavailable.
    $newsItems = [
        [
            'title' => 'ANTARA dokumentasikan festival budaya nusantara',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-1.jpg',
            'day' => '12',
            'month_year' => 'Okt 2025',
        ],
        [
            'title' => 'Sorotan foto: panorama alam Indonesia terkini',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-2.jpg',
            'day' => '11',
            'month_year' => 'Okt 2025',
        ],
        [
            'title' => 'Potret ekspresi warga dalam kegiatan sosial',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-3.jpg',
            'day' => '10',
            'month_year' => 'Okt 2025',
        ],
        [
            'title' => 'Dinamika olahraga nasional melalui foto ANTARA',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-1.jpg',
            'day' => '09',
            'month_year' => 'Okt 2025',
        ],
        [
            'title' => 'Ragam foto inspiratif karya jurnalis daerah',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-2.jpg',
            'day' => '08',
            'month_year' => 'Okt 2025',
        ],
        [
            'title' => 'Momen humaniora terhangat dalam bingkai kamera',
            'link' => 'https://www.antaranews.com/foto',
            'image' => 'images/resource/news1-3.jpg',
            'day' => '07',
            'month_year' => 'Okt 2025',
        ],
    ];
}

libxml_clear_errors();
?>
<section id="news" class="news-section-two">
    <div class="auto-container">
        <div class="row">
            <div class="title-column col-lg-3 col-md-4">
                <div class="inner-column">
                    <div class="sec-title">
                        <span class="sub-title titleantara"><img src="images/icons/titleantara.png" alt="Logo ANTARA" class="titleantara__logo">Berita Terkini</span>
                        <h2>Berita dan Informasi Terkini<br />dari seluruh Indonesia</h2>
                        <div class="text">Ikuti perkembangan berita dan informasi terbaru yang disajikan secara akurat dan terpercaya oleh tim jurnalis ANTARA.</div>
                    </div>
                </div>
            </div>

            <div class="carousel-column col-lg-9 col-md-8">
                <div class="carousel-outer">
                    <div class="news-carousel owl-carousel owl-theme">
                        <?php foreach ($newsItems as $news) : ?>
                            <div class="news-block-three wow fadeInUp">
                                <div class="inner-box">
                                    <div class="image-box">
                                        <figure class="image">
                                            <a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <img src="<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <img src="<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                            </a>
                                        </figure>
                                        <?php if (!empty($news['day']) && !empty($news['month_year'])) : ?>
                                            <span class="date"><strong><?php echo htmlspecialchars($news['day'], ENT_QUOTES, 'UTF-8'); ?></strong> <span><?php echo htmlspecialchars($news['month_year'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="content-box">
                                        <ul class="post-meta">
                                            <li><i class="fal fa-user"></i>Redaksi ANTARA</li>
                                            <li><i class="fa fa-comment"></i>0 Komentar</li>
                                        </ul>
                                        <h4 class="title"><a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></a></h4>
                                        <a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>" class="theme-btn read-more">Baca Selengkapnya <i class="icon fa fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #news.news-section-two {
        padding: 70px 0 0;
        background: linear-gradient(180deg, #f7f9fc 0%, #ffffff 100%);
    }

    #news.news-section-two .auto-container {
        max-width: none;
        width: 100%;
        padding-left: clamp(20px, 6vw, 96px);
        padding-right: clamp(20px, 6vw, 96px);
    }

    #news.news-section-two .title-column .sec-title {
        max-width: 320px;
        margin-right: auto;
    }

    #news.news-section-two .title-column .sec-title .text {
        font-size: 15px;
        line-height: 1.5;
    }

    #news.news-section-two .carousel-column .news-block-three .inner-box {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    #news.news-section-two .carousel-column .news-block-three .content-box {
        margin-top: auto;
    }

    @media (max-width: 991px) {
        #news.news-section-two {
            padding: 54px 0 0;
        }

        #news.news-section-two .auto-container {
            padding-left: 20px;
            padding-right: 20px;
        }

        #news.news-section-two .title-column .sec-title {
            max-width: 100%;
            text-align: center;
        }
    }
</style>
