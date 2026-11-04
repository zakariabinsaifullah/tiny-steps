/**
 * Alignment classes per device, shared by edit and save.
 */
export function justifyClasses( justify = {} ) {
    return [
        justify.Desktop && `is-justify-${ justify.Desktop }`,
        justify.Tablet && `is-justify-tablet-${ justify.Tablet }`,
        justify.Mobile && `is-justify-mobile-${ justify.Mobile }`
    ].filter( Boolean );
}
