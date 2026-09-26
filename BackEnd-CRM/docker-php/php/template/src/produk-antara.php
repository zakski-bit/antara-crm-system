<?php
$_SERVER['PHP_SELF'] = 'produk-antara.php';

$rootPath = dirname(__DIR__, 2);
$composerAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function (string $class) use ($rootPath): void {
        $prefix = 'App\\';
        $baseDir = $rootPath . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

\App\Core\Config::loadFromDirectory($rootPath . '/app/Config');

$productDefinitions = [
    [
        'id' => 'afiliasi-antara',
        'name' => 'Program Afiliasi ANTARA',
        'segment' => 'Kemitraan Konten',
        'summary' => 'Sindikasi berita resmi ANTARA lengkap dengan dukungan editorial untuk portal dan platform digital mitra.',
        'logo' => 'assets/img/product/logo-afiliasi-antara.png',
        'alt_logos' => [],
        'highlights' => [
            'Kurasi berita prioritas nasional, daerah, dan tematik secara real-time.',
            'Widget siap tanam untuk website, aplikasi, dan signage digital.',
            'Tim editor khusus yang membantu penyesuaian tone-of-voice mitra.',
        ],
        'details' => 'Program Afiliasi ANTARA memberikan akses langsung ke jaringan jurnalis nasional kami sehingga mitra dapat memperkaya kanal berita mereka dengan konten yang kredibel dan terkini. Paket kemitraan mencakup integrasi API, dukungan onboarding, hingga opsi monetisasi iklan bersama.',
    ],
    [
        'id' => 'ahc',
        'name' => 'ANTARA Health Communications (AHC)',
        'segment' => 'Komunikasi Kesehatan',
        'summary' => 'Repositori konten kesehatan berbasis jurnalisme data untuk mendukung kampanye edukasi institusi kesehatan dan CSR.',
        'logo' => 'assets/img/product/logo-ahc.png',
        'alt_logos' => [],
        'highlights' => [
            'Paket artikel, infografik, dan video literasi kesehatan.',
            'Editorial plan bulanan yang disusun bersama tim komunikasi klien.',
            'Distribusi multi-kanal ke portal ANTARA dan jaringan afiliasi daerah.',
        ],
        'details' => 'AHC memadukan riset kesehatan publik dengan gaya bercerita khas ANTARA sehingga pesan edukasi lebih mudah diterima masyarakat. Solusi ini ideal untuk kementerian/lembaga, rumah sakit, hingga korporasi yang menjalankan program tanggung jawab sosial di bidang kesehatan.',
    ],
    [
        'id' => 'antara-etp',
        'name' => 'ANTARA Event Tracking Platform (ETP)',
        'segment' => 'Peliputan & Event',
        'summary' => 'Platform pemantauan event dan konferensi pers yang mengintegrasikan agenda, liputan, dan distribusi materi publikasi.',
        'logo' => 'assets/img/product/logo-antara-etp.png',
        'alt_logos' => [
            'assets/img/product/logo-antara-etp-ok.png',
        ],
        'highlights' => [
            'Dashboard agenda nasional dan korporasi untuk newsroom dan mitra.',
            'Manajemen kredensial media dengan notifikasi otomatis.',
            'Distribusi sertifikat kehadiran dan materi liputan secara digital.',
        ],
        'details' => 'ANTARA ETP membantu tim PR dan penyelenggara event mengelola proses akreditasi media, publikasi jadwal, hingga distribusi rilis dan materi visual pasca acara. Integrasi dengan ruang redaksi ANTARA memastikan momentum pemberitaan tetap terjaga.',
    ],
    [
        'id' => 'antara-foto',
        'name' => 'ANTARA Foto',
        'segment' => 'Konten Visual',
        'summary' => 'Layanan foto jurnalistik dan arsip visual Indonesia dengan lisensi yang fleksibel untuk media dan perusahaan.',
        'logo' => 'assets/img/product/logo-antara-foto.png',
        'alt_logos' => [],
        'highlights' => [
            'Lebih dari satu juta foto dengan metadata lengkap dan kontinu diperbarui.',
            'Pilihan lisensi editorial, komersial, dan korporasi.',
            'Tim fotografer nasional siap membantu permintaan pemotretan khusus.',
        ],
        'details' => 'ANTARA Foto menyajikan dokumentasi bersejarah dan liputan terkini dalam resolusi tinggi. Sistem penelusuran memudahkan kurator konten menemukan materi sesuai kata kunci, lokasi, maupun tema kampanye visual klien.',
    ],
    [
        'id' => 'antara-news',
        'name' => 'ANTARA News',
        'segment' => 'Portal Berita',
        'summary' => 'Portal berita resmi Republik Indonesia dengan update 24/7 dalam bahasa Indonesia dan Inggris.',
        'logo' => 'assets/img/product/logo-antara-news.png',
        'alt_logos' => [],
        'highlights' => [
            'Jaringan koresponden di 34 provinsi dan 13 biro luar negeri.',
            'Ragam kanal tematik mulai dari ekonomi, olahraga, hingga kreatif.',
            'Integrasi API untuk kebutuhan embed headline dan RSS khusus.',
        ],
        'details' => 'Sebagai kantor berita nasional, ANTARA News memastikan akurasi, kecepatan, dan kedalaman setiap publikasi. Mitra dapat memanfaatkan penempatan advertorial, branded content, hingga takeover halaman khusus kampanye.',
    ],
    [
        'id' => 'antara-tv',
        'name' => 'ANTARA TV',
        'segment' => 'Video News',
        'summary' => 'Produksi video berita dan dokumenter singkat yang siap tayang untuk televisi, OTT, dan kanal digital.',
        'logo' => 'assets/img/product/logo-antara-tv.png',
        'alt_logos' => [],
        'highlights' => [
            'Feed video harian dalam format landscape dan vertical.',
            'Studio dan mobile crew untuk kebutuhan live report maupun feature.',
            'Integrasi closed-caption dan multi bahasa sesuai permintaan.',
        ],
        'details' => 'ANTARA TV menghadirkan paket video breaking news, liputan mendalam, hingga program spesial untuk kebutuhan brand storytelling. Sistem distribusi mendukung CDN dan delivery ke newsroom mitra.',
    ],
    [
        'id' => 'bloomberg',
        'name' => 'Bloomberg Partnership',
        'segment' => 'Jaringan Global',
        'summary' => 'Akses konten bisnis dan pasar global melalui kolaborasi ANTARA dengan Bloomberg.',
        'logo' => 'assets/img/product/logo-bloomberg.png',
        'alt_logos' => [],
        'highlights' => [
            'Sindikasi berita pasar modal dan ekonomi internasional.',
            'Penyesuaian konten bahasa Indonesia dengan jurnalis ANTARA.',
            'Fasilitas wawancara eksklusif dan riset pasar global.',
        ],
        'details' => 'Kemitraan dengan Bloomberg memperkuat cakupan ekonomi makro ANTARA dan membuka peluang bundling konten yang relevan bagi perbankan, lembaga keuangan, maupun media bisnis nasional.',
    ],
    [
        'id' => 'branda',
        'name' => 'BRANDA by ANTARA',
        'segment' => 'Konten Branded',
        'summary' => 'Studio kreatif untuk merancang kampanye brand journalism lintas kanal.',
        'logo' => 'assets/img/product/logo-branda.png',
        'alt_logos' => [],
        'highlights' => [
            'Strategi storytelling berbasis data audiens ANTARA.',
            'Produksi multimedia (artikel, video, audio, interaktif).',
            'Distribusi terukur dengan laporan performa komprehensif.',
        ],
        'details' => 'BRANDA membantu perusahaan mengemas pesan komunikasi menjadi cerita yang relevan. Tim kreatif menangani ide, produksi, hingga aktivasi distribusi pada kanal ANTARA dan jaringan mitra.',
    ],
    [
        'id' => 'etp-core',
        'name' => 'Event & Press (ETP) Core',
        'segment' => 'Platform Digital',
        'summary' => 'Mesin inti untuk manajemen agenda, registrasi, dan publikasi event yang dapat dikustomisasi bagi instansi.',
        'logo' => 'assets/img/product/logo-etp.png',
        'alt_logos' => [],
        'highlights' => [
            'Formulir registrasi adaptif dengan verifikasi email dan SMS.',
            'Panel admin multi peran untuk penyelenggara dan humas.',
            'Integrasi laporan kehadiran serta export data ke CRM.',
        ],
        'details' => 'ETP Core menjadi fondasi berbagai implementasi event management di lingkungan ANTARA. Solusi ini dapat di-branding sesuai instansi dan mendukung kebutuhan hybrid/virtual event.',
    ],
    [
        'id' => 'hcm-ads-media',
        'name' => 'HCM Ads Media',
        'segment' => 'Monetisasi Iklan',
        'summary' => 'Pengelolaan inventori iklan digital ANTARA beserta jaringan premium partner.',
        'logo' => 'assets/img/product/logo-hcm-ads-media.png',
        'alt_logos' => [],
        'highlights' => [
            'Pilihan paket CPM, CPT, dan brand immersion.',
            'Optimasi kreatif dan A/B testing lintas platform.',
            'Pelaporan performa real-time dengan dashboard klien.',
        ],
        'details' => 'HCM Ads Media memfasilitasi brand untuk menayangkan kampanye di ekosistem media ANTARA. Tim account akan membantu sinkronisasi materi kreatif serta compliance terhadap regulasi periklanan.',
    ],
    [
        'id' => 'imcs',
        'name' => 'Indonesia Market & Corporate Service (IMCS)',
        'segment' => 'Data Keuangan',
        'summary' => 'Feed data dan pemberitaan korporasi yang dirancang untuk perbankan, regulator, dan emiten.',
        'logo' => 'assets/img/product/logo-imcs.png',
        'alt_logos' => [],
        'highlights' => [
            'Ringkasan aksi korporasi dan rilis regulator harian.',
            'Analitik pasar modal dan komoditas terintegrasi.',
            'Notifikasi kilat melalui email, SMS, dan dashboard web.',
        ],
        'details' => 'IMCS menyajikan insight pasar Indonesia dengan dukungan riset internal ANTARA. Layanan ini kompatibel dengan kebutuhan portal keuangan, internal dealing room, maupun portal investor relations.',
    ],
    [
        'id' => 'it-hardware-rental',
        'name' => 'ANTARA IT Hardware Rental',
        'segment' => 'Layanan Teknologi',
        'summary' => 'Sewa perangkat IT siap pakai untuk newsroom, event, dan kebutuhan produksi konten.',
        'logo' => 'assets/img/product/LOGO-IT-HARDWARE-RENTAL.png',
        'alt_logos' => [],
        'highlights' => [
            'Pilihan perangkat komputer, kamera, hingga jaringan bergerak.',
            'Maintenance on-site dan helpdesk 24/7 selama masa sewa.',
            'Opsional bundling dengan layanan produksi konten ANTARA.',
        ],
        'details' => 'Unit rental ANTARA menyiapkan perangkat tervalidasi untuk operasional newsroom maupun event skala besar. Semua perangkat dilengkapi konfigurasi keamanan standar dan opsi asuransi aset.',
    ],
    [
        'id' => 'layanan-analisa-informasi',
        'name' => 'Layanan Analisa & Informasi',
        'segment' => 'Insight & Riset',
        'summary' => 'Analisis isu dan intelijen media untuk mendukung pengambilan keputusan strategis.',
        'logo' => 'assets/img/product/logo-layanan-analisa-dan-informasi.png',
        'alt_logos' => [
            'assets/img/product/logo-layanan-analisa-dan-informasi-ok.png',
        ],
        'highlights' => [
            'Monitoring multi-platform (media massa, sosial, dan forum).',
            'Dashboard sentiment dan heatmap isu prioritas.',
            'Rekomendasi strategi komunikasi dalam bentuk executive summary.',
        ],
        'details' => 'Tim analis ANTARA menyusun laporan berkala mengenai isu yang berdampak pada instansi atau korporasi. Metodologi mencakup analisis kualitatif dan kuantitatif serta wawancara pakar bila diperlukan.',
    ],
    [
        'id' => 'layanan-data',
        'name' => 'Layanan Data ANTARA',
        'segment' => 'Data & API',
        'summary' => 'Akses API dan dataset tematik (demografi, ekonomi kreatif, UMKM, dan lainnya) dengan pembaruan reguler.',
        'logo' => 'assets/img/product/logo-layanan-data.png',
        'alt_logos' => [],
        'highlights' => [
            'Format JSON, CSV, dan feed realtime sesuai kebutuhan integrasi.',
            'Metadata terstandarisasi untuk mempermudah analitik.',
            'Pilihan paket langganan berdasarkan volume pemanggilan API.',
        ],
        'details' => 'Layanan Data menghadirkan kumpulan dataset yang dikurasi dari peliputan lapangan, sumber resmi, dan kolaborasi lembaga pemerintah. Cocok untuk dashboard internal, penelitian, maupun aplikasi publik.',
    ],
    [
        'id' => 'lpa',
        'name' => 'Layanan PR ANTARA (LPA)',
        'segment' => 'Public Relations',
        'summary' => 'Distribusi siaran pers nasional dan layanan media handling terpadu.',
        'logo' => 'assets/img/product/logo-lpa.png',
        'alt_logos' => [],
        'highlights' => [
            'Distribusi ke newsroom ANTARA dan >250 media partner.',
            'Pilihan format press release, advertorial, dan multimedia kit.',
            'Pelaporan publikasi lengkap dengan data reach dan engagement.',
        ],
        'details' => 'LPA memastikan pesan korporasi menjangkau audiens yang tepat lewat jaringan media ANTARA. Tim kami membantu copywriting, penerjemahan, hingga koordinasi doorstop dan konferensi pers.',
    ],
    [
        'id' => 'mice',
        'name' => 'ANTARA MICE Solutions',
        'segment' => 'Event & Aktivasi',
        'summary' => 'Pendampingan penyelenggaraan meeting, incentive, convention, dan exhibition dengan dukungan media.',
        'logo' => 'assets/img/product/logo-mice.png',
        'alt_logos' => [],
        'highlights' => [
            'Perencanaan agenda dan kurasi narasumber strategis.',
            'Produksi materi publikasi (press kit, highlight video, photobooth).',
            'Liputan multi-kanal sebelum, saat, dan pasca acara.',
        ],
        'details' => 'Divisi MICE ANTARA menggabungkan keahlian event management dan kekuatan newsroom sehingga pengalaman peserta terjaga sekaligus memberikan nilai pemberitaan yang tinggi.',
    ],
    [
        'id' => 'otp',
        'name' => 'One-Time Publication (OTP)',
        'segment' => 'Distribusi Pesan Singkat',
        'summary' => 'Platform push notification dan SMS blast untuk pengiriman kabar penting secara simultan.',
        'logo' => 'assets/img/product/logo-otp-new.png',
        'alt_logos' => [],
        'highlights' => [
            'Segmentasi audiens berdasarkan lokasi dan minat.',
            'Dashboard monitoring deliverability dan open rate.',
            'Integrasi webhook dengan sistem internal klien.',
        ],
        'details' => 'OTP cocok untuk penyebaran pengumuman mendesak, klarifikasi informasi, maupun kampanye awareness yang membutuhkan kecepatan dan keterukuran.',
    ],
    [
        'id' => 'reuters',
        'name' => 'Reuters Syndication',
        'segment' => 'Jaringan Global',
        'summary' => 'Kerja sama konten dengan Reuters untuk memperluas cakupan berita internasional.',
        'logo' => 'assets/img/product/logo-reuters.png',
        'alt_logos' => [],
        'highlights' => [
            'Akses foto, video, dan teks internasional dengan opsi lisensi fleksibel.',
            'Kurasi konten internasional dalam bahasa Indonesia oleh editor ANTARA.',
            'Paket bundling untuk media ekonomi dan umum.',
        ],
        'details' => 'Sindikasi Reuters melengkapi konten global ANTARA sehingga mitra mendapatkan kombinasi berita lokal dan internasional dalam satu kontrak layanan.',
    ],
    [
        'id' => 'sewa-perangkat-keras',
        'name' => 'Sewa Perangkat Keras ANTARA',
        'segment' => 'Layanan Teknologi',
        'summary' => 'Penyewaan perangkat pendukung peliputan lapangan dan newsroom dalam jangka pendek.',
        'logo' => 'assets/img/product/logo-sewa-perangkat-keras.png',
        'alt_logos' => [
            'assets/img/product/logo-sewa-perangkat-keras-ok.png',
        ],
        'highlights' => [
            'Laptop, kamera, perangkat audio, hingga kit live streaming.',
            'Logistik pengiriman ke kota-kota besar di Indonesia.',
            'Paket bundling dengan teknisi on-site bila dibutuhkan.',
        ],
        'details' => 'Layanan ini membantu tim produksi dan redaksi ketika membutuhkan perangkat tambahan secara cepat, termasuk untuk program liputan khusus dan roadshow.',
    ],
];

ob_start();
?>

    <div class="page-wrapper">
        <div class="content">
            <div class="page-header d-md-flex d-block align-items-center justify-content-between">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Produk LKBN ANTARA</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">Komersial</li>
                            <li class="breadcrumb-item active" aria-current="page">Produk</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex flex-wrap my-xl-auto">
                    <a href="payments.php" class="btn btn-primary me-2 mb-2">
                        <i class="ti ti-credit-card me-2"></i>Menu Pembayaran
                    </a>
                    <a href="mailto:commercial@antara.co.id" class="btn btn-outline-secondary mb-2">
                        <i class="ti ti-headset me-2"></i>Hubungi Account Manager
                    </a>
                </div>
            </div>

            <div class="alert alert-light border d-flex align-items-start mb-4" role="alert">
                <i class="ti ti-info-circle me-3 fs-20 text-primary"></i>
                <div>
                    <h6 class="mb-1">Ringkasan Internal Produk</h6>
                    <p class="mb-0">
                        Uraian singkat berikut membantu tim newsroom, pemasaran, dan komersial memahami proposisi nilai setiap produk ANTARA. Untuk spesifikasi teknis resmi,
                        silakan koordinasi dengan unit bisnis terkait melalui CRM atau menu pembayaran.
                    </p>
                </div>
            </div>

            <div class="row">
                <?php foreach ($productDefinitions as $product): ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card border h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light p-3 rounded me-3 d-flex align-items-center justify-content-center" style="min-width: 96px; min-height: 96px;">
                                    <img src="<?php echo htmlspecialchars($product['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-height: 64px; width: auto;">
                                </div>
                                <div>
                                    <span class="badge bg-primary-transparent text-uppercase fs-11 mb-1">
                                        <?php echo htmlspecialchars($product['segment'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <h5 class="mb-0"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                </div>
                            </div>
                            <p class="text-muted mb-3 flex-grow-1">
                                <?php echo htmlspecialchars($product['summary'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <?php if (!empty($product['highlights'])): ?>
                            <ul class="list-unstyled mb-4">
                                <?php foreach ($product['highlights'] as $highlight): ?>
                                <li class="d-flex mb-2">
                                    <i class="ti ti-checks text-success me-2 mt-1"></i>
                                    <span><?php echo htmlspecialchars($highlight, ENT_QUOTES, 'UTF-8'); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top mt-auto">
                                <a href="payments.php" class="btn btn-sm btn-primary">
                                    <i class="ti ti-shopping-cart me-2"></i>Ajukan Pembelian
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="ti ti-file-description me-2"></i>Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>
    </div>

    <?php foreach ($productDefinitions as $product): ?>
    <div class="modal fade" id="modal-<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-2">
                            <img src="<?php echo htmlspecialchars($product['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-height: 44px; width: auto;">
                        </div>
                        <div>
                            <h5 class="modal-title mb-0"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <small class="text-muted text-uppercase"><?php echo htmlspecialchars($product['segment'], ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        <?php echo htmlspecialchars($product['details'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <?php if (!empty($product['highlights'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Nilai Utama</h6>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($product['highlights'] as $highlight): ?>
                            <li class="d-flex mb-2">
                                <i class="ti ti-star text-warning me-2 mt-1"></i>
                                <span><?php echo htmlspecialchars($highlight, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['alt_logos'])): ?>
                    <div>
                        <h6 class="fw-semibold mb-2">Variasi Logo Terkait</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($product['alt_logos'] as $altLogo): ?>
                            <div class="border rounded p-2 bg-light">
                                <img src="<?php echo htmlspecialchars($altLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="Alternatif logo <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-height: 40px; width: auto;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer justify-content-between">
                    <a href="payments.php" class="btn btn-primary">
                        <i class="ti ti-wallet me-2"></i>Lanjut ke Pembayaran
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php';
