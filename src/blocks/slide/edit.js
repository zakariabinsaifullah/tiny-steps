import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    useInnerBlocksProps,
    InspectorControls,
    BlockControls,
    MediaPlaceholder,
    MediaReplaceFlow,
} from '@wordpress/block-editor';
import { PanelBody, FocalPointPicker, TextareaControl, Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { NativeToggleControl } from '../../components';
import SlideMedia from './media';

const TEMPLATE = [ [ 'core/heading', { level: 3 } ], [ 'core/paragraph' ] ];

const toPercent = ( { x, y } ) => `${ Math.round( x * 100 ) }% ${ Math.round( y * 100 ) }%`;
const fromPercent = ( value = '50% 50%' ) => {
    const [ x, y ] = value.split( ' ' ).map( v => parseFloat( v ) / 100 );
    return { x: isNaN( x ) ? 0.5 : x, y: isNaN( y ) ? 0.5 : y };
};

export default function Edit( { attributes, setAttributes } ) {
    const { imageId, imageUrl, imageAlt, imagePosition, shade } = attributes;

    const onSelectImage = media =>
        setAttributes( {
            imageId: media?.id,
            imageUrl: media?.url,
            imageAlt: media?.alt || ''
        } );

    const removeImage = () => setAttributes( { imageId: undefined, imageUrl: undefined, imageAlt: '' } );

    const blockProps = useBlockProps( {
        className: [ imageUrl && 'has-image', shade && 'has-shade' ].filter( Boolean ).join( ' ' )
    } );

    const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, { template: TEMPLATE } );

    return (
        <>
            <BlockControls group="other">
                <MediaReplaceFlow
                    mediaId={ imageId }
                    mediaURL={ imageUrl }
                    allowedTypes={ [ 'image' ] }
                    accept="image/*"
                    onSelect={ onSelectImage }
                    name={ imageUrl ? __( 'Replace image', 'tiny-steps' ) : __( 'Add image', 'tiny-steps' ) }
                    onReset={ imageUrl ? removeImage : undefined }
                />
            </BlockControls>
            <InspectorControls>
                <PanelBody title={ __( 'Background image', 'tiny-steps' ) }>
                    { imageUrl ? (
                        <>
                            <FocalPointPicker
                                label={ __( 'Focal point', 'tiny-steps' ) }
                                url={ imageUrl }
                                value={ fromPercent( imagePosition ) }
                                onChange={ value => setAttributes( { imagePosition: toPercent( value ) } ) }
                                __nextHasNoMarginBottom
                            />
                            <TextareaControl
                                label={ __( 'Alternative text', 'tiny-steps' ) }
                                value={ imageAlt }
                                onChange={ value => setAttributes( { imageAlt: value } ) }
                                __nextHasNoMarginBottom
                            />
                            <Button variant="secondary" isDestructive onClick={ removeImage } style={ { marginTop: 16 } }>
                                { __( 'Remove image', 'tiny-steps' ) }
                            </Button>
                        </>
                    ) : (
                        <MediaPlaceholder
                            labels={ { title: __( 'Background image', 'tiny-steps' ) } }
                            allowedTypes={ [ 'image' ] }
                            accept="image/*"
                            onSelect={ onSelectImage }
                            disableMediaButtons={ false }
                            isAppender
                        />
                    ) }
                    <NativeToggleControl
                        label={ __( 'Dark shade', 'tiny-steps' ) }
                        help={ __( 'Darkens the bottom of the slide so white text stays readable.', 'tiny-steps' ) }
                        checked={ shade }
                        onChange={ value => setAttributes( { shade: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...innerBlocksProps }>
                <SlideMedia imageUrl={ imageUrl } imageAlt={ imageAlt } imagePosition={ imagePosition } shade={ shade } />
                { children }
            </div>
        </>
    );
}
