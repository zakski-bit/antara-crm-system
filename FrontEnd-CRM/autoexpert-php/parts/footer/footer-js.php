<script src="js/jquery.js"></script> 
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.fancybox.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery.countdown.js"></script>
<script src="js/bxslider.js"></script>
<script src="js/mixitup.js"></script>
<script src="js/wow.js"></script>
<script src="js/appear.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/swiper.min.js"></script>
<script src="js/owl.js"></script>
<script src="js/script.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/jquery.form.min.js"></script>
<script src="js/contact-form-script.js"></script>
<!-- form submit -->
<script src="js/jquery.validate.min.js"></script>
<script src="js/jquery.form.min.js"></script>
<script>
(function() {
    var bgIframes = Array.prototype.slice.call(document.querySelectorAll('iframe[data-yt-bg]'));
    if (!bgIframes.length) {
        return;
    }

    var players = {};
    bgIframes.forEach(function(iframe, index) {
        if (!iframe.id) {
            iframe.id = 'bg-yt-' + index;
        }
    });

    function initBgPlayers() {
        bgIframes.forEach(function(iframe) {
            if (iframe.dataset.ytPlayerBound || !window.YT || !window.YT.Player) {
                return;
            }
            iframe.dataset.ytPlayerBound = 'true';
            players[iframe.id] = new YT.Player(iframe.id, {
                events: {
                    onReady: function(event) {
                        try {
                            event.target.mute();
                            event.target.playVideo();
                        } catch (err) {}
                    },
                    onStateChange: function(event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            event.target.playVideo();
                        }
                        if (event.data === YT.PlayerState.UNSTARTED) {
                            event.target.mute();
                        }
                    }
                }
            });
        });
    }

    var previousCallback = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = function() {
        if (typeof previousCallback === 'function') {
            previousCallback();
        }
        initBgPlayers();
    };

    if (window.YT && window.YT.Player) {
        initBgPlayers();
    } else if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
        var tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
    }
})();

const scrollToTopLinks = document.querySelectorAll('#scroll-to-top-link');
if (scrollToTopLinks.length) {
    const isHomePage = (() => {
        const path = (window.location.pathname || '').toLowerCase();
        return /index-9\.php$/.test(path) || /index\.php$/.test(path) || /autoexpert-php\/?$/.test(path);
    })();

    scrollToTopLinks.forEach((link) => {
        link.style.cursor = 'pointer';
        link.addEventListener('click', (event) => {
            if (!isHomePage) {
                // Let the link navigate normally when we are not on the home page.
                return;
            }
            event.preventDefault();
            if (window.jQuery) {
                window.jQuery('html, body').stop().animate({ scrollTop: 0 }, 800);
            } else {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });
}
</script>
</body>
</html>
