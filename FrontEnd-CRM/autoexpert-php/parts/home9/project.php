<?php
$feedUrl = 'https://www.antaranews.com/rss/top-news.xml';
$maxItems = 6;
$newsItems = [];
$defaultImage = 'images/resource/project9-1.jpg';

/**
 * Convert RSS publication date into Indonesian date format.
 */
function project_section_format_date($pubDate)
{
    $timestamp = strtotime((string) $pubDate);

    if (!$timestamp) {
        return '';
    }

    $bulanIndonesia = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $bulan = (int) date('n', $timestamp);

    return date('j', $timestamp) . ' ' . ($bulanIndonesia[$bulan] ?? date('F', $timestamp)) . ' ' . date('Y', $timestamp);
}

/**
 * Determine the best image to pair with an RSS item.
 */
function project_section_extract_image($item, $description)
{
    $media = $item->children('media', true);
    if ($media && isset($media->content)) {
        foreach ($media->content as $content) {
            $attributes = $content->attributes();
            if ($attributes && !empty($attributes['url'])) {
                return (string) $attributes['url'];
            }
        }
    }

    if (isset($item->enclosure)) {
        foreach ($item->enclosure as $enclosure) {
            $attributes = $enclosure->attributes();
            if ($attributes && !empty($attributes['url'])) {
                return (string) $attributes['url'];
            }
        }
    }

    if ($description && preg_match('/<img[^>]+src="([^"]+)"/i', $description, $matches)) {
        return $matches[1];
    }

    return '';
}

$feedResponse = '';

if (function_exists('curl_init')) {
    $curl = curl_init($feedUrl);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_USERAGENT => 'AutoExpert News Widget',
    ]);

    $feedResponse = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($httpCode >= 400) {
        $feedResponse = '';
    }
}

if ($feedResponse === '' && ini_get('allow_url_fopen')) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'AutoExpert News Widget',
        ],
    ]);
    $feedResponse = @file_get_contents($feedUrl, false, $context) ?: '';
}

if ($feedResponse !== '') {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($feedResponse, 'SimpleXMLElement', LIBXML_NOCDATA);

    if ($xml && isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $title = trim((string) $item->title);
            $link = trim((string) $item->link);
            $contentEncoded = $item->children('content', true);
            $rawDescription = $contentEncoded && isset($contentEncoded->encoded)
                ? (string) $contentEncoded->encoded
                : (string) $item->description;
            $imageUrl = project_section_extract_image($item, $rawDescription);

            if ($title === '' || $link === '') {
                continue;
            }

            $newsItems[] = [
                'title' => $title,
                'link' => $link,
                'image' => $imageUrl !== '' ? $imageUrl : $defaultImage,
                'published_at' => project_section_format_date($item->pubDate ?? ''),
            ];

            if (count($newsItems) >= $maxItems) {
                break;
            }
        }
    }

    libxml_clear_errors();
}
?>
<section id="project" class="project-section9 pt-0 pb-0">
    <div class="container-fluid pb-0">
        <div class="sec-title text-center">
            <span class="sub-title">Liputan Unggulan</span>
            <h2 class="words-slide-up text-split">Sorotan investigasi dan feature terbaik ANTARA</h2>
        </div>

        <div class="outer-box">
            <div class="projects-carousel9 owl-carousel owl-theme default-dots">
                <?php if (!empty($newsItems)) : ?>
                    <?php foreach ($newsItems as $news) : ?>
                        <div class="project-block9">
                            <div class="inner-box">
                                <div class="image-box">
                                    <figure class="image">
                                        <a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                            <img src="<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </a>
                                    </figure>
                                </div>
                                <div class="content-box">
                                    <div class="meta-row">
                                        <?php if (!empty($news['published_at'])) : ?>
                                            <span class="cat">Dipublikasi <?php echo htmlspecialchars($news['published_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endif; ?>
                                        <a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>" class="theme-btn read-more" target="_blank" rel="noopener">
                                            <i class="far fa-arrow-up"></i>
                                        </a>
                                    </div>
                                    <h4 class="title">
                                        <a href="<?php echo htmlspecialchars($news['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                            <?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div class="overlay-1"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="project-block9">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><a href="#"><img src="images/resource/project9-1.jpg" alt="Liputan Unggulan ANTARA"></a></figure>
                            </div>
                            <div class="content-box">
                                <div class="meta-row">
                                    <span class="cat">Informasi ANTARA</span>
                                    <a href="#" class="theme-btn read-more disabled" aria-disabled="true">
                                        <i class="far fa-arrow-up"></i>
                                    </a>
                                </div>
                                <h4 class="title"><a href="#">Berita terbaru ANTARA belum dapat dimuat</a></h4>
                                <span class="cat">Silakan coba beberapa saat lagi.</span>
                            </div>
                            <div class="overlay-1"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    #project.project-section9 {
        padding-top: 0;
    }
</style>
