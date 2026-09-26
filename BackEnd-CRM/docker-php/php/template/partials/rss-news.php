<?php

declare(strict_types=1);

if (!function_exists('getAntaraFeedsCatalog')) {
    function getAntaraFeedsCatalog(): array
    {
        return [
            'headline' => [
                'label' => 'Headline',
                'feeds' => [
                    'terkini' => [
                        'title' => 'ANTARA News Terkini',
                        'url' => 'https://www.antaranews.com/rss/terkini.xml',
                        'description' => 'Update terbaru lintas desk ANTARA News.',
                        'limit' => 18,
                    ],
                    'top-news' => [
                        'title' => 'ANTARA Top News',
                        'url' => 'https://www.antaranews.com/rss/top-news.xml',
                        'description' => 'Sorotan berita utama pilihan redaksi.',
                    ],
                ],
            ],
            'visual' => [
                'label' => 'Visual & Infografik',
                'feeds' => [
                    'galeri-photo' => [
                        'title' => 'ANTARA News Galeri Photo',
                        'url' => 'https://www.antaranews.com/rss/photo.xml',
                        'description' => 'Kumpulan foto unggulan dari lapangan.',
                    ],
                    'video' => [
                        'title' => 'ANTARA News Video',
                        'url' => 'https://www.antaranews.com/rss/video.xml',
                        'description' => 'Liputan video terbaru ANTARA.',
                    ],
                    'infografik' => [
                        'title' => 'Infografik',
                        'url' => 'https://www.antaranews.com/rss/infografik.xml',
                        'description' => 'Visual data dan penjelasan ringkas.',
                    ],
                ],
            ],
            'nasional' => [
                'label' => 'Nasional',
                'feeds' => [
                    'politik' => [
                        'title' => 'Politik',
                        'url' => 'https://www.antaranews.com/rss/politik.xml',
                        'description' => 'Dinamika politik nasional dan pemerintahan.',
                    ],
                    'hukum' => [
                        'title' => 'Hukum',
                        'url' => 'https://www.antaranews.com/rss/hukum.xml',
                        'description' => 'Penegakan hukum dan isu regulasi.',
                    ],
                    'humaniora' => [
                        'title' => 'Humaniora',
                        'url' => 'https://www.antaranews.com/rss/humaniora.xml',
                        'description' => 'Pendidikan, kesehatan, dan isu sosial.',
                    ],
                ],
            ],
            'ekonomi' => [
                'label' => 'Ekonomi & Bisnis',
                'feeds' => [
                    'ekonomi' => [
                        'title' => 'Ekonomi',
                        'url' => 'https://www.antaranews.com/rss/ekonomi.xml',
                        'description' => 'Makro ekonomi dan kebijakan fiskal.',
                    ],
                    'ekonomi-finansial' => [
                        'title' => 'Finansial',
                        'url' => 'https://www.antaranews.com/rss/ekonomi-finansial.xml',
                        'description' => 'Perbankan, pasar uang, dan investasi.',
                    ],
                    'ekonomi-bisnis' => [
                        'title' => 'Bisnis',
                        'url' => 'https://www.antaranews.com/rss/ekonomi-bisnis.xml',
                        'description' => 'Korporasi, UKM, dan strategi bisnis.',
                    ],
                    'ekonomi-bursa' => [
                        'title' => 'Bursa',
                        'url' => 'https://www.antaranews.com/rss/ekonomi-bursa.xml',
                        'description' => 'Pasar modal dan pergerakan saham.',
                    ],
                ],
            ],
            'metro' => [
                'label' => 'Metro',
                'feeds' => [
                    'metro' => [
                        'title' => 'Metro',
                        'url' => 'https://www.antaranews.com/rss/metro.xml',
                        'description' => 'Isu perkotaan dan layanan publik.',
                    ],
                    'metro-kriminalitas' => [
                        'title' => 'Kriminalitas',
                        'url' => 'https://www.antaranews.com/rss/metro-kriminalitas.xml',
                        'description' => 'Peristiwa kriminal wilayah metropolitan.',
                    ],
                    'metro-lintas-kota' => [
                        'title' => 'Lintas Kota',
                        'url' => 'https://www.antaranews.com/rss/metro-lintas-kota.xml',
                        'description' => 'Kabar dari berbagai kota di Indonesia.',
                    ],
                    'metro-lenggang-jakarta' => [
                        'title' => 'Lenggang Jakarta',
                        'url' => 'https://www.antaranews.com/rss/metro-lenggang-jakarta.xml',
                        'description' => 'Sorotan aktivitas di Jakarta.',
                    ],
                ],
            ],
            'sepakbola' => [
                'label' => 'Sepakbola',
                'feeds' => [
                    'sepakbola' => [
                        'title' => 'Sepakbola',
                        'url' => 'https://www.antaranews.com/rss/sepakbola.xml',
                        'description' => 'Berita sepakbola nasional dan dunia.',
                    ],
                    'sepakbola-indonesia' => [
                        'title' => 'Liga Indonesia',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-indonesia.xml',
                        'description' => 'Kompetisi dan klub tanah air.',
                    ],
                    'sepakbola-internasional' => [
                        'title' => 'Sepakbola Internasional',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-internasional.xml',
                        'description' => 'Liga top dunia dan tim nasional.',
                    ],
                    'sepakbola-liga-inggris' => [
                        'title' => 'Liga Inggris',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-inggris-premier.xml',
                        'description' => 'Premier League dan klub Inggris.',
                    ],
                    'sepakbola-liga-spanyol' => [
                        'title' => 'Liga Spanyol',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-spanyol.xml',
                        'description' => 'La Liga dan klub Spanyol.',
                    ],
                    'sepakbola-liga-italia' => [
                        'title' => 'Liga Italia',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-italia-seri-a.xml',
                        'description' => 'Serie A dan klub Italia.',
                    ],
                    'sepakbola-liga-champions' => [
                        'title' => 'Liga Champions',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-champions.xml',
                        'description' => 'Kompetisi antarklub Eropa.',
                    ],
                    'sepakbola-lain' => [
                        'title' => 'Liga-Liga Lain',
                        'url' => 'https://www.antaranews.com/rss/sepakbola-liga-liga-dunia.xml',
                        'description' => 'Liga dan turnamen global lainnya.',
                    ],
                ],
            ],
            'olahraga' => [
                'label' => 'Olahraga',
                'feeds' => [
                    'olahraga' => [
                        'title' => 'Olahraga',
                        'url' => 'https://www.antaranews.com/rss/olahraga.xml',
                        'description' => 'Lintas cabang olahraga non sepakbola.',
                    ],
                    'olahraga-bulutangkis' => [
                        'title' => 'Bulutangkis',
                        'url' => 'https://www.antaranews.com/rss/olahraga-bulutangkis.xml',
                        'description' => 'Turnamen dan atlet bulutangkis.',
                    ],
                    'olahraga-bola-basket' => [
                        'title' => 'Bola Basket',
                        'url' => 'https://www.antaranews.com/rss/olahraga-bola-basket.xml',
                        'description' => 'Berita bola basket nasional dan internasional.',
                    ],
                    'olahraga-tenis' => [
                        'title' => 'Tenis',
                        'url' => 'https://www.antaranews.com/rss/olahraga-tenis.xml',
                        'description' => 'Pertandingan dan peringkat tenis.',
                    ],
                    'olahraga-balap' => [
                        'title' => 'Balap',
                        'url' => 'https://www.antaranews.com/rss/olahraga-balap.xml',
                        'description' => 'MotoGP, F1, dan olahraga balap lainnya.',
                    ],
                    'olahraga-all-sport' => [
                        'title' => 'All Sport',
                        'url' => 'https://www.antaranews.com/rss/olahraga-all-sport.xml',
                        'description' => 'Sorotan ragam cabang olahraga.',
                    ],
                ],
            ],
            'lifestyle' => [
                'label' => 'Gaya Hidup & Hiburan',
                'feeds' => [
                    'lifestyle' => [
                        'title' => 'Lifestyle',
                        'url' => 'https://www.antaranews.com/rss/lifestyle.xml',
                        'description' => 'Tren gaya hidup, kuliner, dan travel.',
                    ],
                    'hiburan' => [
                        'title' => 'Hiburan',
                        'url' => 'https://www.antaranews.com/rss/hiburan.xml',
                        'description' => 'Musik, film, dan budaya pop.',
                    ],
                ],
            ],
            'global' => [
                'label' => 'Dunia',
                'feeds' => [
                    'dunia' => [
                        'title' => 'Dunia',
                        'url' => 'https://www.antaranews.com/rss/dunia.xml',
                        'description' => 'Perkembangan global lintas kawasan.',
                    ],
                    'dunia-asean' => [
                        'title' => 'ASEAN',
                        'url' => 'https://www.antaranews.com/rss/dunia-asean.xml',
                        'description' => 'Kabar negara-negara ASEAN.',
                    ],
                    'dunia-internasional' => [
                        'title' => 'Internasional',
                        'url' => 'https://www.antaranews.com/rss/dunia-internasional.xml',
                        'description' => 'Sorotan isu internasional utama.',
                    ],
                    'international-corner' => [
                        'title' => 'International Corner',
                        'url' => 'https://www.antaranews.com/rss/dunia-internasional-corner.xml',
                        'description' => 'Laporan mendalam dunia internasional.',
                    ],
                ],
            ],
            'english' => [
                'label' => 'English Desk',
                'feeds' => [
                    'english-news' => [
                        'title' => 'ANTARA English News',
                        'url' => 'https://en.antaranews.com/rss/news.xml',
                        'description' => 'Curated news in English for global partners.',
                    ],
                ],
            ],
            'daerah' => [
                'label' => 'Kanal Daerah',
                'feeds' => [
                    'nusantara' => [
                        'title' => 'Nusantara',
                        'url' => 'https://www.antaranews.com/rss/nusantara.xml',
                        'description' => 'Sorotan regional nasional lintas provinsi.',
                    ],
                    'daerah-aceh' => [
                        'title' => 'Aceh',
                        'url' => 'https://aceh.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita terbaru dari biro ANTARA Aceh.',
                    ],
                    'daerah-ambon' => [
                        'title' => 'Maluku (Ambon)',
                        'url' => 'https://ambon.antaranews.com/rss/terkini.xml',
                        'description' => 'Update wilayah Maluku dari biro Ambon.',
                    ],
                    'daerah-bali' => [
                        'title' => 'Bali',
                        'url' => 'https://bali.antaranews.com/rss/terkini.xml',
                        'description' => 'Liputan ekonomi dan budaya dari Bali.',
                    ],
                    'daerah-babel' => [
                        'title' => 'Bangka Belitung',
                        'url' => 'https://babel.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita Bangka Belitung pilihan redaksi daerah.',
                    ],
                    'daerah-banten' => [
                        'title' => 'Banten',
                        'url' => 'https://banten.antaranews.com/rss/terkini.xml',
                        'description' => 'Isu pelayanan publik dan ekonomi Banten.',
                    ],
                    'daerah-bengkulu' => [
                        'title' => 'Bengkulu',
                        'url' => 'https://bengkulu.antaranews.com/rss/terkini.xml',
                        'description' => 'Peristiwa dan kebijakan di Bengkulu.',
                    ],
                    'daerah-gorontalo' => [
                        'title' => 'Gorontalo',
                        'url' => 'https://gorontalo.antaranews.com/rss/terkini.xml',
                        'description' => 'Update sosial ekonomi Gorontalo.',
                    ],
                    'daerah-jateng' => [
                        'title' => 'Jawa Tengah',
                        'url' => 'https://jateng.antaranews.com/rss/terkini.xml',
                        'description' => 'Sorotan Jawa Tengah dan sekitarnya.',
                    ],
                    'daerah-jabar' => [
                        'title' => 'Jawa Barat',
                        'url' => 'https://jabar.antaranews.com/rss/terkini.xml',
                        'description' => 'Liputan pemerintahan dan komunitas Jawa Barat.',
                    ],
                    'daerah-jatim' => [
                        'title' => 'Jawa Timur',
                        'url' => 'https://jatim.antaranews.com/rss/terkini.xml',
                        'description' => 'Komprehensif berita dari Jatim.',
                    ],
                    'daerah-jambi' => [
                        'title' => 'Jambi',
                        'url' => 'https://jambi.antaranews.com/rss/terkini.xml',
                        'description' => 'Informasi pemerintahan dan ekonomi daerah Jambi.',
                    ],
                    'daerah-kalbar' => [
                        'title' => 'Kalimantan Barat',
                        'url' => 'https://kalbar.antaranews.com/rss/terkini.xml',
                        'description' => 'Sorotan Kalimantan Barat dari lapangan.',
                    ],
                    'daerah-kalsel' => [
                        'title' => 'Kalimantan Selatan',
                        'url' => 'https://kalsel.antaranews.com/rss/terkini.xml',
                        'description' => 'Liputan banua dan ekonomi Kalsel.',
                    ],
                    'daerah-kalteng' => [
                        'title' => 'Kalimantan Tengah',
                        'url' => 'https://kalteng.antaranews.com/rss/terkini.xml',
                        'description' => 'Kabar terbaru dari Kalimantan Tengah.',
                    ],
                    'daerah-kaltim' => [
                        'title' => 'Kalimantan Timur',
                        'url' => 'https://kaltim.antaranews.com/rss/terkini.xml',
                        'description' => 'Sorotan pembangunan dan energi Kaltim.',
                    ],
                    'daerah-kaltara' => [
                        'title' => 'Kalimantan Utara',
                        'url' => 'https://kaltara.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita lintas perbatasan Kalimantan Utara.',
                    ],
                    'daerah-kepri' => [
                        'title' => 'Kepulauan Riau',
                        'url' => 'https://kepri.antaranews.com/rss/terkini.xml',
                        'description' => 'Kabar utama dari provinsi kepulauan Riau.',
                    ],
                    'daerah-lampung' => [
                        'title' => 'Lampung',
                        'url' => 'https://lampung.antaranews.com/rss/terkini.xml',
                        'description' => 'Tren sosial ekonomi seputar Lampung.',
                    ],
                    'daerah-papua' => [
                        'title' => 'Papua',
                        'url' => 'https://papua.antaranews.com/rss/terkini.xml',
                        'description' => 'Liputan pembangunan dan budaya Papua.',
                    ],
                    'daerah-papua-barat' => [
                        'title' => 'Papua Barat',
                        'url' => 'https://papuabarat.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita Papua Barat dan kawasan Sorong Raya.',
                    ],
                    'daerah-riau' => [
                        'title' => 'Riau',
                        'url' => 'https://riau.antaranews.com/rss/terkini.xml',
                        'description' => 'Sorotan industri dan lingkungan Riau.',
                    ],
                    'daerah-sulawesi-tengah' => [
                        'title' => 'Sulawesi Tengah',
                        'url' => 'https://sulteng.antaranews.com/rss/terkini.xml',
                        'description' => 'Kabar utama dari Sulawesi Tengah.',
                    ],
                    'daerah-sulawesi-tenggara' => [
                        'title' => 'Sulawesi Tenggara',
                        'url' => 'https://sultra.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita pertambangan dan maritim Sultra.',
                    ],
                    'daerah-sumatera-barat' => [
                        'title' => 'Sumatera Barat',
                        'url' => 'https://sumbar.antaranews.com/rss/terkini.xml',
                        'description' => 'Sorotan Minangkabau dan Sumbar.',
                    ],
                    'daerah-sumatera-selatan' => [
                        'title' => 'Sumatera Selatan',
                        'url' => 'https://sumsel.antaranews.com/rss/terkini.xml',
                        'description' => 'Update Palembang dan sekitarnya.',
                    ],
                    'daerah-sumatera-utara' => [
                        'title' => 'Sumatera Utara',
                        'url' => 'https://sumut.antaranews.com/rss/terkini.xml',
                        'description' => 'Berita Medan dan kawasan Sumut.',
                    ],
                ],
            ],
            'tekno' => [
                'label' => 'Tekno',
                'feeds' => [
                    'tekno' => [
                        'title' => 'Tekno',
                        'url' => 'https://www.antaranews.com/rss/tekno.xml',
                        'description' => 'Inovasi teknologi dan sains.',
                    ],
                ],
            ],
            'otomotif' => [
                'label' => 'Otomotif',
                'feeds' => [
                    'otomotif' => [
                        'title' => 'Otomotif',
                        'url' => 'https://www.antaranews.com/rss/otomotif.xml',
                        'description' => 'Perkembangan industri otomotif.',
                    ],
                    'otomotif-umum' => [
                        'title' => 'Otomotif Umum',
                        'url' => 'https://www.antaranews.com/rss/otomotif-umum.xml',
                        'description' => 'Tips dan berita otomotif harian.',
                    ],
                    'otomotif-go-green' => [
                        'title' => 'Otomotif Go-Green',
                        'url' => 'https://www.antaranews.com/rss/otomotif-go-green.xml',
                        'description' => 'Kendaraan listrik dan ramah lingkungan.',
                    ],
                    'otomotif-prototype' => [
                        'title' => 'Otomotif Prototype',
                        'url' => 'https://www.antaranews.com/rss/otomotif-prototype.xml',
                        'description' => 'Konsep dan inovasi kendaraan baru.',
                    ],
                    'otomotif-review' => [
                        'title' => 'Otomotif Review',
                        'url' => 'https://www.antaranews.com/rss/otomotif-review.xml',
                        'description' => 'Ulasan kendaraan terbaru.',
                    ],
                ],
            ],
            'lingkungan' => [
                'label' => 'Lingkungan & CSR',
                'feeds' => [
                    'warta-bumi' => [
                        'title' => 'Warta Bumi',
                        'url' => 'https://www.antaranews.com/rss/warta-bumi.xml',
                        'description' => 'Isu lingkungan dan keberlanjutan.',
                    ],
                    'rilis-pers' => [
                        'title' => 'Rilis Pers',
                        'url' => 'https://www.antaranews.com/rss/rilis-pers.xml',
                        'description' => 'Rilis resmi korporasi dan lembaga.',
                    ],
                ],
            ],
        ];
    }
}

if (!function_exists('getAntaraFeedRegistry')) {
    function getAntaraFeedRegistry(): array
    {
        static $registry = null;

        if ($registry === null) {
            $registry = [];
            foreach (getAntaraFeedsCatalog() as $categoryKey => $category) {
                $label = $category['label'] ?? $categoryKey;
                foreach ($category['feeds'] as $slug => $feed) {
                    $registry[$slug] = [
                        'slug' => $slug,
                        'category_key' => $categoryKey,
                        'group' => $label,
                        'title' => $feed['title'] ?? $slug,
                        'url' => $feed['url'],
                        'description' => $feed['description'] ?? '',
                    ];
                    if (isset($feed['limit'])) {
                        $registry[$slug]['limit'] = (int) $feed['limit'];
                    }
                }
            }
        }

        return $registry;
    }
}

if (!function_exists('antaraNormalizeFeedContent')) {
    function antaraNormalizeFeedContent(string $content): string
    {
        if ($content === '') {
            return '';
        }

        if (strncmp($content, "\x1f\x8b", 2) === 0 && function_exists('gzdecode')) {
            $decoded = @gzdecode($content);
            if ($decoded !== false && $decoded !== '') {
                $content = $decoded;
            }
        }

        if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
            $content = substr($content, 3);
        }

        return trim($content);
    }
}

if (!function_exists('antaraNormalizeMediaUrl')) {
    function antaraNormalizeMediaUrl(string $url, array $feedInfo = []): string
    {
        $normalized = trim($url);
        if ($normalized === '') {
            return '';
        }

        if (stripos($normalized, 'http://') === 0) {
            $normalized = 'https://' . substr($normalized, 7);
        }

        if (stripos($normalized, 'https://img.antaranews.com/video/preview/') === 0) {
            $replacement = 'https://video.antaranews.com/preview/';
            $normalized = preg_replace('#^https://img\.antaranews\.com/video/preview/#i', $replacement, $normalized) ?? $normalized;
        }

        return $normalized;
    }
}

if (!function_exists('getAntaraNewsFeed')) {
    function getAntaraNewsFeed(string $identifier = 'terkini', int $limit = 12): array
    {
        $registry = getAntaraFeedRegistry();
        $feedInfo = $registry[$identifier] ?? null;

        if ($feedInfo === null && filter_var($identifier, FILTER_VALIDATE_URL)) {
            $feedInfo = [
                'slug' => hash('crc32b', $identifier),
                'category_key' => 'custom',
                'group' => 'Kustom',
                'title' => 'RSS Kustom',
                'url' => $identifier,
                'description' => $identifier,
            ];
        }

        if ($feedInfo === null) {
            $identifier = 'terkini';
            $feedInfo = $registry[$identifier] ?? [
                'slug' => $identifier,
                'category_key' => 'headline',
                'group' => 'Headline',
                'title' => 'ANTARA News Terkini',
                'url' => 'https://www.antaranews.com/rss/terkini.xml',
                'description' => 'Update terbaru lintas desk ANTARA News.',
                'limit' => 18,
            ];
        }

        $rssUrl = $feedInfo['url'];
        $effectiveLimit = $limit > 0 ? $limit : (int) ($feedInfo['limit'] ?? 12);
        if ($effectiveLimit <= 0) {
            $effectiveLimit = 12;
        }

        $result = [
            'items' => [],
            'meta' => [
                'title' => $feedInfo['title'],
                'description' => $feedInfo['description'] ?? '',
                'link' => 'https://www.antaranews.com',
                'feed_url' => $rssUrl,
                'updated_at' => null,
                'updated_at_formatted' => null,
                'updated_at_iso' => null,
                'feed' => [
                    'slug' => $feedInfo['slug'] ?? $identifier,
                    'title' => $feedInfo['title'],
                    'group' => $feedInfo['group'] ?? null,
                    'category_key' => $feedInfo['category_key'] ?? null,
                    'description' => $feedInfo['description'] ?? '',
                    'url' => $rssUrl,
                ],
            ],
            'error' => null,
        ];

        $context = stream_context_create([
            'http' => [
                'timeout' => 6,
                'user_agent' => 'AntaraDashboard/1.0 (+https://www.antaranews.com)',
            ],
        ]);

        $cacheTtl = 300;
        $rssContent = null;
        $cacheFile = null;
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'antara_feed_cache';

        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        if (is_dir($cacheDir)) {
            $safeSlug = preg_replace('/[^a-z0-9\-]+/i', '_', (string) ($feedInfo['slug'] ?? $identifier));
            $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $safeSlug . '.xml';
        }

        if ($cacheFile !== null && is_file($cacheFile) && (time() - (int) @filemtime($cacheFile)) < $cacheTtl) {
            $cachedRaw = @file_get_contents($cacheFile);
            if ($cachedRaw !== false && $cachedRaw !== null) {
                $normalizedCached = antaraNormalizeFeedContent((string) $cachedRaw);
                if ($normalizedCached !== '') {
                    $rssContent = $normalizedCached;
                    if ($normalizedCached !== (string) $cachedRaw) {
                        @file_put_contents($cacheFile, $normalizedCached);
                    }
                }
            }
        }

        if ($rssContent === null) {
            $fetchedRaw = @file_get_contents($rssUrl, false, $context);
            if ($fetchedRaw !== false && $fetchedRaw !== null) {
                $normalizedFetched = antaraNormalizeFeedContent((string) $fetchedRaw);
                if ($normalizedFetched !== '') {
                    $rssContent = $normalizedFetched;
                    if ($cacheFile !== null) {
                        @file_put_contents($cacheFile, $normalizedFetched);
                    }
                }
            }
        }

        if ($rssContent === null) {
            $result['error'] = 'Feed tidak dapat dimuat.';
            return $result;
        }

        $previousLibxmlState = libxml_use_internal_errors(true);
        $rss = @simplexml_load_string($rssContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        $libxmlErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxmlState);

        if ($rss === false || !isset($rss->channel)) {
            if (!empty($libxmlErrors)) {
                $firstError = $libxmlErrors[0];
                $errorMessage = trim($firstError->message ?? '');
                if ($errorMessage !== '') {
                    error_log(sprintf('ANTARA RSS parse error (%s): %s', $rssUrl, $errorMessage));
                }
            }
            $result['error'] = 'Format feed tidak valid.';
            return $result;
        }

        $channel = $rss->channel;

        if (isset($channel->title)) {
            $result['meta']['title'] = (string) $channel->title;
        }

        if (isset($channel->description)) {
            $result['meta']['description'] = trim((string) $channel->description);
        }

        if (isset($channel->link)) {
            $result['meta']['link'] = (string) $channel->link;
        }

        if (isset($channel->lastBuildDate)) {
            $lastBuildRaw = (string) $channel->lastBuildDate;
            try {
                $updatedAt = new DateTime($lastBuildRaw);
                $updatedAt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $result['meta']['updated_at'] = $updatedAt;
                $result['meta']['updated_at_formatted'] = $updatedAt->format('d M Y H:i');
                $result['meta']['updated_at_iso'] = $updatedAt->format(DateTime::ATOM);
            } catch (Exception $e) {
                $result['meta']['updated_at'] = null;
                $result['meta']['updated_at_formatted'] = $lastBuildRaw;
                $result['meta']['updated_at_iso'] = $lastBuildRaw;
            }
        }

        $namespaces = $rss->getNamespaces(true);

        foreach ($channel->item as $item) {
            if ($effectiveLimit > 0 && count($result['items']) >= $effectiveLimit) {
                break;
            }

            $title = isset($item->title) ? trim((string) $item->title) : '';
            $link = isset($item->link) ? (string) $item->link : '#';
            $pubDateRaw = isset($item->pubDate) ? (string) $item->pubDate : '';
            $description = isset($item->description) ? (string) $item->description : '';

            if ($description === '' && isset($namespaces['content'])) {
                $contentChild = $item->children($namespaces['content']);
                if (isset($contentChild->encoded)) {
                    $description = (string) $contentChild->encoded;
                }
            }

            $imageUrl = '';
            if (isset($namespaces['media'])) {
                $mediaChild = $item->children($namespaces['media']);
                if (isset($mediaChild->content)) {
                    foreach ($mediaChild->content as $mediaContent) {
                        $attributes = $mediaContent->attributes();
                        if (isset($attributes['url'])) {
                            $imageUrl = (string) $attributes['url'];
                            break;
                        }
                    }
                }
            }

            if ($imageUrl === '' && isset($item->enclosure)) {
                $enclosureAttributes = $item->enclosure->attributes();
                if (isset($enclosureAttributes['url'])) {
                    $imageUrl = (string) $enclosureAttributes['url'];
                }
            }

            if ($imageUrl === '') {
                if (preg_match('/<img[^>]+src="([^"]+)"/i', $description, $matches)) {
                    $imageUrl = $matches[1];
                }
            }

            if ($imageUrl !== '') {
                $imageUrl = antaraNormalizeMediaUrl($imageUrl, $feedInfo);
            }

            $cleanDescription = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($description))));
            if ($cleanDescription !== '') {
                if (function_exists('mb_strimwidth')) {
                    $excerpt = trim(mb_strimwidth($cleanDescription, 0, 180, '...'));
                } else {
                    $excerpt = trim(substr($cleanDescription, 0, 177)) . '...';
                }
            } else {
                $excerpt = '';
            }

            $pubDateFormatted = $pubDateRaw;
            $pubDateIso = $pubDateRaw;
            try {
                if ($pubDateRaw !== '') {
                    $pubDate = new DateTime($pubDateRaw);
                    $pubDate->setTimezone(new DateTimeZone('Asia/Jakarta'));
                    $pubDateFormatted = $pubDate->format('d M Y H:i');
                    $pubDateIso = $pubDate->format(DateTime::ATOM);
                }
            } catch (Exception $e) {
                $pubDateFormatted = $pubDateRaw;
                $pubDateIso = $pubDateRaw;
            }

            $result['items'][] = [
                'title' => $title,
                'link' => $link,
                'pub_date_raw' => $pubDateRaw,
                'pub_date_formatted' => $pubDateFormatted,
                'pub_date_iso' => $pubDateIso,
                'excerpt' => $excerpt,
                'image' => $imageUrl,
            ];
        }

        if (empty($result['items'])) {
            $result['error'] = 'Tidak ada berita yang dapat ditampilkan.';
        }

        return $result;
    }
}
