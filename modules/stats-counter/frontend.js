// Stats Counter — animate numbers on scroll into view
(function() {
    function animateValue(el, target, duration) {
        var start = 0;
        var startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            el.textContent = Math.floor(progress * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        }
        requestAnimationFrame(step);
    }

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                var target = parseInt(el.dataset.target, 10) || 0;
                animateValue(el, target, 1500);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.fluxstack-stats__value').forEach(function(el) {
        observer.observe(el);
    });
})();
