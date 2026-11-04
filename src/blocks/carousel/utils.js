/**
 * CSS custom properties shared by edit and save.
 */
export function carouselStyle( { perView = {}, gap = {}, height = {} } ) {
    const style = {};
    const devices = { Desktop: 'd', Tablet: 't', Mobile: 'm' };

    Object.entries( devices ).forEach( ( [ device, key ] ) => {
        if ( perView[ device ] ) {
            style[ `--tnys-per-view-${ key }` ] = perView[ device ];
        }
        if ( gap[ device ] !== undefined && gap[ device ] !== null && gap[ device ] !== '' ) {
            style[ `--tnys-gap-${ key }` ] = `${ gap[ device ] }px`;
        }
        if ( height[ device ] ) {
            style[ `--tnys-slide-h-${ key }` ] = `${ height[ device ] }px`;
        }
    } );

    return style;
}

export function carouselClasses( { gridDesktop } ) {
    return [ 'tnys-carousel', gridDesktop && 'is-grid-desktop' ].filter( Boolean ).join( ' ' );
}
