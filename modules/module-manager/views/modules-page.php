<div class="fluxstack-app" id="fluxstack-modules">
    <div class="fluxstack-header">
        <div class="fluxstack-header__left">
            <h1 class="fluxstack-header__title">FluxStack Modules</h1>
            <span class="fluxstack-header__version">v2.0.0</span>
        </div>
        <div class="fluxstack-header__right">
            <button type="button" class="fluxstack-btn fluxstack-btn--primary" id="fluxstack-save" data-action="fluxstack_save_modules">
                <span class="fluxstack-btn__text"><?php esc_html_e('Save Changes', 'fluxstack'); ?></span>
                <span class="dashicons dashicons-saved"></span>
            </button>
        </div>
    </div>
    <div class="fluxstack-toast" id="fluxstack-toast" hidden></div>

    <nav class="fluxstack-tabs">
        <button class="fluxstack-tabs__tab is-active" data-tab="modules"><span class="dashicons dashicons-admin-plugins"></span> <?php esc_html_e('Modules', 'fluxstack'); ?></button>
        <button class="fluxstack-tabs__tab" data-tab="blocks"><span class="dashicons dashicons-block-default"></span> <?php esc_html_e('Blocks', 'fluxstack'); ?></button>
        <?php do_action('fluxstack_modules_tabs'); ?>
    </nav>

    <!-- Modules -->
    <div class="fluxstack-panel is-active" data-panel="modules">
        <?php foreach ($grouped as $category => $categoryModules) :
            $labels = ['utility' => __('Utility', 'fluxstack'), 'feature' => __('Features', 'fluxstack'), 'cpt' => __('Content Types', 'fluxstack')];
        ?>
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php echo esc_html($labels[$category] ?? ucfirst($category)); ?></h2>
            <div class="fluxstack-grid">
                <?php foreach ($categoryModules as $module) :
                    $isCore = in_array($module->id(), $coreModules);
                    $isEnabled = $module->isEnabled();
                ?>
                <div class="fluxstack-card <?php echo $isEnabled ? 'is-active' : ''; ?> <?php echo $isCore ? 'is-locked' : ''; ?>">
                    <div class="fluxstack-card__header">
                        <div class="fluxstack-card__info">
                            <h3 class="fluxstack-card__title"><?php echo esc_html($module->name()); ?></h3>
                            <?php if ($isCore) : ?><span class="fluxstack-tag fluxstack-tag--core">Core</span><?php endif; ?>
                        </div>
                        <label class="fluxstack-switch">
                            <input type="checkbox" data-module="<?php echo esc_attr($module->id()); ?>" value="1" <?php checked($isEnabled); ?> <?php disabled($isCore); ?>>
                            <span class="fluxstack-switch__track"></span>
                        </label>
                    </div>
                    <p class="fluxstack-card__desc"><?php echo esc_html($module->description()); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>

        <?php if (!empty($subPages)) : ?>
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Site Settings Pages', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Additional settings pages under Site Settings. Enable per project as needed.', 'fluxstack'); ?></p>
            <div class="fluxstack-grid">
                <?php foreach ($subPages as $sub) :
                    if (!empty($sub['core'])) continue;
                    $moduleSettings = get_option('fluxstack_modules', []);
                    $isEnabled = $moduleSettings[$sub['id']] ?? ($sub['default'] ?? false);
                ?>
                <div class="fluxstack-card <?php echo $isEnabled ? 'is-active' : ''; ?>">
                    <div class="fluxstack-card__header">
                        <div class="fluxstack-card__info">
                            <h3 class="fluxstack-card__title"><?php echo esc_html($sub['title']); ?></h3>
                            <span class="fluxstack-tag fluxstack-tag--settings">Page</span>
                        </div>
                        <label class="fluxstack-switch">
                            <input type="checkbox" data-module="<?php echo esc_attr($sub['id']); ?>" value="1" <?php checked($isEnabled); ?>>
                            <span class="fluxstack-switch__track"></span>
                        </label>
                    </div>
                    <p class="fluxstack-card__desc"><?php echo esc_html($sub['description'] ?? ''); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <!-- Blocks -->
    <div class="fluxstack-panel" data-panel="blocks">
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Theme Blocks', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Standalone blocks for the editor. CPT-related blocks are managed by their parent module.', 'fluxstack'); ?></p>
            <?php if (!empty($blocks)) : ?>
            <div class="fluxstack-grid">
                <?php foreach ($blocks as $block) : $isEnabled = $block->isEnabled(); ?>
                <div class="fluxstack-card <?php echo $isEnabled ? 'is-active' : ''; ?>">
                    <div class="fluxstack-card__header">
                        <div class="fluxstack-card__info">
                            <h3 class="fluxstack-card__title"><?php echo esc_html($block->name()); ?></h3>
                            <span class="fluxstack-tag fluxstack-tag--block">Block</span>
                        </div>
                        <label class="fluxstack-switch">
                            <input type="checkbox" data-module="<?php echo esc_attr($block->id()); ?>" value="1" <?php checked($isEnabled); ?>>
                            <span class="fluxstack-switch__track"></span>
                        </label>
                    </div>
                    <p class="fluxstack-card__desc"><?php echo esc_html($block->description()); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="fluxstack-empty"><span class="dashicons dashicons-block-default"></span><p><?php esc_html_e('No standalone blocks yet.', 'fluxstack'); ?></p></div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Module-registered panels -->
    <?php do_action('fluxstack_modules_panels'); ?>
</div>
