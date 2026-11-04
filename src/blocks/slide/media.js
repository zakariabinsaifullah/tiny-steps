/**
 * Background image and shade, shared by edit and save.
 */
export default function SlideMedia( { imageUrl, imageId, imageAlt, imagePosition, shade } ) {
    return (
        <>
            { imageUrl && (
                <img
                    className={ `tnys-slide__image${ imageId ? ` wp-image-${ imageId }` : '' }` }
                    src={ imageUrl }
                    alt={ imageAlt }
                    style={ imagePosition && imagePosition !== '50% 50%' ? { objectPosition: imagePosition } : undefined }
                    loading="lazy"
                    decoding="async"
                />
            ) }
            { shade && <span className="tnys-slide__shade" aria-hidden="true" /> }
        </>
    );
}
