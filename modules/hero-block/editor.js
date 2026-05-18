import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, RangeControl, SelectControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit({ attributes, setAttributes }) {
        const { heading, subheading, backgroundImage, overlayOpacity, ctaText, ctaUrl, ctaSecondaryText, ctaSecondaryUrl, minHeight, textAlign } = attributes;
        const blockProps = useBlockProps({ className: `fluxstack-hero fluxstack-hero--align-${textAlign}`, style: { backgroundImage: backgroundImage?.url ? `url(${backgroundImage.url})` : undefined, minHeight, backgroundSize: 'cover', backgroundPosition: 'center' } });

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Background', 'fluxstack')}>
                        <MediaUploadCheck>
                            <MediaUpload onSelect={(media) => setAttributes({ backgroundImage: { id: media.id, url: media.url, alt: media.alt } })} allowedTypes={['image']} value={backgroundImage?.id} render={({ open }) => (<div>{backgroundImage?.url ? (<div><img src={backgroundImage.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} /><Button onClick={() => setAttributes({ backgroundImage: {} })} variant="link" isDestructive>{__('Remove', 'fluxstack')}</Button></div>) : (<Button onClick={open} variant="secondary">{__('Select Image', 'fluxstack')}</Button>)}</div>)} />
                        </MediaUploadCheck>
                        <RangeControl label={__('Overlay Opacity', 'fluxstack')} value={overlayOpacity} onChange={(v) => setAttributes({ overlayOpacity: v })} min={0} max={100} step={5} />
                        <TextControl label={__('Min Height', 'fluxstack')} value={minHeight} onChange={(v) => setAttributes({ minHeight: v })} />
                    </PanelBody>
                    <PanelBody title={__('Layout', 'fluxstack')}>
                        <SelectControl label={__('Text Alignment', 'fluxstack')} value={textAlign} options={[{ label: 'Center', value: 'center' }, { label: 'Left', value: 'left' }, { label: 'Right', value: 'right' }]} onChange={(v) => setAttributes({ textAlign: v })} />
                    </PanelBody>
                    <PanelBody title={__('Primary CTA', 'fluxstack')} initialOpen={false}>
                        <TextControl label={__('Text', 'fluxstack')} value={ctaText} onChange={(v) => setAttributes({ ctaText: v })} />
                        <TextControl label={__('URL', 'fluxstack')} value={ctaUrl} onChange={(v) => setAttributes({ ctaUrl: v })} type="url" />
                    </PanelBody>
                    <PanelBody title={__('Secondary CTA', 'fluxstack')} initialOpen={false}>
                        <TextControl label={__('Text', 'fluxstack')} value={ctaSecondaryText} onChange={(v) => setAttributes({ ctaSecondaryText: v })} />
                        <TextControl label={__('URL', 'fluxstack')} value={ctaSecondaryUrl} onChange={(v) => setAttributes({ ctaSecondaryUrl: v })} type="url" />
                    </PanelBody>
                </InspectorControls>
                <section {...blockProps}>
                    <div className="fluxstack-hero__overlay" style={{ opacity: overlayOpacity / 100 }}></div>
                    <div className="fluxstack-hero__content">
                        <RichText tagName="h1" className="fluxstack-hero__heading" value={heading} onChange={(v) => setAttributes({ heading: v })} placeholder={__('Hero Heading...', 'fluxstack')} />
                        <RichText tagName="p" className="fluxstack-hero__subheading" value={subheading} onChange={(v) => setAttributes({ subheading: v })} placeholder={__('Subheading...', 'fluxstack')} />
                    </div>
                </section>
            </>
        );
    },
    save() { return null; },
});
