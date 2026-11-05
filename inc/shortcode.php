<?php
/**
 * Posts Grid Shortcode
 *
 * Renders a filterable, paginated post grid via AJAX, and a single featured
 * post card to sit above it.
 *
 * Usage: [tnys_featured_post]
 *        [tnys_posts_grid per_page="9" exclude="featured"]
 */

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'tnys_posts_grid_enqueue_assets' ) ) :
	function tnys_posts_grid_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'tnys-posts-grid',
			get_theme_file_uri( 'assets/css/shortcode.css' ),
			array(),
			$version
		);

		wp_enqueue_script(
			'tnys-posts-grid',
			get_theme_file_uri( 'assets/js/shortcode.js' ),
			array(),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'tnys_posts_grid_arrow_svg' ) ) :
	/**
	 * The design's right arrow, stroked in the current text colour.
	 *
	 * Inlined because this markup also travels over AJAX.
	 *
	 * @param int    $size  Rendered width and height in px.
	 * @param string $class Class for the svg element.
	 */
	function tnys_posts_grid_arrow_svg( $size = 18, $class = 'ipg-arrow' ) {
		return '<svg class="' . esc_attr( $class ) . '" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M3.6 9H14.4M9.9 13.5L14.4 9L9.9 4.5" stroke="currentColor" stroke-width="1.575" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_category_label' ) ) :
	/**
	 * Name of the post's first term in the taxonomy, or ''.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy used for the category label.
	 */
	function tnys_posts_grid_category_label( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_date' ) ) :
	/**
	 * The publish date, shared by the card and the featured post.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $prefix  BEM block name for the class.
	 */
	function tnys_posts_grid_date( $post_id, $prefix ) {
		return '<time class="' . $prefix . '__date" datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . esc_html( get_the_date( 'F j, Y', $post_id ) ) . '</time>';
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_render_post_item' ) ) :
	/**
	 * Renders a single post card: image → category pill → title → excerpt → date.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy used for the category label.
	 */
	function tnys_posts_grid_render_post_item( $post_id, $taxonomy = 'category' ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$excerpt   = wp_trim_words( get_the_excerpt( $post_id ), 18 );
		$category  = tnys_posts_grid_category_label( $post_id, $taxonomy );
		$thumbnail = has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, 'medium_large', array( 'loading' => 'lazy' ) )
			: '';

		$html = '<article class="ipg-card">';

		if ( $thumbnail ) {
			$html .= '<a href="' . esc_url( $permalink ) . '" class="ipg-card__image" tabindex="-1" aria-hidden="true">' . $thumbnail . '</a>';
		}

		$html .= '<div class="ipg-card__body">';

		if ( $category ) {
			$html .= '<span class="ipg-card__category">' . esc_html( $category ) . '</span>';
		}

		$html .= '<h3 class="ipg-card__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h3>';

		if ( $excerpt ) {
			$html .= '<p class="ipg-card__excerpt">' . esc_html( $excerpt ) . '</p>';
		}

		$html .= tnys_posts_grid_date( $post_id, 'ipg-card' );
		$html .= '</div>';
		$html .= '</article>';

		return $html;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_render_posts' ) ) :
	/**
	 * Renders the full grid of post cards for a given WP_Query.
	 *
	 * @param WP_Query $query    The query to render.
	 * @param string   $taxonomy Taxonomy used for the category label.
	 */
	function tnys_posts_grid_render_posts( $query, $taxonomy = 'category' ) {
		if ( ! $query->have_posts() ) {
			return '<p class="ipg-no-posts">' . esc_html__( 'No posts found.', 'tiny-steps' ) . '</p>';
		}

		$html = '<div class="ipg-grid">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$html .= tnys_posts_grid_render_post_item( get_the_ID(), $taxonomy );
		}

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_resolve_category_ids' ) ) :
	/**
	 * Resolves a comma-separated list of term IDs/slugs into an array of term IDs.
	 */
	function tnys_posts_grid_resolve_category_ids( $categories_raw, $taxonomy ) {
		$ids = array();

		foreach ( array_filter( array_map( 'trim', explode( ',', (string) $categories_raw ) ), 'strlen' ) as $token ) {
			$term = is_numeric( $token )
				? get_term_by( 'id', (int) $token, $taxonomy )
				: get_term_by( 'slug', sanitize_title( $token ), $taxonomy );

			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return array_unique( $ids );
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_pagination_range' ) ) :
	/**
	 * Returns an array of page numbers and '...' placeholders.
	 * Always shows first/last page and current page ± 1 neighbour.
	 */
	function tnys_posts_grid_pagination_range( $total_pages, $current_page ) {
		$total_pages  = (int) $total_pages;
		$current_page = (int) $current_page;

		if ( $total_pages <= 7 ) {
			return range( 1, $total_pages );
		}

		$left  = max( 2, $current_page - 1 );
		$right = min( $total_pages - 1, $current_page + 1 );
		$pages = array( 1 );

		if ( $left > 2 ) {
			$pages[] = '...';
		}

		for ( $i = $left; $i <= $right; $i++ ) {
			$pages[] = $i;
		}

		if ( $right < $total_pages - 1 ) {
			$pages[] = '...';
		}

		$pages[] = $total_pages;

		return $pages;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_render_pagination' ) ) :
	/**
	 * Renders prev/next arrows + numbered page buttons with ellipsis.
	 */
	function tnys_posts_grid_render_pagination( $total_pages, $current_page ) {
		$total_pages  = (int) $total_pages;
		$current_page = (int) $current_page;

		if ( $total_pages <= 1 ) {
			return '';
		}

		$svg_prev = tnys_posts_grid_arrow_svg( 18, 'ipg-arrow ipg-arrow--prev' );
		$svg_next = tnys_posts_grid_arrow_svg( 18, 'ipg-arrow' );

		$html = '<div class="ipg-pagination">';

		// Prev.
		$prev_page = max( 1, $current_page - 1 );
		$html     .= '<button class="ipg-page-btn ipg-page-arrow ipg-page-arrow--prev"'
			. ( 1 === $current_page ? ' disabled' : '' )
			. ' data-page="' . $prev_page . '" aria-label="' . esc_attr__( 'Previous page', 'tiny-steps' ) . '">'
			. $svg_prev
			. '</button>';

		// Pages.
		foreach ( tnys_posts_grid_pagination_range( $total_pages, $current_page ) as $page ) {
			if ( '...' === $page ) {
				$html .= '<span class="ipg-page-ellipsis">&hellip;</span>';
			} else {
				$active = ( (int) $page === $current_page ) ? ' active' : '';
				$html  .= '<button class="ipg-page-btn' . $active . '"' . ( $active ? ' aria-current="page"' : '' ) . ' data-page="' . (int) $page . '" aria-label="' . sprintf( esc_attr__( 'Page %d', 'tiny-steps' ), (int) $page ) . '">' . (int) $page . '</button>';
			}
		}

		// Next.
		$next_page = min( $total_pages, $current_page + 1 );
		$html     .= '<button class="ipg-page-btn ipg-page-arrow ipg-page-arrow--next"'
			. ( $current_page === $total_pages ? ' disabled' : '' )
			. ' data-page="' . $next_page . '" aria-label="' . esc_attr__( 'Next page', 'tiny-steps' ) . '">'
			. $svg_next
			. '</button>';

		$html .= '</div>';

		return $html;
	}
endif;


// =============================================================================
// AJAX handler
// =============================================================================

if ( ! function_exists( 'tnys_posts_grid_ajax' ) ) :
	function tnys_posts_grid_ajax() {
		check_ajax_referer( 'tnys_posts_grid_nonce', 'nonce' );

		$cat        = isset( $_POST['cat'] )        ? absint( $_POST['cat'] )                                        : 0;
		$page       = isset( $_POST['page'] )       ? max( 1, absint( $_POST['page'] ) )                             : 1;
		$per_page   = isset( $_POST['per_page'] )   ? min( 50, max( 1, absint( $_POST['per_page'] ) ) )              : 6;
		$post_type  = isset( $_POST['post_type'] )  ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) )       : 'post';
		$taxonomy   = isset( $_POST['taxonomy'] )   ? sanitize_key( $_POST['taxonomy'] )                             : 'category';
		$categories = isset( $_POST['categories'] ) ? sanitize_text_field( wp_unslash( $_POST['categories'] ) )      : '';
		$exclude    = isset( $_POST['exclude'] )    ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['exclude'] ) ) ) ) ) : array();

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$allowed_cat_ids = array_filter( array_map( 'absint', explode( ',', $categories ) ) );

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'post__not_in'   => $exclude,
		);

		if ( $cat > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $cat,
				),
			);
		} elseif ( ! empty( $allowed_cat_ids ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $allowed_cat_ids,
				),
			);
		}

		$query = new WP_Query( $args );

		wp_send_json_success( array(
			'html'         => tnys_posts_grid_render_posts( $query, $taxonomy ),
			'pagination'   => tnys_posts_grid_render_pagination( (int) $query->max_num_pages, $page ),
			'total_pages'  => (int) $query->max_num_pages,
			'current_page' => $page,
		) );
	}
endif;
add_action( 'wp_ajax_tnys_posts_grid', 'tnys_posts_grid_ajax' );
add_action( 'wp_ajax_nopriv_tnys_posts_grid', 'tnys_posts_grid_ajax' );


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'tnys_get_featured_post_id' ) ) :
	/**
	 * The post the featured card shows: the given ID when it is a published
	 * post of the type, otherwise the newest sticky post, otherwise the newest
	 * post. 0 when there is none.
	 *
	 * @param int|string $id        Requested post ID, or empty.
	 * @param string     $post_type Post type.
	 */
	function tnys_get_featured_post_id( $id = '', $post_type = 'post' ) {
		$id = absint( $id );
		if ( $id && 'publish' === get_post_status( $id ) && get_post_type( $id ) === $post_type ) {
			return $id;
		}

		$base = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => 1,
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$sticky = 'post' === $post_type ? get_option( 'sticky_posts', array() ) : array();
		if ( $sticky ) {
			$ids = get_posts( $base + array( 'post__in' => array_map( 'absint', $sticky ) ) );
			if ( $ids ) {
				return (int) $ids[0];
			}
		}

		$ids = get_posts( $base );
		return $ids ? (int) $ids[0] : 0;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_resolve_exclude' ) ) :
	/**
	 * Turns the grid's `exclude` attribute into post IDs. The token `featured`
	 * resolves to whatever [tnys_featured_post] shows without an id.
	 */
	function tnys_posts_grid_resolve_exclude( $exclude_raw, $post_type ) {
		$ids = array();
		foreach ( array_filter( array_map( 'trim', explode( ',', (string) $exclude_raw ) ), 'strlen' ) as $token ) {
			if ( 'featured' === strtolower( $token ) ) {
				$ids[] = tnys_get_featured_post_id( '', $post_type );
			} else {
				$ids[] = absint( $token );
			}
		}
		return array_values( array_unique( array_filter( $ids ) ) );
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_resolve_taxonomy' ) ) :
	/**
	 * Returns the primary hierarchical taxonomy for a post type.
	 */
	function tnys_posts_grid_resolve_taxonomy( $post_type ) {
		foreach ( get_object_taxonomies( $post_type, 'objects' ) as $tax ) {
			if ( $tax->public && $tax->hierarchical ) {
				return $tax->name;
			}
		}
		return 'category';
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_resolve_allowed_cats' ) ) :
	/**
	 * Resolves allowed category IDs, falling back to all non-empty terms.
	 */
	function tnys_posts_grid_resolve_allowed_cats( $categories_raw, $taxonomy ) {
		$ids = tnys_posts_grid_resolve_category_ids( $categories_raw, $taxonomy );

		if ( empty( $ids ) ) {
			$all = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'fields' => 'ids' ) );
			$ids = is_wp_error( $all ) ? array() : array_map( 'intval', $all );
		}

		return $ids;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_render_tabs' ) ) :
	/**
	 * Renders the filter tab buttons for a given set of terms.
	 *
	 * @param array $terms Array of WP_Term objects.
	 */
	function tnys_posts_grid_render_tabs( $terms ) {
		if ( empty( $terms ) ) {
			return '';
		}

		$html  = '<div class="ipg-nav">';
		$html .= '<button type="button" class="ipg-filter-btn active" aria-pressed="true" data-cat="0">' . esc_html__( 'All', 'tiny-steps' ) . '</button>';
		foreach ( $terms as $term ) {
			$html .= '<button type="button" class="ipg-filter-btn" aria-pressed="false" data-cat="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</button>';
		}
		$html .= '</div>';

		return $html;
	}
endif;


if ( ! function_exists( 'tnys_posts_grid_shortcode' ) ) :
	/**
	 * [tnys_posts_grid per_page="9" post_type="post" categories="4,9" exclude="featured" id=""]
	 *
	 * `per_page`   — posts per page (default 9).
	 * `categories` — comma-separated term IDs or slugs; omit for all categories.
	 * `exclude`    — comma-separated post IDs to leave out; the word `featured`
	 *                stands for the post [tnys_featured_post] shows.
	 * `id`         — when set, tabs are omitted and the grid listens for a remote
	 *                tnys:filter event fired by [tnys_posts_tabs for="<id>"].
	 */
	function tnys_posts_grid_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'per_page'   => 9,
				'post_type'  => 'post',
				'categories' => '',
				'exclude'    => '',
				'id'         => '',
			),
			$atts,
			'tnys_posts_grid'
		);

		$per_page  = min( 50, max( 1, (int) $atts['per_page'] ) );
		$post_type = sanitize_key( $atts['post_type'] );
		$grid_id   = sanitize_html_class( $atts['id'] );

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$taxonomy        = tnys_posts_grid_resolve_taxonomy( $post_type );
		$allowed_cat_ids = tnys_posts_grid_resolve_allowed_cats( $atts['categories'], $taxonomy );
		$exclude_ids     = tnys_posts_grid_resolve_exclude( $atts['exclude'], $post_type );

		if ( empty( $allowed_cat_ids ) ) {
			return '<p class="ipg-no-posts">' . esc_html__( 'No categories found.', 'tiny-steps' ) . '</p>';
		}

		// Initial query (page 1, no category filter).
		$query = new WP_Query( array(
			'post_type'      => $post_type,
			'posts_per_page' => $per_page,
			'paged'          => 1,
			'post_status'    => 'publish',
			'post__not_in'   => $exclude_ids,
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $allowed_cat_ids,
				),
			),
		) );

		tnys_posts_grid_enqueue_assets();

		$config = wp_json_encode( array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'tnys_posts_grid_nonce' ),
			'perPage'    => $per_page,
			'postType'   => $post_type,
			'taxonomy'   => $taxonomy,
			'categories' => implode( ',', $allowed_cat_ids ),
			'exclude'    => implode( ',', $exclude_ids ),
		) );

		$grid_id_attr = $grid_id ? ' data-grid-id="' . esc_attr( $grid_id ) . '"' : '';
		$html = '<div class="ipg-wrapper" data-config="' . esc_attr( $config ) . '"' . $grid_id_attr . '>';

		// Embed tabs only in self-contained mode (no id attribute).
		if ( ! $grid_id ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'include'    => $allowed_cat_ids,
				'orderby'    => 'include',
				'hide_empty' => true,
			) );
			$html .= tnys_posts_grid_render_tabs( is_wp_error( $terms ) ? array() : $terms );
		}

		$html .= '<div class="ipg-posts">' . tnys_posts_grid_render_posts( $query, $taxonomy ) . '</div>';
		$html .= '<div class="ipg-pagination-wrap">' . tnys_posts_grid_render_pagination( (int) $query->max_num_pages, 1 ) . '</div>';

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;
add_shortcode( 'tnys_posts_grid', 'tnys_posts_grid_shortcode' );
// Backwards-compatible alias: some content used the callback name as the tag.
add_shortcode( 'tnys_posts_grid_shortcode', 'tnys_posts_grid_shortcode' );


if ( ! function_exists( 'tnys_posts_tabs_shortcode' ) ) :
	/**
	 * [tnys_posts_tabs for="blog" post_type="post" categories="4,9"]
	 *
	 * Renders standalone filter tabs that control a remote [tnys_posts_grid id="blog"].
	 * `for`        — must match the `id` of the target [tnys_posts_grid].
	 * `categories` — must match the `categories` passed to the target grid.
	 * `post_type`  — must match the `post_type` of the target grid.
	 */
	function tnys_posts_tabs_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'for'        => '',
				'post_type'  => 'post',
				'categories' => '',
			),
			$atts,
			'tnys_posts_tabs'
		);

		$grid_id   = sanitize_html_class( $atts['for'] );
		$post_type = sanitize_key( $atts['post_type'] );

		if ( ! $grid_id ) {
			return '';
		}

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$taxonomy        = tnys_posts_grid_resolve_taxonomy( $post_type );
		$allowed_cat_ids = tnys_posts_grid_resolve_allowed_cats( $atts['categories'], $taxonomy );

		if ( empty( $allowed_cat_ids ) ) {
			return '';
		}

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'include'    => $allowed_cat_ids,
			'orderby'    => 'include',
			'hide_empty' => true,
		) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		tnys_posts_grid_enqueue_assets();

		$html  = '<div class="ipg-tabs-remote" data-for="' . esc_attr( $grid_id ) . '">';
		$html .= tnys_posts_grid_render_tabs( $terms );
		$html .= '</div>';

		return $html;
	}
endif;
add_shortcode( 'tnys_posts_tabs', 'tnys_posts_tabs_shortcode' );


if ( ! function_exists( 'tnys_featured_post_shortcode' ) ) :
	/**
	 * [tnys_featured_post id="" post_type="post" label="Featured" button=""]
	 *
	 * One post as a wide card: image on the left; tag, title, excerpt and date
	 * on the right, plus a button when `button` has a label. Without `id` it
	 * shows the newest sticky post, or the newest post when none is sticky.
	 */
	function tnys_featured_post_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'        => '',
				'post_type' => 'post',
				'label'     => __( 'Featured', 'tiny-steps' ),
				'button'    => '',
			),
			$atts,
			'tnys_featured_post'
		);

		$post_type = sanitize_key( $atts['post_type'] );
		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$post_id = tnys_get_featured_post_id( $atts['id'], $post_type );
		if ( ! $post_id ) {
			return '';
		}

		tnys_posts_grid_enqueue_assets();

		$taxonomy  = tnys_posts_grid_resolve_taxonomy( $post_type );
		$permalink = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$excerpt   = wp_trim_words( get_the_excerpt( $post_id ), 24 );

		$html = '<article class="ipg-featured">';

		if ( has_post_thumbnail( $post_id ) ) {
			$html .= '<a href="' . esc_url( $permalink ) . '" class="ipg-featured__image" tabindex="-1" aria-hidden="true">';
			$html .= get_the_post_thumbnail( $post_id, 'large' );
			$html .= '</a>';
		}

		$html .= '<div class="ipg-featured__body">';

		if ( '' !== trim( $atts['label'] ) ) {
			$html .= '<span class="ipg-featured__tag">' . esc_html( $atts['label'] ) . '</span>';
		}

		$html .= '<h3 class="ipg-featured__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h3>';

		if ( $excerpt ) {
			$html .= '<p class="ipg-featured__excerpt">' . esc_html( $excerpt ) . '</p>';
		}

		$html .= tnys_posts_grid_date( $post_id, 'ipg-featured' );

		/*
		 * `wp-element-button` is what theme.json's styles.elements.button
		 * compiles to, so this is the theme's primary button by construction.
		 */
		if ( '' !== trim( $atts['button'] ) ) {
			$html .= '<a class="wp-element-button ipg-featured__button" href="' . esc_url( $permalink ) . '" aria-label="' . esc_attr( sprintf( /* translators: %s: post title */ __( 'Read more: %s', 'tiny-steps' ), $title ) ) . '">' . esc_html( $atts['button'] ) . '</a>';
		}

		$html .= '</div>';
		$html .= '</article>';

		return $html;
	}
endif;
add_shortcode( 'tnys_featured_post', 'tnys_featured_post_shortcode' );


if ( ! function_exists( 'tnys_related_posts_shortcode' ) ) :
	/**
	 * [tnys_related_posts count="3" post_type="post"]
	 *
	 * Cards for posts that share a category with the current post, newest
	 * first, topped up with the newest other posts when there are not enough.
	 * Meant for the single post template; outside a single post it simply
	 * lists the newest posts.
	 */
	function tnys_related_posts_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'count'     => 3,
				'post_type' => 'post',
			),
			$atts,
			'tnys_related_posts'
		);

		$count     = min( 12, max( 1, (int) $atts['count'] ) );
		$post_type = sanitize_key( $atts['post_type'] );
		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$taxonomy = tnys_posts_grid_resolve_taxonomy( $post_type );
		$current  = is_singular() ? get_queried_object_id() : 0;
		$exclude  = $current ? array( $current ) : array();
		$ids      = array();

		$base = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( $current ) {
			$terms = wp_get_post_terms( $current, $taxonomy, array( 'fields' => 'ids' ) );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$ids = get_posts( $base + array(
					'posts_per_page' => $count,
					'post__not_in'   => $exclude,
					'tax_query'      => array(
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => $terms,
						),
					),
				) );
			}
		}

		if ( count( $ids ) < $count ) {
			$ids = array_merge( $ids, get_posts( $base + array(
				'posts_per_page' => $count - count( $ids ),
				'post__not_in'   => array_merge( $exclude, $ids ),
			) ) );
		}

		if ( empty( $ids ) ) {
			return '';
		}

		tnys_posts_grid_enqueue_assets();

		$html = '<div class="ipg-grid ipg-related">';
		foreach ( $ids as $id ) {
			$html .= tnys_posts_grid_render_post_item( $id, $taxonomy );
		}
		$html .= '</div>';

		return $html;
	}
endif;
add_shortcode( 'tnys_related_posts', 'tnys_related_posts_shortcode' );
