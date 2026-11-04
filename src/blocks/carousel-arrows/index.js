import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
        <path d="m10 18-6-6 6-6 1.4 1.45L7.85 11H11v2H7.85l3.55 3.55L10 18Zm4 0-1.4-1.45L16.15 13H13v-2h3.15L12.6 7.45 14 6l6 6-6 6Z" />
    </svg>
);

registerBlockType( metadata.name, {
    icon: inlineIcon,
    edit: Edit,
    save
} );
