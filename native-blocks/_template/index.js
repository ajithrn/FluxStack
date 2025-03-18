/**
 * Block Name
 * 
 * This is the source file that would be compiled to build.js
 */

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { useBlockProps, InnerBlocks } = wp.blockEditor;

registerBlockType('fluxstack/block-name', {
    title: __('Block Name', 'fluxstack'),
    icon: 'block-default', // Choose from WordPress dashicons
    category: 'fluxstack',
    description: __('Block description goes here', 'fluxstack'),
    
    supports: {
        html: false,
        anchor: true,
        align: ['wide', 'full']
    },
    
    // Edit function defines the editor interface
    edit: () => {
        const blockProps = useBlockProps({
            className: 'fluxstack-block-name',
        });
        
        // Define a template for inner blocks if needed
        const TEMPLATE = [
            ['core/paragraph', { placeholder: __('Add content here...', 'fluxstack') }],
        ];
        
        return (
            <div {...blockProps}>
                <InnerBlocks
                    template={TEMPLATE}
                    templateLock={false}
                    allowedBlocks={null}
                />
            </div>
        );
    },
    
    // Save function defines the output saved to post content
    save: () => {
        const blockProps = useBlockProps.save({
            className: 'fluxstack-block-name',
        });
        
        return (
            <div {...blockProps}>
                <InnerBlocks.Content />
            </div>
        );
    }
});
