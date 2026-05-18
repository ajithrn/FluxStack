import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit({ attributes, setAttributes }) {
        const { heading, text, buttonText, buttonUrl, layout } = attributes;
        const blockProps = useBlockProps({ className: `fluxstack-cta fluxstack-cta--${layout}` });

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Layout', 'fluxstack')}>
                        <SelectControl label={__('Style', 'fluxstack')} value={layout} options={[{ label: 'Horizontal', value: 'horizontal' }, { label: 'Stacked', value: 'stacked' }]} onChange={(v) => setAttributes({ layout: v })} />
                    </PanelBody>
                    <PanelBody title={__('Button', 'fluxstack')} initialOpen={false}>
                        <TextControl label={__('Text', 'fluxstack')} value={buttonText} onChange={(v) => setAttributes({ buttonText: v })} />
                        <TextControl label={__('URL', 'fluxstack')} value={buttonUrl} onChange={(v) => setAttributes({ buttonUrl: v })} type="url" />
                    </PanelBody>
                </InspectorControls>
                <section {...blockProps}>
                    <div className="fluxstack-cta__content">
                        <RichText tagName="h2" className="fluxstack-cta__heading" value={heading} onChange={(v) => setAttributes({ heading: v })} placeholder={__('CTA Heading...', 'fluxstack')} />
                        <RichText tagName="p" className="fluxstack-cta__text" value={text} onChange={(v) => setAttributes({ text: v })} placeholder={__('Supporting text...', 'fluxstack')} />
                    </div>
                    {buttonText && <div className="fluxstack-cta__action"><span className="fluxstack-cta__button">{buttonText}</span></div>}
                </section>
            </>
        );
    },
    save() { return null; },
});
