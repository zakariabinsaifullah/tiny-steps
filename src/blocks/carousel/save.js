import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { carouselStyle, carouselClasses } from './utils';

export default function save( { attributes } ) {
    const { autoplay, interval, loop } = attributes;

    const blockProps = useBlockProps.save( {
        className: carouselClasses( attributes ),
        style: carouselStyle( attributes ),
        'data-autoplay': autoplay ? interval * 1000 : undefined,
        'data-loop': loop ? 'true' : undefined
    } );

    const innerBlocksProps = useInnerBlocksProps.save( {
        className: 'tnys-carousel__track',
        tabIndex: 0,
        role: 'region',
        'aria-roledescription': 'carousel'
    } );

    return (
        <div { ...blockProps }>
            <div { ...innerBlocksProps } />
        </div>
    );
}
