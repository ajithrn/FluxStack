/**
 * Custom Button Styles
 * 
 * Extends the core button block with custom styles
 */
(function() {
    var __ = wp.i18n.__;
    var registerBlockStyle = wp.blocks.registerBlockStyle;
    var unregisterBlockStyle = wp.blocks.unregisterBlockStyle;

    // Unregister default styles if needed
    // unregisterBlockStyle('core/button', 'fill');
    // unregisterBlockStyle('core/button', 'outline');

    // Register custom button styles
    registerBlockStyle('core/button', {
        name: 'primary',
        label: __('Primary', 'fluxstack'),
        isDefault: false,
    });

    // You can add more styles here
    // registerBlockStyle('core/button', {
    //     name: 'secondary',
    //     label: __('Secondary', 'fluxstack'),
    //     isDefault: false,
    // });
})();
