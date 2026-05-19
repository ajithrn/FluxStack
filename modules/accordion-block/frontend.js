// Accordion — close other items when one opens (optional exclusive mode)
(function() {
    document.querySelectorAll('.fluxstack-accordion').forEach(function(accordion) {
        accordion.querySelectorAll('details').forEach(function(detail) {
            detail.addEventListener('toggle', function() {
                if (this.open) {
                    accordion.querySelectorAll('details[open]').forEach(function(other) {
                        if (other !== detail) other.removeAttribute('open');
                    });
                }
            });
        });
    });
})();
