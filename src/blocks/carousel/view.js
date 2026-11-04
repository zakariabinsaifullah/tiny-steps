/**
 * Tiny Steps carousel: scroll-snap track with prev/next, autoplay and
 * remote Carousel Arrows that target a carousel by its HTML anchor.
 */
const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

function initCarousel( carousel ) {
    const track = carousel.querySelector( ':scope > .tnys-carousel__track' );
    if ( ! track || carousel.tnysCarousel ) {
        return;
    }

    const loop = carousel.dataset.loop === 'true';
    const listeners = new Set();

    const slides = () => Array.from( track.children );
    const maxScroll = () => track.scrollWidth - track.clientWidth;
    const atStart = () => track.scrollLeft <= 2;
    const atEnd = () => track.scrollLeft >= maxScroll() - 2;
    const canScroll = () => maxScroll() > 2;

    // One step is one slide plus the gap.
    const step = () => {
        const [ first, second ] = slides();
        if ( ! first ) {
            return track.clientWidth;
        }
        return second ? second.offsetLeft - first.offsetLeft : first.offsetWidth;
    };

    const go = direction => {
        if ( ! canScroll() ) {
            return;
        }
        if ( loop && direction > 0 && atEnd() ) {
            track.scrollTo( { left: 0 } );
        } else if ( loop && direction < 0 && atStart() ) {
            track.scrollTo( { left: maxScroll() } );
        } else {
            track.scrollBy( { left: direction * step() } );
        }
    };

    const notify = () => listeners.forEach( fn => fn( api ) );

    const api = {
        prev: () => go( -1 ),
        next: () => go( 1 ),
        atStart,
        atEnd,
        canScroll,
        loop,
        subscribe: fn => {
            listeners.add( fn );
            fn( api );
        }
    };

    let frame;
    const onChange = () => {
        cancelAnimationFrame( frame );
        frame = requestAnimationFrame( notify );
    };
    track.addEventListener( 'scroll', onChange, { passive: true } );
    new ResizeObserver( onChange ).observe( track );

    // Arrow keys move one slide when the track has focus.
    track.addEventListener( 'keydown', event => {
        if ( event.key === 'ArrowRight' || event.key === 'ArrowLeft' ) {
            event.preventDefault();
            go( event.key === 'ArrowRight' ? 1 : -1 );
        }
    } );

    // Autoplay pauses on hover, focus and when the tab is hidden.
    const delay = parseInt( carousel.dataset.autoplay, 10 );
    if ( delay && ! reducedMotion.matches ) {
        let paused = false;
        const pause = () => ( paused = true );
        const resume = () => ( paused = false );
        carousel.addEventListener( 'mouseenter', pause );
        carousel.addEventListener( 'mouseleave', resume );
        carousel.addEventListener( 'focusin', pause );
        carousel.addEventListener( 'focusout', resume );
        setInterval( () => {
            if ( paused || document.hidden ) {
                return;
            }
            if ( atEnd() ) {
                track.scrollTo( { left: 0 } );
            } else {
                go( 1 );
            }
        }, delay );
    }

    carousel.tnysCarousel = api;
}

function findTarget( arrows ) {
    const id = arrows.dataset.target;
    if ( id ) {
        const el = document.getElementById( id );
        const carousel = el && ( el.matches( '.wp-block-tnys-carousel' ) ? el : el.querySelector( '.wp-block-tnys-carousel' ) );
        if ( carousel ) {
            return carousel;
        }
    }

    // No target set: use the first carousel that comes after the arrows.
    return Array.from( document.querySelectorAll( '.wp-block-tnys-carousel' ) ).find(
        carousel => arrows.compareDocumentPosition( carousel ) & Node.DOCUMENT_POSITION_FOLLOWING
    );
}

function initArrows( arrows ) {
    const carousel = findTarget( arrows );
    if ( ! carousel || ! carousel.tnysCarousel ) {
        return;
    }

    const prev = arrows.querySelector( '.tnys-carousel-arrows__prev' );
    const next = arrows.querySelector( '.tnys-carousel-arrows__next' );
    const track = carousel.querySelector( ':scope > .tnys-carousel__track' );

    if ( track ) {
        if ( ! track.id ) {
            track.id = `${ carousel.id || 'tnys-carousel' }-track-${ Math.random().toString( 36 ).slice( 2, 8 ) }`;
        }
        prev?.setAttribute( 'aria-controls', track.id );
        next?.setAttribute( 'aria-controls', track.id );
    }

    prev?.addEventListener( 'click', () => carousel.tnysCarousel.prev() );
    next?.addEventListener( 'click', () => carousel.tnysCarousel.next() );

    carousel.tnysCarousel.subscribe( api => {
        const scrollable = api.canScroll();
        arrows.classList.toggle( 'is-static', ! scrollable );
        if ( prev ) {
            prev.disabled = ! scrollable || ( ! api.loop && api.atStart() );
        }
        if ( next ) {
            next.disabled = ! scrollable || ( ! api.loop && api.atEnd() );
        }
    } );
}

function init() {
    document.querySelectorAll( '.wp-block-tnys-carousel' ).forEach( initCarousel );
    document.querySelectorAll( '.wp-block-tnys-carousel-arrows' ).forEach( initArrows );
}

if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
} else {
    init();
}
