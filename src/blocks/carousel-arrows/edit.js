import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { NativeRangeControl, NativeResponsiveControl, NativeSelectControl, NativeTextControl, NativeToggleControl } from '../../components';
import Arrows from './arrows';
import { justifyClasses } from './utils';

const JUSTIFY_OPTIONS = [
    { label: __( 'Default', 'tiny-steps' ), value: '' },
    { label: __( 'Left', 'tiny-steps' ), value: 'left' },
    { label: __( 'Center', 'tiny-steps' ), value: 'center' },
    { label: __( 'Right', 'tiny-steps' ), value: 'right' }
];

export default function Edit( props ) {
    const { attributes, setAttributes } = props;
    const { target, justify, size, hideWhenStatic, resMode = 'Desktop' } = attributes;

    const blockProps = useBlockProps( {
        className: justifyClasses( justify ).join( ' ' ),
        style: size && size !== 48 ? { '--tnys-arrow-size': `${ size }px` } : undefined
    } );

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Settings', 'tiny-steps' ) }>
                    <NativeTextControl
                        label={ __( 'Carousel HTML anchor', 'tiny-steps' ) }
                        help={ __(
                            'The HTML anchor of the Carousel to control (Carousel → Advanced → HTML anchor). Leave empty to control the next carousel on the page.',
                            'tiny-steps'
                        ) }
                        value={ target }
                        onChange={ value => setAttributes( { target: value.replace( /^#/, '' ).trim() } ) }
                    />
                    <NativeResponsiveControl label={ __( 'Alignment', 'tiny-steps' ) } props={ props }>
                        <NativeSelectControl
                            value={ justify?.[ resMode ] || '' }
                            options={ JUSTIFY_OPTIONS }
                            onChange={ value => setAttributes( { justify: { ...justify, [ resMode ]: value || undefined } } ) }
                        />
                    </NativeResponsiveControl>
                    <NativeRangeControl
                        label={ __( 'Button size (px)', 'tiny-steps' ) }
                        value={ size }
                        onChange={ value => setAttributes( { size: value } ) }
                        min={ 32 }
                        max={ 64 }
                        step={ 1 }
                        resetFallbackValue={ 48 }
                    />
                    <NativeToggleControl
                        label={ __( 'Hide when there is nothing to scroll', 'tiny-steps' ) }
                        checked={ hideWhenStatic }
                        onChange={ value => setAttributes( { hideWhenStatic: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <Arrows />
            </div>
        </>
    );
}
