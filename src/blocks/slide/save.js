import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import SlideMedia from './media';

export default function save( { attributes } ) {
    const { imageId, imageUrl, imageAlt, imagePosition, shade } = attributes;

    const blockProps = useBlockProps.save( {
        className: [ imageUrl && 'has-image', shade && 'has-shade' ].filter( Boolean ).join( ' ' ) || undefined,
        role: 'group',
        'aria-roledescription': 'slide'
    } );

    const { children, ...innerBlocksProps } = useInnerBlocksProps.save( blockProps );

    return (
        <div { ...innerBlocksProps }>
            <SlideMedia imageUrl={ imageUrl } imageId={ imageId } imageAlt={ imageAlt } imagePosition={ imagePosition } shade={ shade } />
            { children }
        </div>
    );
}
