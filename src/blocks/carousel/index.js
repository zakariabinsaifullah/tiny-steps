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
        <path d="M7 19V5h10v14H7Zm-5-2V7h3v10H2Zm17 0V7h3v10h-3ZM9 17h6V7H9v10Z" />
    </svg>
);

registerBlockType( metadata.name, {
    icon: inlineIcon,
    edit: Edit,
    save
} );
