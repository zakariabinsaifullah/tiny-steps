/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'kadence/rowlayout';
const ATTRIBUTE = 'addOverlay';
const CLASS_NAME = 'core-overlay';

/**
 * Add `addOverlay` attribute to kadence/rowlayout.
 */
addFilter('blocks.registerBlockType', 'core-theme/kadence-row-overlay-add-attribute', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'boolean',
                default: false
            }
        }
    };
});

/**
 * Add "Add Overlay" toggle to the kadence/rowlayout inspector.
 */
addFilter(
    'editor.BlockEdit',
    'core-theme/kadence-row-overlay-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Overlay', 'core')}>
                            <NativeToggleControl
                                label={__('Add overlay', 'core')}
                                help={__('Apply the photo-hero overlay treatment (right 57% on desktop, bottom band on tablet/mobile). Requires a Tiber background colour and a Kadence overlay image.', 'core')}
                                checked={!!attributes[ATTRIBUTE]}
                                onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                            />
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

/**
 * Apply the class in the editor preview so designers can see the effect
 * without having to add the class manually.
 */
addFilter(
    'editor.BlockListBlock',
    'core-theme/kadence-row-overlay-add-class',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, CLASS_NAME].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);
