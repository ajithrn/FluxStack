<?php
/**
 * Theme footer — bridge for plugins that call get_footer().
 * Renders the Blade layout closing via Acorn's view system.
 */

if (function_exists('app') && app()->bound('view')) {
    echo app('view')->make('sections.footer-compat')->render();
} else {
    // Fallback
    ?>
    </div>
    <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}
