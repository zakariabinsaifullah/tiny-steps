import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import Arrows from './arrows';
import { justifyClasses } from './utils';

export default function save( { attributes } ) {
    const { target, justify, hideWhenStatic } = attributes;

    const blockProps = useBlockProps.save( {
        className: [ ...justifyClasses( justify ), hideWhenStatic && 'hide-when-static' ].filter( Boolean ).join( ' ' ),
        'data-target': target || undefined
    } );

    return (
        <div { ...blockProps }>
            <Arrows />
        </div>
    );
}
