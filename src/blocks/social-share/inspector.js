/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, __experimentalToolsPanel as ToolsPanel, __experimentalToolsPanelItem as ToolsPanelItem } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { NativeToggleControl, NativeUnitControl, PanelColorControl } from '../../components';

export default function Inspector(props) {
    const { attributes, setAttributes } = props;
    const { showCopyLink, showLinkedIn, showTwitter, showFacebook, iconSize, gap, iconColor, iconBgColor, iconRadius, iconPadding } =
        attributes;

    useEffect(() => {
        setAttributes({
            blockStyle: {
                ...(iconSize && { '--icon-size': iconSize }),
                ...(gap && { '--gap': gap }),
                ...(iconColor && { '--icon-color': iconColor }),
                ...(iconBgColor && { '--icon-bg': iconBgColor }),
                ...(iconRadius && { '--icon-radius': iconRadius }),
                ...(iconPadding && { '--icon-padding': iconPadding })
            }
        });
    }, [iconSize, gap, iconColor, iconBgColor, iconRadius, iconPadding]);

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Platforms', 'tiny-steps')}>
                    <NativeToggleControl
                        label={__('Copy Link', 'tiny-steps')}
                        checked={showCopyLink}
                        onChange={value => setAttributes({ showCopyLink: value })}
                    />
                    <NativeToggleControl
                        label={__('LinkedIn', 'tiny-steps')}
                        checked={showLinkedIn}
                        onChange={value => setAttributes({ showLinkedIn: value })}
                    />
                    <NativeToggleControl
                        label={__('X (Twitter)', 'tiny-steps')}
                        checked={showTwitter}
                        onChange={value => setAttributes({ showTwitter: value })}
                    />
                    <NativeToggleControl
                        label={__('Facebook', 'tiny-steps')}
                        checked={showFacebook}
                        onChange={value => setAttributes({ showFacebook: value })}
                    />
                </PanelBody>
                <PanelBody title={__('Layout', 'tiny-steps')} initialOpen={false}>
                    <NativeUnitControl
                        label={__('Icon Size', 'tiny-steps')}
                        value={iconSize}
                        onChange={value => setAttributes({ iconSize: value })}
                        mb={16}
                    />
                    <NativeUnitControl label={__('Gap', 'tiny-steps')} value={gap} onChange={value => setAttributes({ gap: value })} />
                </PanelBody>
            </InspectorControls>
            <InspectorControls group="styles">
                <ToolsPanel
                    label={__('Icon Style', 'tiny-steps')}
                    resetAll={() =>
                        setAttributes({
                            iconColor: undefined,
                            iconBgColor: undefined,
                            iconRadius: undefined,
                            iconPadding: undefined
                        })
                    }
                >
                    <ToolsPanelItem
                        hasValue={() => !!iconBgColor || !!iconColor}
                        label={__('Colors', 'tiny-steps')}
                        onDeselect={() => setAttributes({ iconBgColor: undefined })}
                        onSelect={() => {}}
                    >
                        <PanelColorControl
                            label={__('Colors', 'tiny-steps')}
                            colorSettings={[
                                {
                                    value: iconColor,
                                    onChange: color => setAttributes({ iconColor: color }),
                                    label: __('Color', 'tiny-steps')
                                },
                                {
                                    value: iconBgColor,
                                    onChange: color => setAttributes({ iconBgColor: color }),
                                    label: __('Background', 'tiny-steps')
                                }
                            ]}
                        />
                    </ToolsPanelItem>

                    <ToolsPanelItem
                        hasValue={() => !!iconRadius}
                        label={__('Radius', 'tiny-steps')}
                        onDeselect={() => setAttributes({ iconRadius: undefined })}
                        onSelect={() => {}}
                    >
                        <NativeUnitControl
                            label={__('Radius', 'tiny-steps')}
                            value={iconRadius}
                            onChange={value => setAttributes({ iconRadius: value })}
                        />
                    </ToolsPanelItem>

                    <ToolsPanelItem
                        hasValue={() => !!iconPadding}
                        label={__('Padding', 'tiny-steps')}
                        onDeselect={() => setAttributes({ iconPadding: undefined })}
                        onSelect={() => {}}
                    >
                        <NativeUnitControl
                            label={__('Padding', 'tiny-steps')}
                            value={iconPadding}
                            onChange={value => setAttributes({ iconPadding: value })}
                        />
                    </ToolsPanelItem>
                </ToolsPanel>
            </InspectorControls>
        </>
    );
}
