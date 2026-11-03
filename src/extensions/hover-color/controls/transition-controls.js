/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { NativeRangeControl, NativeSelectControl } from '../../../components';

const HoverTransitionControls = ( { attributes, setAttributes } ) => {
    const {
        hoverTransitionDuration,
        hoverTransitionTiming,
        hoverTextColor,
        hoverBackgroundColor,
        hoverBorderColor,
        customHoverTextColor,
        customHoverBackgroundColor,
        customHoverBorderColor
    } = attributes;

    const hasHoverColor =
        customHoverBorderColor ||
        customHoverTextColor ||
        customHoverBackgroundColor ||
        hoverTextColor ||
        hoverBackgroundColor ||
        hoverBorderColor;

    if ( ! hasHoverColor ) {
        return null;
    }

    const timingOptions = [
        { label: __( 'Standard', 'tiny-steps' ), value: 'cubic-bezier(0.4, 0, 0.2, 1)' },
        { label: __( 'Ease', 'tiny-steps' ), value: 'ease' },
        { label: __( 'Linear', 'tiny-steps' ), value: 'linear' },
        { label: __( 'Ease In', 'tiny-steps' ), value: 'ease-in' },
        { label: __( 'Ease Out', 'tiny-steps' ), value: 'ease-out' },
        { label: __( 'Ease In Out', 'tiny-steps' ), value: 'ease-in-out' }
    ];

    return (
        <div
            className="tnys-hover-color__transition-controls"
            style={ {
                gridTemplateColumns: 'repeat(2, minmax(0px, 1fr))',
                gap: 'calc(16px)',
                gridColumn: '1 / -1'
            } }
        >
            <NativeRangeControl
                label={ __( 'Transition Duration', 'tiny-steps' ) }
                value={ hoverTransitionDuration }
                onChange={ value => setAttributes( { hoverTransitionDuration: value } ) }
                min={ 0 }
                max={ 2000 }
                step={ 50 }
                resetFallbackValue={ 200 }
                help={ __( 'Duration in milliseconds', 'tiny-steps' ) }
            />
            <NativeSelectControl
                label={ __( 'Timing Function', 'tiny-steps' ) }
                value={ hoverTransitionTiming }
                options={ timingOptions }
                onChange={ value => setAttributes( { hoverTransitionTiming: value } ) }
            />
        </div>
    );
};

export default HoverTransitionControls;
