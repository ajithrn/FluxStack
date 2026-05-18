(function($) {
    'use strict';

    // Tab switching
    $('.fluxstack-tabs__tab').on('click', function() {
        var tab = $(this).data('tab');
        $('.fluxstack-tabs__tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.fluxstack-panel').removeClass('is-active');
        $('.fluxstack-panel[data-panel="' + tab + '"]').addClass('is-active');
    });

    // Toggle card active state
    $(document).on('change', '.fluxstack-switch input', function() {
        $(this).closest('.fluxstack-card').toggleClass('is-active', $(this).is(':checked'));
    });

    // File upload via WP Media Library
    $(document).on('click', '.js-upload-btn', function(e) {
        e.preventDefault();
        var container = $(this).closest('.fluxstack-upload');
        var input = container.find('input[type="hidden"]');
        var preview = container.find('.fluxstack-upload__preview');

        var frame = wp.media({ title: 'Select File', multiple: false, library: { type: 'image' } });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            input.val(attachment.url);
            preview.html('<img src="' + attachment.url + '" alt="">');
            var actions = container.find('.fluxstack-upload__actions');
            if (!actions.find('.js-remove-btn').length) {
                actions.append('<button type="button" class="fluxstack-upload__btn fluxstack-upload__btn--remove js-remove-btn">Remove</button>');
            }
        });
        frame.open();
    });

    // Remove uploaded file
    $(document).on('click', '.js-remove-btn', function(e) {
        e.preventDefault();
        var container = $(this).closest('.fluxstack-upload');
        container.find('input[type="hidden"]').val('');
        container.find('.fluxstack-upload__preview').html('<span class="dashicons dashicons-format-image"></span>');
        $(this).remove();
    });

    // Save button
    $('#fluxstack-save').on('click', function() {
        var btn = $(this);
        var action = btn.data('action');
        var btnText = btn.find('.fluxstack-btn__text');
        var originalText = btnText.text();
        var postData = { action: action, nonce: fluxstackAdmin.nonce };

        // Clear previous validation
        $('.fluxstack-field').removeClass('is-invalid');

        // Validate email fields
        var hasError = false;
        $('input[type="email"]').each(function() {
            var val = $(this).val().trim();
            if (val && !isValidEmail(val)) {
                $(this).closest('.fluxstack-field').addClass('is-invalid');
                hasError = true;
            }
        });

        if (hasError) {
            showToast('Please fix validation errors.', true);
            return;
        }

        if (action === 'fluxstack_save_modules') {
            // Collect module toggles
            var modules = {};
            $('input[data-module]').each(function() {
                modules[$(this).data('module')] = $(this).is(':checked') ? '1' : '';
            });
            postData.modules = modules;

            // Also collect any site settings fields on this page
            var settings = collectSettings();
            if (Object.keys(settings).length > 0) {
                postData.settings = settings;
            }

        } else if (action === 'fluxstack_save_site_settings') {
            postData.settings = collectSettings();
        }

        btn.addClass('is-saving');
        btnText.text(fluxstackAdmin.strings.saving);

        $.post(fluxstackAdmin.ajaxUrl, postData)
            .done(function(r) { showToast(r.success ? fluxstackAdmin.strings.saved : fluxstackAdmin.strings.error, !r.success); })
            .fail(function() { showToast(fluxstackAdmin.strings.error, true); })
            .always(function() { btn.removeClass('is-saving'); btnText.text(originalText); });
    });

    // Collect all site settings fields
    function collectSettings() {
        var settings = {};
        $('[name^="fluxstack_site_settings"]').each(function() {
            var match = $(this).attr('name').match(/\[([^\]]+)\]/);
            if (!match) return;
            var key = match[1];

            if ($(this).attr('type') === 'checkbox') {
                settings[key] = $(this).is(':checked') ? '1' : '';
            } else if ($(this).data('url-field') !== undefined) {
                // URL prefix fields: prepend https:// if value exists
                var val = $(this).val().trim();
                settings[key] = val ? 'https://' + val.replace(/^https?:\/\//, '') : '';
            } else {
                settings[key] = $(this).val();
            }
        });
        return settings;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showToast(msg, isError) {
        var t = $('#fluxstack-toast');
        t.text(msg).toggleClass('is-error', !!isError).removeAttr('hidden');
        setTimeout(function() { t.attr('hidden', true); }, 3000);
    }
})(jQuery);
