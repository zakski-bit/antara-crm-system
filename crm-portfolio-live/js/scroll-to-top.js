document.addEventListener('DOMContentLoaded', function() {
    var scrollToTopLinks = document.querySelectorAll('#scroll-to-top-link');

    if (!scrollToTopLinks.length) {
        return;
    }

    var isHomePage = (function() {
        var path = (window.location.pathname || '').toLowerCase();
        return /index-9\.php$/.test(path) || /index\.php$/.test(path) || /autoexpert-php\/?$/.test(path);
    })();

    scrollToTopLinks.forEach(function(link) {
        link.style.cursor = 'pointer';
        link.addEventListener('click', function(e) {
            if (!isHomePage) {
                // Allow normal navigation to home when we're on other pages.
                return;
            }
            e.preventDefault();
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
});
