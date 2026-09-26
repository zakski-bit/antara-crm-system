<?php
$galleryFeedUrl = 'https://www.antaranews.com/rss/photo.xml';
$maxGalleryItems = 6;
$galleryItems = [];

libxml_use_internal_errors(true);
$galleryContext = stream_context_create([
    'http' => [
        'timeout' => 5,
        'user_agent' => 'ANTARA Photo Widget/1.0 (+https://www.antaranews.com)',
    ],
]);
$galleryFeedContent = @file_get_contents($galleryFeedUrl, false, $galleryContext);
if ($galleryFeedContent !== false) {
    $galleryRss = @simplexml_load_string($galleryFeedContent, 'SimpleXMLElement', LIBXML_NOCDATA);
    if ($galleryRss !== false && isset($galleryRss->channel->item)) {
        foreach ($galleryRss->channel->item as $item) {
            if (count($galleryItems) >= $maxGalleryItems) {
                break;
            }

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

            $galleryItems[] = [
                'title' => trim((string) $item->title),
                'link' => (string) $item->link,
                'image' => $image ?: 'images/resource/project-thumb-1.jpg',
            ];
        }
    }
}

if (empty($galleryItems)) {
    $galleryItems = [
        [
            'title' => 'Sorotan foto ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-1.jpg',
        ],
        [
            'title' => 'Galeri redaksi ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-2.jpg',
        ],
        [
            'title' => 'Dokumentasi ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-3.jpg',
        ],
        [
            'title' => 'Momen pilihan ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-4.jpg',
        ],
        [
            'title' => 'Koleksi foto ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-5.jpg',
        ],
        [
            'title' => 'Feature foto ANTARA',
            'link' => '#',
            'image' => 'images/resource/project-thumb-6.jpg',
        ],
    ];
}

libxml_clear_errors();
?>
<footer class="main-footer">
  <div class="bg-image"  style="background-image: url(./images/icons/shape-5.png)"></div>
  <div class="widgets-section">
    <div class="auto-container">
      <div class="row">
        <div class="footer-column col-lg-3 col-sm-6">
          <div class="footer-widget about-widget">
            <h5 class="about-title">About us</h5>
            <div class="text">Desires to obtain pain of itself, <br>because it is pain, but occasionally circumstances.</div>
            <ul class="social-icon-two">
              <li><a href="#"><i class="fa fa-x"></i></a></li>
              <li><a href="#"><i class="fab fa-instagram"></i></a></li>
              <li><a href="#"><i class="fab fa-facebook"></i></a></li>
              <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="footer-column col-lg-3 col-sm-6">
          <div class="footer-widget">
            <h5 class="widget-title">Explore</h5>
            <ul class="user-links">
              <li><a href="#">About Company</a></li>
              <li><a href="#">Meet the Team</a></li>
              <li><a href="#">News & Media</a></li>
              <li><a href="#">Our Projects</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-column col-lg-3 col-sm-6">
          <div class="footer-widget contact-widget">
            <h5 class="widget-title">Contact</h5>
            <div class="widget-content">
              <div class="text">Jalan Antara Kav 53-61, Pasar Baru, Jakarta Pusat 10710</div>
              <ul class="contact-info">
                <li><i class="fa fa-envelope"></i><a href="mailto:customer.care@antara.id">customer.care@antara.id</a><br /></li>
                <li><i class="fas fa-phone"></i><a href="tel:+62213842591">021-3842591 / 021-22395579 / 08119569694</a><br /></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="footer-column col-lg-3 col-sm-6">
          <div class="footer-widget gallery-widget">
            <h5 class="widget-title">Gallery</h5>
            <div class="widget-content">
              <div class="outer clearfix">
                <?php foreach ($galleryItems as $gallery) : ?>
                  <figure class="image">
                    <a href="<?php echo htmlspecialchars($gallery['link'], ENT_QUOTES, 'UTF-8'); ?>">
                      <img src="<?php echo htmlspecialchars($gallery['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($gallery['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    </a>
                  </figure>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="auto-container">
      <div class="inner-container">
        <div class="copyright-text">&copy; 2025 <a href="index-9.php">ANTARA</a> | All Rights Reserved | <a href="index-9.php">NEWS</a>
        </div>
      </div>
    </div>
  </div>
</footer>

</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>

<?php require_once('footer-js.php'); ?>
