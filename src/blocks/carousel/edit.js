import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { NativeRangeControl, NativeResponsiveControl, NativeToggleControl } from '../../components';
import { carouselStyle, carouselClasses } from './utils';
import './editor.scss';

const TEMPLATE = [ [ 'tnys/slide' ], [ 'tnys/slide' ], [ 'tnys/slide' ] ];

export default function Edit( props ) {
    const { attributes, setAttributes } = props;
    const { perView, gap, height, gridDesktop, autoplay, interval, loop, resMode = 'Desktop' } = attributes;

    const blockProps = useBlockProps( {
        className: carouselClasses( attributes ),
        style: carouselStyle( attributes )
    } );

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'tnys-carousel__track' },
        {
            allowedBlocks: [ 'tnys/slide' ],
            template: TEMPLATE,
            orientation: 'horizontal'
        }
    );

    const setDeviceValue = ( key, value ) => setAttributes( { [ key ]: { ...attributes[ key ], [ resMode ]: value } } );

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Layout', 'tiny-steps' ) }>
                    <NativeResponsiveControl label={ __( 'Slides per view', 'tiny-steps' ) } props={ props }>
                        <NativeRangeControl
                            value={ perView?.[ resMode ] }
                            onChange={ value => setDeviceValue( 'perView', value ) }
                            min={ 1 }
                            max={ 8 }
                            step={ 0.1 }
                        />
                    </NativeResponsiveControl>
                    <NativeResponsiveControl label={ __( 'Gap (px)', 'tiny-steps' ) } props={ props }>
                        <NativeRangeControl
                            value={ gap?.[ resMode ] }
                            onChange={ value => setDeviceValue( 'gap', value ) }
                            min={ 0 }
                            max={ 80 }
                            step={ 1 }
                        />
                    </NativeResponsiveControl>
                    <NativeResponsiveControl label={ __( 'Slide min height (px)', 'tiny-steps' ) } props={ props }>
                        <NativeRangeControl
                            value={ height?.[ resMode ] }
                            onChange={ value => setDeviceValue( 'height', value ) }
                            min={ 0 }
                            max={ 900 }
                            step={ 1 }
                        />
                    </NativeResponsiveControl>
                    <NativeToggleControl
                        label={ __( 'Show as grid on desktop', 'tiny-steps' ) }
                        help={ __( 'Slides wrap into rows on desktop and slide on tablet and mobile.', 'tiny-steps' ) }
                        checked={ gridDesktop }
                        onChange={ value => setAttributes( { gridDesktop: value } ) }
                    />
                </PanelBody>
                <PanelBody title={ __( 'Behaviour', 'tiny-steps' ) } initialOpen={ false }>
                    <NativeToggleControl
                        label={ __( 'Loop back to the start', 'tiny-steps' ) }
                        checked={ loop }
                        onChange={ value => setAttributes( { loop: value } ) }
                    />
                    <NativeToggleControl
                        label={ __( 'Autoplay', 'tiny-steps' ) }
                        checked={ autoplay }
                        onChange={ value => setAttributes( { autoplay: value } ) }
                    />
                    { autoplay && (
                        <NativeRangeControl
                            label={ __( 'Interval (seconds)', 'tiny-steps' ) }
                            value={ interval }
                            onChange={ value => setAttributes( { interval: value } ) }
                            min={ 2 }
                            max={ 15 }
                            step={ 1 }
                        />
                    ) }
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <div { ...innerBlocksProps } />
            </div>
        </>
    );
}
