/**
 * Highlight font family — sidebar setting for Heading & Paragraph blocks.
 *
 * Stores the chosen font stack on the block (`highlightFontFamily`) and feeds
 * it to `.tnys-highlight` spans as a CSS custom property. The editor receives
 * the variable through the BlockListBlock wrapper below; the front end gets it
 * via `tnys_render_highlight_font_family()` in inc/extensions.php.
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { useSelect } from '@wordpress/data';
import { InspectorControls, store as blockEditorStore } from '@wordpress/block-editor';
import { ColorPalette, PanelBody, SelectControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

const SUPPORTED_BLOCKS = ['core/heading', 'core/paragraph'];
const ATTRIBUTE = 'highlightFontFamily';
const COLOR_ATTRIBUTE = 'highlightColor';
const FONT_FAMILY_VARIABLE = '--tnys-highlight-font-family';
const COLOR_VARIABLE = '--tnys-highlight-color';
const GRADIENT_VARIABLE = '--tnys-highlight-gradient';

addFilter('blocks.registerBlockType', 'tnys-highlight-add-attributes', (settings, name) => {
    if (!SUPPORTED_BLOCKS.includes(name)) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'string'
            },
            [COLOR_ATTRIBUTE]: {
                type: 'string'
            }
        }
    };
});

const withHighlightFontFamilyControls = createHigherOrderComponent(BlockEdit => {
    return props => {
        const { name, attributes, setAttributes } = props;

        // Runs before the early return to keep hook order stable.
        const fontFamilyOptions = useSelect(select => {
            const settings = select(blockEditorStore).getSettings();
            const typography = settings?.typography || settings?.__experimentalFeatures?.typography;
            const fontFamiliesSetting = typography?.fontFamilies;

            if (!fontFamiliesSetting) {
                return [];
            }

            const families = [];
            if (Array.isArray(fontFamiliesSetting)) {
                families.push(...fontFamiliesSetting);
            } else {
                const { theme = [], custom = [], default: defaultFonts = [] } = fontFamiliesSetting;
                families.push(...theme, ...custom, ...defaultFonts);
            }

            return [
                { label: __('Default', 'tiny-steps'), value: '' },
                ...families.map(family => ({
                    label: family.name || family.slug || __('Unknown', 'tiny-steps'),
                    value: family.fontFamily || (family.slug ? `var(--wp--preset--font-family--${family.slug})` : family.slug)
                }))
            ];
        }, []);

        const paletteColors = useSelect(select => {
            const settings = select(blockEditorStore).getSettings();
            const paletteSetting = settings?.color?.palette || settings?.__experimentalFeatures?.color?.palette;

            if (!paletteSetting) {
                return [];
            }

            if (Array.isArray(paletteSetting)) {
                return paletteSetting;
            }

            const { theme = [], custom = [], default: defaultColors = [] } = paletteSetting;
            return [...theme, ...custom, ...defaultColors];
        }, []);

        if (!SUPPORTED_BLOCKS.includes(name)) {
            return <BlockEdit {...props} />;
        }

        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title={__('Highlight', 'tiny-steps')} initialOpen={false}>
                        <SelectControl
                            label={__('Highlight font family', 'tiny-steps')}
                            help={__('Font family for highlighted text in this block.', 'tiny-steps')}
                            value={attributes[ATTRIBUTE] || ''}
                            options={fontFamilyOptions}
                            onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                            __nextHasNoMarginBottom
                            __next40pxDefaultSize
                        />
                        <ColorPalette
                            label={__('Highlight color', 'tiny-steps')}
                            help={__('Solid color replacing the gradient fill. Clear to use the default gradient.', 'tiny-steps')}
                            colors={paletteColors}
                            value={attributes[COLOR_ATTRIBUTE] || ''}
                            onChange={value => setAttributes({ [COLOR_ATTRIBUTE]: value || undefined })}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        );
    };
});
addFilter('editor.BlockEdit', 'tnys-highlight-font-family-controls', withHighlightFontFamilyControls);

const withHighlightFontFamilyWrapper = createHigherOrderComponent(BlockListBlock => {
    return props => {
        const { name, attributes } = props;

        if (!SUPPORTED_BLOCKS.includes(name) || (!attributes[ATTRIBUTE] && !attributes[COLOR_ATTRIBUTE])) {
            return <BlockListBlock {...props} />;
        }

        const style = {
            ...props.wrapperProps?.style
        };

        if (attributes[ATTRIBUTE]) {
            style[FONT_FAMILY_VARIABLE] = attributes[ATTRIBUTE];
        }

        // A solid highlight color replaces the gradient fill via two variables:
        // the fill color itself, and `none` to suppress the gradient image.
        if (attributes[COLOR_ATTRIBUTE]) {
            style[COLOR_VARIABLE] = attributes[COLOR_ATTRIBUTE];
            style[GRADIENT_VARIABLE] = 'none';
        }

        return <BlockListBlock {...props} wrapperProps={{ ...props.wrapperProps, style }} />;
    };
});
addFilter('editor.BlockListBlock', 'tnys-highlight-font-family-wrapper', withHighlightFontFamilyWrapper);
