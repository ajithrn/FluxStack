/**
 * Columns 25/75 Block (Built Version)
 */
(function() {
    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var InnerBlocks = wp.blockEditor.InnerBlocks;

    registerBlockType('fluxstack/columns-25-75', {
        title: __('Columns 25/75', 'fluxstack'),
        icon: 'columns',
        category: 'fluxstack',
        description: __('A two-column layout with 25/75 ratio and 1.5em gap', 'fluxstack'),
        
        supports: {
            html: false,
            anchor: true,
            align: ['wide', 'full']
        },
        
        edit: function() {
            var blockProps = useBlockProps({
                className: 'fluxstack-columns-25-75',
            });
            
            var TEMPLATE = [
                ['core/column', { width: '25%' }, [
                    ['core/paragraph', { placeholder: __('Add content to the left column (25%)', 'fluxstack') }],
                ]],
                ['core/column', { width: '75%' }, [
                    ['core/paragraph', { placeholder: __('Add content to the right column (75%)', 'fluxstack') }],
                ]],
            ];
            
            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(InnerBlocks, {
                    template: TEMPLATE,
                    templateLock: false,
                    allowedBlocks: null
                })
            );
        },
        
        save: function() {
            var blockProps = useBlockProps.save({
                className: 'fluxstack-columns-25-75',
            });
            
            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(InnerBlocks.Content)
            );
        }
    });
})();
