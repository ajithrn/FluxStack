import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import {
    PanelBody,
    ToggleControl,
    SelectControl,
    Button,
    TextControl,
    TextareaControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const VARIANT_OPTIONS = [
    { label: __('Default (bordered)', 'fluxstack'), value: 'default' },
    { label: __('Separated (cards)', 'fluxstack'), value: 'separated' },
    { label: __('Minimal (dividers only)', 'fluxstack'), value: 'minimal' },
];

const ICON_OPTIONS = [
    { label: __('Plus / Minus', 'fluxstack'), value: 'plus' },
    { label: __('Chevron', 'fluxstack'), value: 'chevron' },
    { label: __('None', 'fluxstack'), value: 'none' },
];

const GAP_OPTIONS = [
    { label: __('Compact', 'fluxstack'), value: 'compact' },
    { label: __('Comfortable', 'fluxstack'), value: 'comfortable' },
    { label: __('Spacious', 'fluxstack'), value: 'spacious' },
];

registerBlockType(metadata.name, {
    edit({ attributes, setAttributes }) {
        const { items, openFirst, variant, iconStyle, gap } = attributes;
        const [expandedItems, setExpandedItems] = useState({});
        const blockProps = useBlockProps({
            className: [
                'fluxstack-accordion',
                'fluxstack-accordion--' + variant,
                'fluxstack-accordion--gap-' + gap,
                'fluxstack-accordion--icon-' + iconStyle,
            ].join(' '),
        });

        const updateItem = (index, field, value) => {
            const updated = [...items];
            updated[index] = { ...updated[index], [field]: value };
            setAttributes({ items: updated });
        };

        const addItem = () => {
            const newIndex = items.length;
            setAttributes({ items: [...items, { question: '', answer: '' }] });
            setExpandedItems({ ...expandedItems, [newIndex]: true });
        };

        const removeItem = (index) => {
            setAttributes({ items: items.filter((_, i) => i !== index) });
        };

        const moveItem = (index, direction) => {
            const updated = [...items];
            const target = index + direction;
            if (target < 0 || target >= updated.length) return;
            [updated[index], updated[target]] = [updated[target], updated[index]];
            setAttributes({ items: updated });
        };

        const toggleExpand = (index) => {
            setExpandedItems({ ...expandedItems, [index]: !expandedItems[index] });
        };

        const isItemExpanded = (index) => {
            return expandedItems[index] !== undefined ? expandedItems[index] : (index === 0);
        };

        // --- Sidebar: Settings tab ---
        const settingsInspector = React.createElement(InspectorControls, null,
            React.createElement(PanelBody, { title: __('Manage Items', 'fluxstack'), initialOpen: true },
                items.length === 0
                    ? React.createElement('p', { style: { fontSize: '0.85rem', opacity: 0.7, fontStyle: 'italic' } },
                        __('No items yet. Click "+ Add Item" in the canvas or below.', 'fluxstack')
                    )
                    : items.map((item, index) =>
                        React.createElement('div', {
                            key: index,
                            style: {
                                marginBottom: '0.75rem',
                                padding: '0.75rem',
                                background: '#f9fafb',
                                borderRadius: '0.375rem',
                                border: '1px solid #e5e7eb',
                            }
                        },
                            React.createElement('div', {
                                style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.5rem' }
                            },
                                React.createElement('span', {
                                    style: { fontSize: '0.7rem', fontWeight: 600, color: '#6b7280', textTransform: 'uppercase', letterSpacing: '0.025em' }
                                }, __('Item', 'fluxstack') + ' ' + (index + 1)),
                                React.createElement('div', { style: { display: 'flex', gap: '2px' } },
                                    React.createElement(Button, {
                                        icon: 'arrow-up-alt2',
                                        size: 'small',
                                        disabled: index === 0,
                                        onClick: () => moveItem(index, -1),
                                        label: __('Move up', 'fluxstack'),
                                    }),
                                    React.createElement(Button, {
                                        icon: 'arrow-down-alt2',
                                        size: 'small',
                                        disabled: index === items.length - 1,
                                        onClick: () => moveItem(index, 1),
                                        label: __('Move down', 'fluxstack'),
                                    }),
                                    React.createElement(Button, {
                                        icon: 'no-alt',
                                        size: 'small',
                                        isDestructive: true,
                                        onClick: () => removeItem(index),
                                        label: __('Remove', 'fluxstack'),
                                    })
                                )
                            ),
                            React.createElement(TextControl, {
                                label: __('Question', 'fluxstack'),
                                value: item.question || '',
                                onChange: (val) => updateItem(index, 'question', val),
                                placeholder: __('Enter question...', 'fluxstack'),
                                __nextHasNoMarginBottom: true,
                            }),
                            React.createElement('div', { style: { marginTop: '0.5rem' } },
                                React.createElement(TextareaControl, {
                                    label: __('Answer', 'fluxstack'),
                                    value: item.answer || '',
                                    onChange: (val) => updateItem(index, 'answer', val),
                                    placeholder: __('Enter answer...', 'fluxstack'),
                                    rows: 2,
                                    __nextHasNoMarginBottom: true,
                                })
                            )
                        )
                    ),
                React.createElement(Button, {
                    variant: 'secondary',
                    onClick: addItem,
                    icon: 'plus-alt2',
                    style: { width: '100%', justifyContent: 'center', marginTop: '0.5rem' },
                }, __('Add Item', 'fluxstack'))
            ),
            React.createElement(PanelBody, { title: __('Behavior', 'fluxstack'), initialOpen: false },
                React.createElement(ToggleControl, {
                    label: __('Open first item by default', 'fluxstack'),
                    checked: openFirst,
                    onChange: (val) => setAttributes({ openFirst: val }),
                    __nextHasNoMarginBottom: true,
                })
            )
        );

        // --- Sidebar: Styles tab ---
        const stylesInspector = React.createElement(InspectorControls, { group: 'styles' },
            React.createElement(PanelBody, { title: __('Accordion Style', 'fluxstack'), initialOpen: true },
                React.createElement(SelectControl, {
                    label: __('Variant', 'fluxstack'),
                    value: variant,
                    options: VARIANT_OPTIONS,
                    onChange: (val) => setAttributes({ variant: val }),
                    help: __('Controls the visual style of each accordion item.', 'fluxstack'),
                    __nextHasNoMarginBottom: true,
                }),
                React.createElement('div', { style: { marginTop: '1rem' } },
                    React.createElement(SelectControl, {
                        label: __('Toggle Icon', 'fluxstack'),
                        value: iconStyle,
                        options: ICON_OPTIONS,
                        onChange: (val) => setAttributes({ iconStyle: val }),
                        __nextHasNoMarginBottom: true,
                    })
                ),
                React.createElement('div', { style: { marginTop: '1rem' } },
                    React.createElement(SelectControl, {
                        label: __('Item Spacing', 'fluxstack'),
                        value: gap,
                        options: GAP_OPTIONS,
                        onChange: (val) => setAttributes({ gap: val }),
                        __nextHasNoMarginBottom: true,
                    })
                )
            )
        );

        // --- Canvas preview ---
        var canvasContent;

        if (items.length === 0) {
            canvasContent = React.createElement('div', { className: 'fluxstack-accordion__placeholder' },
                React.createElement('p', { className: 'fluxstack-accordion__placeholder-title' },
                    __('Accordion / FAQ', 'fluxstack')
                ),
                React.createElement('p', { className: 'fluxstack-accordion__placeholder-desc' },
                    __('Click below to add your first FAQ item.', 'fluxstack')
                ),
                React.createElement('button', {
                    onClick: addItem,
                    className: 'fluxstack-accordion__add-btn',
                }, '+ ' + __('Add Item', 'fluxstack'))
            );
        } else {
            var itemElements = items.map(function(item, index) {
                var expanded = isItemExpanded(index);
                var itemClass = 'fluxstack-accordion__editor-item' + (expanded ? ' fluxstack-accordion__editor-item--expanded' : '');
                var questionClass = 'fluxstack-accordion__editor-question' + (expanded ? '' : ' fluxstack-accordion__editor-question--collapsed');
                var iconClass = 'fluxstack-accordion__editor-icon' + (expanded ? ' fluxstack-accordion__editor-icon--expanded' : '');

                return React.createElement('div', { key: index, className: itemClass },
                    // Toolbar
                    React.createElement('div', { className: 'fluxstack-accordion__toolbar' },
                        React.createElement('span', { className: 'fluxstack-accordion__toolbar-num' }, '#' + (index + 1)),
                        React.createElement('button', {
                            onClick: function() { moveItem(index, -1); },
                            disabled: index === 0,
                            className: 'fluxstack-accordion__toolbar-btn',
                            title: __('Move up', 'fluxstack'),
                        }, '\u25B2'),
                        React.createElement('button', {
                            onClick: function() { moveItem(index, 1); },
                            disabled: index === items.length - 1,
                            className: 'fluxstack-accordion__toolbar-btn',
                            title: __('Move down', 'fluxstack'),
                        }, '\u25BC'),
                        React.createElement('button', {
                            onClick: function() { toggleExpand(index); },
                            className: 'fluxstack-accordion__toolbar-btn',
                            title: expanded ? __('Collapse', 'fluxstack') : __('Expand', 'fluxstack'),
                        }, expanded ? '\u25B4 collapse' : '\u25BE expand'),
                        React.createElement('button', {
                            onClick: function() { removeItem(index); },
                            className: 'fluxstack-accordion__toolbar-btn fluxstack-accordion__toolbar-btn--delete',
                            title: __('Remove item', 'fluxstack'),
                        }, '\u00D7 remove')
                    ),
                    // Question
                    React.createElement('div', {
                        className: questionClass,
                        onClick: expanded ? undefined : function() { toggleExpand(index); },
                    },
                        expanded
                            ? React.createElement(RichText, {
                                tagName: 'span',
                                value: item.question || '',
                                onChange: function(val) { updateItem(index, 'question', val); },
                                placeholder: __('Type your question...', 'fluxstack'),
                                allowedFormats: [],
                                style: { flex: 1 },
                            })
                            : React.createElement('span', { style: { flex: 1 } },
                                item.question || __('(No question)', 'fluxstack')
                            ),
                        iconStyle !== 'none' && React.createElement('span', { className: iconClass },
                            iconStyle === 'chevron' ? '\u25BE' : (expanded ? '\u2212' : '+')
                        )
                    ),
                    // Answer (only when expanded)
                    expanded && React.createElement('div', { className: 'fluxstack-accordion__editor-answer' },
                        React.createElement(RichText, {
                            tagName: 'p',
                            value: item.answer || '',
                            onChange: function(val) { updateItem(index, 'answer', val); },
                            placeholder: __('Type the answer...', 'fluxstack'),
                            allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                            style: { margin: 0 },
                        })
                    )
                );
            });

            canvasContent = React.createElement(React.Fragment, null,
                itemElements,
                React.createElement('button', {
                    onClick: addItem,
                    className: 'fluxstack-accordion__add-btn',
                }, '+ ' + __('Add Item', 'fluxstack'))
            );
        }

        return React.createElement(React.Fragment, null,
            settingsInspector,
            stylesInspector,
            React.createElement('div', blockProps, canvasContent)
        );
    },
    save() {
        return null;
    },
});
