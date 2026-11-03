<?php
/**
 * Core Block Styles
 *
 * Registers custom style variations for core (and third-party) blocks.
 *
 * @package Tiny_Steps
 */

if ( ! function_exists( 'tnys_block_styles' ) ) :
	/**
	 * Registers all custom block style variations for the theme.
	 */
	function tnys_block_styles() {
		register_block_style(
			'core/group',
			array(
				'name'  => 'wrap-mobile',
				'label' => __( 'Wrap Mobile', 'tiny-steps' ),
			)
		);


		register_block_style(
			'core/button',
			array(
				'name'  => 'alternative',
				'label' => __( 'Alternative', 'tiny-steps' ),
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'  => 'link',
				'label' => __( 'Link', 'tiny-steps' ),
			)
		);
	}
endif;
add_action( 'init', 'tnys_block_styles' );
