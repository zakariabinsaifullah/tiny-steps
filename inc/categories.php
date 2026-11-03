<?php
/**
 * Block & Pattern Categories
 *
 * Registers custom block categories and block pattern categories
 * used throughout this theme.
 *
 * @package Tiny_Steps
 */

if ( ! function_exists( 'tnys_block_categories' ) ) :
	/**
	 * Adds the "Tiny Steps" category to the block inserter.
	 *
	 * @param  array                   $block_categories     Existing block categories.
	 * @param  WP_Block_Editor_Context $block_editor_context Current editor context.
	 * @return array
	 */
	function tnys_block_categories( $block_categories, $block_editor_context ) {
		return array_merge(
			array(
				array(
					'slug'  => 'tnys',
					'title' => __( 'Tiny Steps', 'tiny-steps' ),
				),
			),
			$block_categories

		);
	}
endif;
add_filter( 'block_categories_all', 'tnys_block_categories', 10, 2 );


if ( ! function_exists( 'tnys_pattern_categories' ) ) :
	/**
	 * Registers the "Tiny Steps" block pattern category.
	 */
	function tnys_pattern_categories() {
		register_block_pattern_category(
			'tnys',
			array(
				'label'       => __( 'Tiny Steps', 'tiny-steps' ),
				'description' => __( 'A collection of Tiny Steps patterns.', 'tiny-steps' ),
			)
		);
	}
endif;
add_action( 'init', 'tnys_pattern_categories' );


// ── ID column on the Categories admin screen ──────────────────────────────────

if ( ! function_exists( 'tnys_category_columns' ) ) :
	/**
	 * Adds an "ID" column to the Categories list table.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function tnys_category_columns( $columns ) {
		$columns['tnys_id'] = __( 'ID', 'tiny-steps' );
		return $columns;
	}
endif;
add_filter( 'manage_edit-category_columns', 'tnys_category_columns' );

/**
 * Caps the ID column width on the Categories list table.
 */
function tnys_category_column_width() {
	?>
	<style>
		.wp-list-table .column-tnys_id {
			width: 100px;
			max-width: 100px;
		}
	</style>
	<?php
}
add_action( 'admin_head-edit-tags.php', 'tnys_category_column_width' );


if ( ! function_exists( 'tnys_category_column_content' ) ) :
	/**
	 * Outputs the term ID for the custom column.
	 *
	 * @param  string $content      Column content.
	 * @param  string $column_name  Column key.
	 * @param  int    $term_id      Term ID.
	 * @return string
	 */
	function tnys_category_column_content( $content, $column_name, $term_id ) {
		if ( 'tnys_id' === $column_name ) {
			return (string) $term_id;
		}
		return $content;
	}
endif;
add_filter( 'manage_category_custom_column', 'tnys_category_column_content', 10, 3 );
