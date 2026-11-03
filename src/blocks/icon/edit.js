/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import {
    RichText,
    BlockControls,
    InspectorControls,
    useBlockProps,
    JustifyToolbar,
    __experimentalUseBorderProps as useBorderProps,
    __experimentalUseColorProps as useColorProps,
    __experimentalGetSpacingClassesAndStyles as useSpacingProps,
    __experimentalGetShadowClassesAndStyles as useShadowProps,
    __experimentalLinkControl as LinkControl
} from '@wordpress/block-editor';
import { link } from '@wordpress/icons';
import {
    Button,
    PanelBody,
    RangeControl,
    ToolbarButton,
    Popover,
    __experimentalToolsPanel as ToolsPanel, // eslint-disable-line
    __experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

import classNames from 'classnames';

/**
 * Internal dependencies
 */
import {
    NativeResponsiveControl,
    NativeToggleControl,
    NativeTextControl,
    NativeIconPicker,
    PanelColorControl,
    NativeSelectControl,
    NativeTextareaControl,
    NativeUnitControl
} from '../../components';

import { RenderIcon } from '../../helpers';

import './editor.scss';

/*
 * The theme ships DM Sans (100-1000) and Manrope (200-800) as variable
 * fonts; these are the common weights offered. "Default" writes nothing,
 * leaving the title at whatever its tag gives it — bold for a heading,
 * regular for a paragraph.
 */
const TITLE_FONT_WEIGHTS = [
    { label: __('Default', 'tiny-steps'), value: '' },
    { label: __('Light (300)', 'tiny-steps'), value: '300' },
    { label: __('Regular (400)', 'tiny-steps'), value: '400' },
    { label: __('Medium (500)', 'tiny-steps'), value: '500' },
    { label: __('Semi Bold (600)', 'tiny-steps'), value: '600' },
    { label: __('Bold (700)', 'tiny-steps'), value: '700' }
];

export default function Edit(props) {
    const { attributes, setAttributes, className } = props;
    const {
        iconName,
        iconSize,
        iconMarginTop,
        customSvgCode,
        iconType,
        strokeWidth,
        justifyContent,
        href,
        linkTarget,
        sizes,
        resMode,
        heading,
        headingTag,
        showTitle,
        listGap,
        titleColor,
        titleSize,
        titleFontFamily,
        titleFontWeight,
        titleMarginBottom,
        showDesc,
        description,
        descTag,
        descColor,
        descSize,
        descFontFamily,
        showButton,
        buttonText,
        buttonUrl,
        buttonLinkTarget,
        buttonLinkRel,
        buttonMarginTop,
        buttonIconName,
        buttonCustomSvgCode,
        buttonIconType,
        buttonStrokeWidth,
        iconVerticalAlign
    } = attributes;

    // Title, description and button are independent, so the content area
    // renders for any one of them on its own.
    const hasContent = showTitle || showDesc || showButton;

    const hasButtonIcon = !!(buttonIconName || buttonCustomSvgCode);

    const fontFamilies = useSelect(select => {
        const settings = select('core/block-editor').getSettings();
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

        return families;
    }, []);

    const fontFamilyOptions = [
        { label: __('Default', 'tiny-steps'), value: '' },
        ...fontFamilies.map(f => {
            const value = f.fontFamily || (f.slug ? `var(--wp--preset--font-family--${f.slug})` : f.slug);
            return {
                label: f.name || f.slug || __('Unknown', 'tiny-steps'),
                value: value
            };
        })
    ];

    // Default size, and the value each breakpoint falls back to when unset.
    const DEFAULT_ICON_SIZE = 24;

    const resolvedSizes = {
        Desktop: sizes?.Desktop ?? DEFAULT_ICON_SIZE,
        Tablet: sizes?.Tablet ?? sizes?.Desktop ?? DEFAULT_ICON_SIZE,
        Mobile: sizes?.Mobile ?? sizes?.Tablet ?? sizes?.Desktop ?? DEFAULT_ICON_SIZE
    };

    const isInheritedSize = undefined === sizes?.[resMode];

    // Only explicit sizes are written out; style.scss cascades the rest.
    const sizeCustomProperties = {
        ...(sizes?.Desktop && { '--dsize': `${sizes.Desktop}px` }),
        ...(sizes?.Tablet && { '--tsize': `${sizes.Tablet}px` }),
        ...(sizes?.Mobile && { '--msize': `${sizes.Mobile}px` })
    };

    const cssCustomProperties = {
        ...(listGap && { '--list-gap': `${listGap}` }),
        ...(iconMarginTop && { '--icon-margin-top': `${iconMarginTop}` }),
        ...(titleColor && { '--title-color': titleColor }),
        ...(titleSize && { '--title-size': `${titleSize}` }),
        ...(titleFontFamily && { '--title-font-family': titleFontFamily }),
        ...(titleFontWeight && { '--title-font-weight': titleFontWeight }),
        ...(titleMarginBottom && { '--title-margin-bottom': `${titleMarginBottom}` }),
        ...(descColor && { '--desc-color': descColor }),
        ...(descSize && { '--desc-size': `${descSize}` }),
        ...(descFontFamily && { '--desc-font-family': descFontFamily }),
        ...(buttonMarginTop && { '--button-margin-top': `${buttonMarginTop}` })
    };

    useEffect(() => {
        setAttributes({
            blockStyle: cssCustomProperties
        });
    }, [
        listGap,
        iconMarginTop,
        titleColor,
        titleSize,
        titleFontFamily,
        titleFontWeight,
        titleMarginBottom,
        descColor,
        descSize,
        descFontFamily,
        buttonMarginTop
    ]);

    // states
    const [isEditingURL, setIsEditingURL] = useState(false);
    const [popoverAnchor, setPopoverAnchor] = useState(null);

    const borderProps = useBorderProps(attributes);
    const colorProps = useColorProps(attributes);
    const spacingProps = useSpacingProps(attributes);
    const shadowProps = useShadowProps(attributes);

    const blockProps = useBlockProps({
        style: cssCustomProperties,
        className: classNames(className, {
            [`is-${iconType}`]: iconType,
            [`justify-${justifyContent}`]: justifyContent,
            'has-title-weight': !!titleFontWeight
        })
    });

    return (
        <>
            <BlockControls group="block">
                <JustifyToolbar
                    allowedControls={['left', 'center', 'right']}
                    value={justifyContent}
                    onChange={value =>
                        setAttributes({
                            justifyContent: value
                        })
                    }
                />

                {/* <ToolbarButton
                    ref={setPopoverAnchor}
                    name="link"
                    icon={link}
                    title={__('Link', 'tiny-steps')}
                    onClick={() => setIsEditingURL(true)}
                    isActive={!!href || isEditingURL}
                />
                {isEditingURL && (
                    <Popover
                        anchor={popoverAnchor}
                        onClose={() => setIsEditingURL(false)}
                        placement="bottom"
                        focusOnMount={true}
                        offset={12}
                        className="tnys-icon__link-popover"
                        variant="alternate"
                    >
                        <LinkControl
                            value={{
                                url: href,
                                opensInNewTab: linkTarget === '_blank'
                            }}
                            onChange={({ url: newURL = '', opensInNewTab }) => {
                                setAttributes({
                                    href: newURL,
                                    linkTarget: opensInNewTab ? '_blank' : undefined,
                                    linkRel: newURL ? 'nofollow' : undefined,
                                    tagName: 'a'
                                });
                            }}
                            onRemove={() =>
                                setAttributes({
                                    href: undefined,
                                    linkTarget: undefined,
                                    linkRel: undefined,
                                    tagName: 'div'
                                })
                            }
                        />
                    </Popover>
                )} */}
            </BlockControls>
            <InspectorControls>
                <PanelBody title={__('Settings', 'tiny-steps')}>
                    <NativeToggleControl
                        label={__('Add List Title', 'tiny-steps')}
                        checked={showTitle}
                        onChange={value => setAttributes({ showTitle: value })}
                    />
                    <NativeToggleControl
                        label={__('Add Description', 'tiny-steps')}
                        checked={showDesc}
                        onChange={value => setAttributes({ showDesc: value })}
                    />
                    <NativeToggleControl
                        label={__('Add Button', 'tiny-steps')}
                        checked={showButton}
                        onChange={value => setAttributes({ showButton: value })}
                    />
                    <NativeIconPicker
                        onIconSelect={(iconName, iconType) => {
                            setAttributes({ iconName, iconType, customSvgCode: undefined });
                        }}
                        onCustomSvgInsert={({ customSvgCode, iconType, strokeWidth }) => {
                            setAttributes({ customSvgCode, iconType, strokeWidth });
                        }}
                        iconName={iconName}
                        customSvgCode={customSvgCode}
                        iconSize={iconSize}
                        strokeWidth={strokeWidth}
                    />
                    <NativeResponsiveControl label={__('Icon Size (px)', 'tiny-steps')} props={props}>
                        <RangeControl
                            value={resolvedSizes[resMode]}
                            onChange={value => setAttributes({ sizes: { ...sizes, [resMode]: value } })}
                            min={8}
                            max={256}
                            allowReset
                            help={
                                'Desktop' !== resMode && isInheritedSize
                                    ? __(
                                          'Inherited from the larger screen size. Change it to set a size just for this device.',
                                          'tiny-steps'
                                      )
                                    : undefined
                            }
                            __next40pxDefaultSize
                        />
                    </NativeResponsiveControl>
                    {/* Nudges the icon down — mainly for top-aligned icons beside multi-line text. */}
                    <NativeUnitControl
                        label={__('Icon Top Margin', 'tiny-steps')}
                        value={iconMarginTop}
                        onChange={value => setAttributes({ iconMarginTop: value })}
                    />
                </PanelBody>
                {hasContent && (
                    <PanelBody title={__('Title & Description', 'tiny-steps')} initialOpen={false}>
                        <NativeUnitControl
                            label={__('Gap ', 'tiny-steps')}
                            value={listGap}
                            onChange={value => setAttributes({ listGap: value })}
                        />
                        <NativeSelectControl
                            label={__('Vertical Alignment', 'tiny-steps')}
                            value={iconVerticalAlign}
                            onChange={value => setAttributes({ iconVerticalAlign: value })}
                            options={[
                                { label: __('Top', 'tiny-steps'), value: 'top' },
                                { label: __('Center', 'tiny-steps'), value: 'center' },
                                { label: __('Bottom', 'tiny-steps'), value: 'bottom' }
                            ]}
                        />
                        {showTitle && (
                            <>
                                <NativeSelectControl
                                    label={__('Title Tag', 'tiny-steps')}
                                    value={headingTag}
                                    onChange={value => setAttributes({ headingTag: value })}
                                    options={[
                                        { label: __('H1', 'tiny-steps'), value: 'h1' },
                                        { label: __('H2', 'tiny-steps'), value: 'h2' },
                                        { label: __('H3', 'tiny-steps'), value: 'h3' },
                                        { label: __('H4', 'tiny-steps'), value: 'h4' },
                                        { label: __('H5', 'tiny-steps'), value: 'h5' },
                                        { label: __('H6', 'tiny-steps'), value: 'h6' },
                                        { label: __('Paragraph', 'tiny-steps'), value: 'p' },
                                        { label: __('Div', 'tiny-steps'), value: 'div' }
                                    ]}
                                />
                                <NativeTextControl
                                    label={__('Title Text', 'tiny-steps')}
                                    value={heading}
                                    onChange={value => setAttributes({ heading: value })}
                                    placeholder={__('List title...', 'tiny-steps')}
                                />
                            </>
                        )}
                        {showDesc && (
                            <>
                                <NativeSelectControl
                                    label={__('Description Tag', 'tiny-steps')}
                                    value={descTag}
                                    onChange={value => setAttributes({ descTag: value })}
                                    options={[
                                        { label: __('Paragraph', 'tiny-steps'), value: 'p' },
                                        { label: __('Div', 'tiny-steps'), value: 'div' },
                                        { label: __('Span', 'tiny-steps'), value: 'span' }
                                    ]}
                                />
                                <NativeTextareaControl
                                    label={__('Description Text', 'tiny-steps')}
                                    value={description}
                                    onChange={value => setAttributes({ description: value })}
                                    placeholder={__('Description...', 'tiny-steps')}
                                />
                            </>
                        )}
                    </PanelBody>
                )}
                {showButton && (
                    <PanelBody title={__('Button', 'tiny-steps')} initialOpen={false}>
                        <NativeTextControl
                            label={__('Button Text', 'tiny-steps')}
                            value={buttonText}
                            onChange={value => setAttributes({ buttonText: value })}
                            placeholder={__('Learn more', 'tiny-steps')}
                        />
                        <NativeTextControl
                            label={__('Button URL', 'tiny-steps')}
                            value={buttonUrl}
                            onChange={value => setAttributes({ buttonUrl: value })}
                            placeholder={__('https://…', 'tiny-steps')}
                        />
                        <NativeToggleControl
                            label={__('Open in New Tab', 'tiny-steps')}
                            checked={'_blank' === buttonLinkTarget}
                            onChange={value =>
                                setAttributes({
                                    buttonLinkTarget: value ? '_blank' : undefined,
                                    // Matches the rel the block's own link control sets.
                                    buttonLinkRel: value ? 'noreferrer noopener' : undefined
                                })
                            }
                        />
                        {/* Same picker the block's main icon uses, so the icon sets match. */}
                        <NativeIconPicker
                            label={__('Button Icon', 'tiny-steps')}
                            onIconSelect={(iconName, iconType) => {
                                setAttributes({
                                    buttonIconName: iconName,
                                    buttonIconType: iconType,
                                    buttonCustomSvgCode: undefined
                                });
                            }}
                            onCustomSvgInsert={({ customSvgCode, iconType, strokeWidth }) => {
                                setAttributes({
                                    buttonCustomSvgCode: customSvgCode,
                                    buttonIconType: iconType,
                                    buttonStrokeWidth: strokeWidth,
                                    buttonIconName: undefined
                                });
                            }}
                            iconName={buttonIconName}
                            customSvgCode={buttonCustomSvgCode}
                            iconSize={24}
                            strokeWidth={buttonStrokeWidth}
                        />
                        {hasButtonIcon && (
                            <Button
                                variant="tertiary"
                                isDestructive
                                onClick={() =>
                                    setAttributes({
                                        buttonIconName: undefined,
                                        buttonCustomSvgCode: undefined,
                                        buttonStrokeWidth: undefined
                                    })
                                }
                            >
                                {__('Remove Button Icon', 'tiny-steps')}
                            </Button>
                        )}
                        <NativeUnitControl
                            label={__('Top Margin', 'tiny-steps')}
                            value={buttonMarginTop}
                            onChange={value => setAttributes({ buttonMarginTop: value })}
                        />
                    </PanelBody>
                )}
            </InspectorControls>
            <InspectorControls group="styles">
                {showTitle && (
                    <ToolsPanel
                        label={__('Title', 'tiny-steps')}
                        resetAll={() =>
                            setAttributes({
                                titleSize: undefined,
                                titleColor: undefined,
                                titleFontFamily: undefined,
                                titleFontWeight: undefined,
                                titleMarginBottom: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!titleSize}
                            label={__('Size', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    titleSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Font Size', 'tiny-steps')}
                                value={titleSize}
                                onChange={value => setAttributes({ titleSize: value })}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleColor}
                            label={__('Color', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    titleColor: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <PanelColorControl
                                label={__('Color', 'tiny-steps')}
                                colorSettings={[
                                    {
                                        value: titleColor,
                                        onChange: color => setAttributes({ titleColor: color }),
                                        label: __('Color', 'tiny-steps')
                                    }
                                ]}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleFontFamily}
                            label={__('Font', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    titleFontFamily: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeSelectControl
                                label={__('Font', 'tiny-steps')}
                                value={titleFontFamily}
                                onChange={value => setAttributes({ titleFontFamily: value })}
                                options={fontFamilyOptions}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleFontWeight}
                            label={__('Weight', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    titleFontWeight: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeSelectControl
                                label={__('Weight', 'tiny-steps')}
                                value={titleFontWeight}
                                onChange={value => setAttributes({ titleFontWeight: value })}
                                options={TITLE_FONT_WEIGHTS}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!titleMarginBottom}
                            label={__('Bottom Margin', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    titleMarginBottom: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Bottom Margin', 'tiny-steps')}
                                value={titleMarginBottom}
                                onChange={value => setAttributes({ titleMarginBottom: value })}
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                )}
                {showDesc && (
                    <ToolsPanel
                        label={__('Description', 'tiny-steps')}
                        resetAll={() =>
                            setAttributes({
                                descSize: undefined,
                                descColor: undefined,
                                descFontFamily: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!descSize}
                            label={__('Size', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    descSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Font Size', 'tiny-steps')}
                                value={descSize}
                                onChange={value => setAttributes({ descSize: value })}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!descColor}
                            label={__('Color', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    descColor: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <PanelColorControl
                                label={__('Color', 'tiny-steps')}
                                colorSettings={[
                                    {
                                        value: descColor,
                                        onChange: color => setAttributes({ descColor: color }),
                                        label: __('Color', 'tiny-steps')
                                    }
                                ]}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!descFontFamily}
                            label={__('Font', 'tiny-steps')}
                            onDeselect={() => {
                                setAttributes({
                                    descFontFamily: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeSelectControl
                                label={__('Font', 'tiny-steps')}
                                value={descFontFamily}
                                onChange={value => setAttributes({ descFontFamily: value })}
                                options={fontFamilyOptions}
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                )}
            </InspectorControls>
            <div {...blockProps}>
                <div
                    className={classNames('tnys-icon-block-wrapper', {
                        [`icon-valign-${iconVerticalAlign}`]: iconVerticalAlign
                    })}
                >
                    <div
                        className={classNames('icon-container', colorProps.className, borderProps.className)}
                        style={{
                            ...borderProps.style,
                            ...colorProps.style,
                            ...spacingProps.style,
                            ...shadowProps.style,
                            ...sizeCustomProperties
                        }}
                    >
                        <RenderIcon customSvgCode={customSvgCode} iconName={iconName} size={iconSize} />
                    </div>
                    {hasContent && (
                        <div className="icon-content">
                            {showTitle && (
                                <RichText
                                    tagName={headingTag}
                                    value={heading}
                                    onChange={value => setAttributes({ heading: value })}
                                    placeholder={__('List title...', 'tiny-steps')}
                                    className="icon-heading"
                                    withoutInteractiveFormatting
                                />
                            )}
                            {showDesc && (
                                <RichText
                                    tagName={descTag}
                                    value={description}
                                    onChange={value => setAttributes({ description: value })}
                                    placeholder={__('Description...', 'tiny-steps')}
                                    className="icon-description"
                                    withoutInteractiveFormatting
                                />
                            )}
                            {/* No href in the editor, so clicking it cannot navigate away. */}
                            {showButton && (
                                <a className="wp-element-button icon-button">
                                    <RichText
                                        tagName="span"
                                        value={buttonText}
                                        onChange={value => setAttributes({ buttonText: value })}
                                        placeholder={__('Learn more', 'tiny-steps')}
                                        className="icon-button__text"
                                        withoutInteractiveFormatting
                                    />
                                    {hasButtonIcon && (
                                        <span className={classNames('icon-button__icon', `is-${buttonIconType}`)}>
                                            <RenderIcon customSvgCode={buttonCustomSvgCode} iconName={buttonIconName} size={24} />
                                        </span>
                                    )}
                                </a>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
