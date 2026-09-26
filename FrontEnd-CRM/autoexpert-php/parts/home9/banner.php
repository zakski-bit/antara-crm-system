<?php
$defaultBannerVideo = 'videos/hitam_1min.mp4';
$videoFeedUrl = 'https://www.antaranews.com/rss/video.xml';
$bannerVideoSrc = $defaultBannerVideo;

/**
 * Simple helper to fetch remote resources with curl fallback.
 */
function banner_fetch_remote($url)
{
    $result = '';

    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'AutoExpert Video Banner/1.0',
        ]);
        $result = curl_exec($curl) ?: '';
        curl_close($curl);
    }

    if ($result === '' && ini_get('allow_url_fopen')) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'AutoExpert Video Banner/1.0',
            ],
        ]);
        $result = @file_get_contents($url, false, $context) ?: '';
    }

    return $result;
}

/**
 * Normalize relative URLs and force HTTPS so browsers do not block playback.
 */
function banner_resolve_video_url($videoUrl, $refererUrl)
{
    if ($videoUrl === '') {
        return '';
    }

    if (strpos($videoUrl, '//') === 0) {
        $videoUrl = 'https:' . $videoUrl;
    } elseif (strpos($videoUrl, '/') === 0 && strpos($refererUrl, 'http') === 0) {
        $parsed = parse_url($refererUrl);
        if (!empty($parsed['scheme']) && !empty($parsed['host'])) {
            $videoUrl = $parsed['scheme'] . '://' . $parsed['host'] . $videoUrl;
        }
    }

    if (stripos($videoUrl, 'http://') === 0) {
        $videoUrl = 'https://' . substr($videoUrl, 7);
    }

    return $videoUrl;
}

$feedResponse = banner_fetch_remote($videoFeedUrl);

if ($feedResponse !== '' && function_exists('simplexml_load_string')) {
    libxml_use_internal_errors(true);
    $xml = @simplexml_load_string($feedResponse, 'SimpleXMLElement', LIBXML_NOCDATA);

    if ($xml && isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $videoLinkCandidates = [];

            if (isset($item->link)) {
                foreach ($item->link as $linkNode) {
                    $linkValue = trim((string) $linkNode);
                    if ($linkValue !== '') {
                        $videoLinkCandidates[] = $linkValue;
                    }
                }
            }

            foreach ($videoLinkCandidates as $candidateUrl) {
                if (strpos($candidateUrl, '/video/') === false) {
                    continue;
                }

                $pageContent = banner_fetch_remote($candidateUrl);
                if ($pageContent === '') {
                    continue;
                }

                if (preg_match('/<source[^>]+src="([^"]+\\.mp4[^"]*)"[^>]*type="video\\/mp4"/i', $pageContent, $matches)) {
                    $videoUrl = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                    $videoUrl = banner_resolve_video_url($videoUrl, $candidateUrl);
                    if ($videoUrl !== '') {
                        $bannerVideoSrc = $videoUrl;
                        break 2;
                    }
                }

                if (preg_match('/data-video-url="([^"]+\\.mp4[^"]*)"/i', $pageContent, $matches)) {
                    $videoUrl = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                    $videoUrl = banner_resolve_video_url($videoUrl, $candidateUrl);
                    if ($videoUrl !== '') {
                        $bannerVideoSrc = $videoUrl;
                        break 2;
                    }
                }
            }
        }
    }

    libxml_clear_errors();
}
?>
<section id="home" class="banner-section-three">
	<div class="banner-carousel owl-carousel owl-theme">
		<!-- Slide Item -->
		<div class="slide-item" style="position:relative;">
			<div class="video-background">
				<video class="w-100 h-100" autoplay muted loop playsinline preload="auto">
					<source src="<?php echo htmlspecialchars($bannerVideoSrc, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
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
		<!-- Slide Item -->
		<div class="slide-item" style="position:relative;">
			<div class="video-background">
				<video class="w-100 h-100" autoplay muted loop playsinline preload="auto">
					<source src="<?php echo htmlspecialchars($bannerVideoSrc, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
				</video>
			</div>
			<div class="overlay-2"></div>
			<div class="auto-container">
				<div class="content-box">
					<span class="sub-title animate-2 titleantara">JURNALIS ANTARA</span>
					<h1 class="title animate-3">Liputan eksklusif<br class="d-none d-md-block">dari seluruh Nusantara</h1>
					<div class="text animate-4">Rasakan pengalaman membaca yang menyeluruh melalui liputan foto, video, dan teks yang dikirim langsung dari 34 biro ANTARA di Indonesia. Setiap cerita kami hadirkan dengan standar jurnalisme tertinggi.</div>
					<div class="btn-box animate-4">
						<a href="page-contact.php" class="theme-btn btn-style-one hvr-light"><span class="btn-title">Hubungi Redaksi</span></a>
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
