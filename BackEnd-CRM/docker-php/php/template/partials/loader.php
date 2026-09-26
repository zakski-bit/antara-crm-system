<!-- Preloader (matching Frontend CRM) -->
<style>
.preloader {
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 2147483647; /* Max z-index to ensure it's on top of everything */
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.92) 0%, rgba(226, 233, 240, 0.95) 60%, rgba(196, 206, 220, 1) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none; /* By default allow clicks to pass through */
  opacity: 1;
  transition: opacity 0.5s ease, visibility 0.5s;
}

/* Only block clicks when active */
.preloader.active {
  pointer-events: all;
  opacity: 1;
  visibility: visible;
}

.preloader .loader {
  position: relative;
  text-align: center;
}
.preloader:before {
  display: none;
}
.preloader .spinner {
  position: relative;
  width: 90px;
  height: 90px;
  margin: 0 auto 24px auto;
}
.preloader .spinner span {
  position: absolute;
  inset: 0;
  border: 3px solid transparent;
  border-top-color: #d70006;
  border-radius: 50%;
  animation: preloader-rotate 1.2s linear infinite;
}
.preloader .spinner span:nth-child(2) {
  border: 3px solid transparent;
  border-left-color: rgba(215, 0, 6, 0.5);
  inset: 10px;
  animation-duration: 1.5s;
}
.preloader .spinner span:nth-child(3) {
  border: 3px solid transparent;
  border-right-color: rgba(36, 87, 214, 0.45);
  inset: 20px;
  animation-duration: 1.8s;
}
.preloader .spinner span:nth-child(4) {
  border: 3px solid transparent;
  border-bottom-color: rgba(255, 255, 255, 0.6);
  inset: 30px;
  animation-duration: 2.1s;
}
.preloader .loader-logo {
  display: flex;
  justify-content: center;
  margin-bottom: 18px;
  animation: preloader-fade 2s ease-in-out infinite;
}
.preloader .loader-logo__img {
  width: 200px;
  max-width: 70vw;
  height: auto;
}
.preloader .loader-text {
  font-weight: 600;
  font-size: 13px;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: rgba(25, 40, 65, 0.8);
  animation: preloader-text 1.6s ease-in-out infinite;
}
@keyframes preloader-rotate {
  0%   { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
@keyframes preloader-fade {
  0%, 100% { opacity: 1; }
  50%      { opacity: 0.7; }
}
@keyframes preloader-text {
  0%, 100% { letter-spacing: 0.22em; }
  50%      { letter-spacing: 0.32em; }
}
</style>

<!-- Add active class by default to block interactions initially -->
<div class="preloader active">
    <div class="loader">
        <div class="spinner">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="loader-logo">
            <img src="assets/img/logo-antara.png" alt="Antara Logo" class="loader-logo__img">
        </div>
        <div class="loader-text"><span class="loader-percentage">0%</span></div>
    </div>
</div>

<script>
(function() {
    var preloaderInterval = null;
    var preloaderValue = 0;
    var display = document.querySelector('.preloader .loader-percentage');
    var minDisplayTime = 800; // Minimum display time in ms
    var loadTime = Date.now();

    function updatePercentage(value) {
        if (display) display.textContent = value + '%';
    }

    function tick() {
        if (preloaderValue < 99) {
            preloaderValue += 1;
            updatePercentage(preloaderValue);
            var delay = preloaderValue < 60 ? 30 : (preloaderValue < 85 ? 20 : 10);
            preloaderInterval = setTimeout(tick, delay);
        } else {
            preloaderInterval = null;
        }
    }

    // Start counting immediately
    preloaderInterval = setTimeout(tick, 10);

    // Complete and fade out when page is fully loaded
    window.addEventListener('load', function() {
        var elapsedTime = Date.now() - loadTime;
        var remainingTime = Math.max(0, minDisplayTime - elapsedTime);

        setTimeout(function() {
            if (preloaderInterval) {
                clearTimeout(preloaderInterval);
                preloaderInterval = null;
            }
            preloaderValue = 100;
            updatePercentage(preloaderValue);
            
            var el = document.querySelector('.preloader');
            if (el) {
                // Short delay to show 100% before starting fade
                setTimeout(function() {
                    // Start fade out
                    el.style.opacity = '0';
                    
                    // After fade transition (0.5s), remove active class and hide
                    setTimeout(function() {
                        el.classList.remove('active'); // This disables pointer events via CSS
                        el.style.display = 'none'; // Completely remove from layout flow
                    }, 500);
                }, 200);
            }
        }, remainingTime);
    });
})();
</script>