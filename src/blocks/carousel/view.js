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

    // Each slide's start, capped at the end of the track, without duplicates.
    const snapPositions = () => {
        const items = slides();
        const max = maxScroll();
        if ( ! items.length ) {
            return [ 0 ];
        }
        const origin = items[ 0 ].offsetLeft;
        const positions = [];
        items.forEach( item => {
            const left = Math.min( item.offsetLeft - origin, max );
            if ( ! positions.length || left - positions[ positions.length - 1 ] > 2 ) {
                positions.push( left );
            }
        } );
        return positions;
    };

    // Where an in-progress smooth scroll is heading, so quick repeated
    // clicks add up instead of restarting from a mid-animation position.
    let target = null;
    let settleTimer;

    const go = direction => {
        if ( ! canScroll() ) {
            return;
        }
        const positions = snapPositions();
        const from = target ?? track.scrollLeft;

        // The snap position closest to where the track is (or is heading).
        let index = 0;
        positions.forEach( ( left, i ) => {
            if ( Math.abs( left - from ) < Math.abs( positions[ index ] - from ) ) {
                index = i;
            }
        } );

        let next = index + direction;
        if ( next < 0 || next >= positions.length ) {
            if ( ! loop ) {
                return;
            }
            next = next < 0 ? positions.length - 1 : 0;
        }

        target = positions[ next ];
        track.scrollTo( { left: target } );
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
    track.addEventListener(
        'scroll',
        () => {
            clearTimeout( settleTimer );
            settleTimer = setTimeout( () => ( target = null ), 150 );
        },
        { passive: true }
    );
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
                target = 0;
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
