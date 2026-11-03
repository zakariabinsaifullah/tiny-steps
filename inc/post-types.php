<?php
/**
 * Custom Post Types
 *
 * Registers the content types owned by this theme.
 *
 * @package Tiny_Steps
 */

if ( ! function_exists( 'tnys_register_open_role_post_type' ) ) :
	/**
	 * Registers the Open Role post type for job listings.
	 *
	 * Roles have no front-end single view, so `publicly_queryable`, `query_var`
	 * and `rewrite` stay off and no archive is registered. `show_in_rest` keeps
	 * the block editor available in the admin.
	 */
	function tnys_register_open_role_post_type() {
		$labels = array(
			'name'                   => _x( 'Open Roles', 'post type general name', 'tiny-steps' ),
			'singular_name'          => _x( 'Open Role', 'post type singular name', 'tiny-steps' ),
			'menu_name'              => _x( 'Open Roles', 'admin menu', 'tiny-steps' ),
			'name_admin_bar'         => _x( 'Open Role', 'add new on admin bar', 'tiny-steps' ),
			'add_new'                => __( 'Add Open Role', 'tiny-steps' ),
			'add_new_item'           => __( 'Add New Open Role', 'tiny-steps' ),
			'new_item'               => __( 'New Open Role', 'tiny-steps' ),
			'edit_item'              => __( 'Edit Open Role', 'tiny-steps' ),
			'view_item'              => __( 'View Open Role', 'tiny-steps' ),
			'view_items'             => __( 'View Open Roles', 'tiny-steps' ),
			'all_items'              => __( 'All Open Roles', 'tiny-steps' ),
			'search_items'           => __( 'Search Open Roles', 'tiny-steps' ),
			'parent_item_colon'      => __( 'Parent Open Roles:', 'tiny-steps' ),
			'not_found'              => __( 'No open roles found.', 'tiny-steps' ),
			'not_found_in_trash'     => __( 'No open roles found in Trash.', 'tiny-steps' ),
			'archives'               => __( 'Open Role Archives', 'tiny-steps' ),
			'attributes'             => __( 'Open Role Attributes', 'tiny-steps' ),
			'insert_into_item'       => __( 'Insert into open role', 'tiny-steps' ),
			'uploaded_to_this_item'  => __( 'Uploaded to this open role', 'tiny-steps' ),
			'filter_items_list'      => __( 'Filter open roles list', 'tiny-steps' ),
			'items_list_navigation'  => __( 'Open roles list navigation', 'tiny-steps' ),
			'items_list'             => __( 'Open roles list', 'tiny-steps' ),
			'item_published'         => __( 'Open role published.', 'tiny-steps' ),
			'item_updated'           => __( 'Open role updated.', 'tiny-steps' ),
			'item_scheduled'         => __( 'Open role scheduled.', 'tiny-steps' ),
			'item_reverted_to_draft' => __( 'Open role reverted to draft.', 'tiny-steps' ),
			'item_link'              => _x( 'Open Role Link', 'navigation link block title', 'tiny-steps' ),
			'item_link_description'  => _x( 'A link to an open role.', 'navigation link block description', 'tiny-steps' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Open roles at Tiny Steps.', 'tiny-steps' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 22,
			'menu_icon'           => 'dashicons-businessperson',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'open-role', $args );
	}
endif;
add_action( 'init', 'tnys_register_open_role_post_type' );


if ( ! function_exists( 'tnys_open_role_title_placeholder' ) ) :
	/**
	 * Replaces the "Add title" placeholder on the Open Role editing screen.
	 *
	 * Core passes this filter through to the block editor as
	 * `titlePlaceholder`, so the one filter covers both editors.
	 *
	 * @param string  $text Current placeholder text.
	 * @param WP_Post $post Post being edited.
	 * @return string
	 */
	function tnys_open_role_title_placeholder( $text, $post ) {
		if ( $post instanceof WP_Post && 'open-role' === $post->post_type ) {
			return __( 'Role title', 'tiny-steps' );
		}

		return $text;
	}
endif;
add_filter( 'enter_title_here', 'tnys_open_role_title_placeholder', 10, 2 );


// ── Open Role meta fields ─────────────────────────────────────────────────────

// ── Taxonomies ────────────────────────────────────────────────────────────────

if ( ! function_exists( 'tnys_register_open_role_taxonomies' ) ) :
	/**
	 * Registers Job Type for open roles.
	 *
	 * Hierarchical so it presents the checkbox UI of a fixed vocabulary (Tax,
	 * Accounting, Advisory …). It is shown on the role card and can limit the
	 * [opening_roles] shortcode. No public archive: open roles have no
	 * front-end views at all.
	 */
	function tnys_register_open_role_taxonomies() {
		register_taxonomy(
			'tnys-job-type',
			'open-role',
			array(
				'labels'            => array(
					'name'              => _x( 'Job Types', 'taxonomy general name', 'tiny-steps' ),
					'singular_name'     => _x( 'Job Type', 'taxonomy singular name', 'tiny-steps' ),
					'menu_name'         => __( 'Job Types', 'tiny-steps' ),
					'all_items'         => __( 'All Job Types', 'tiny-steps' ),
					'edit_item'         => __( 'Edit Job Type', 'tiny-steps' ),
					'update_item'       => __( 'Update Job Type', 'tiny-steps' ),
					'add_new_item'      => __( 'Add New Job Type', 'tiny-steps' ),
					'new_item_name'     => __( 'New Job Type Name', 'tiny-steps' ),
					'search_items'      => __( 'Search Job Types', 'tiny-steps' ),
					'parent_item'       => __( 'Parent Job Type', 'tiny-steps' ),
					'parent_item_colon' => __( 'Parent Job Type:', 'tiny-steps' ),
					'not_found'         => __( 'No job types found.', 'tiny-steps' ),
				),
				'hierarchical'      => true,
				'public'            => false,
				'publicly_queryable' => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_in_rest'      => false,
				'query_var'         => false,
				'rewrite'           => false,
			)
		);
	}
endif;
add_action( 'init', 'tnys_register_open_role_taxonomies' );


// ── Meta ──────────────────────────────────────────────────────────────────────

if ( ! function_exists( 'tnys_register_open_role_meta' ) ) :
	/**
	 * Registers the open role meta.
	 *
	 * A role keeps four settings: Active, Vacancies, Apply Link and its Job
	 * Types (a taxonomy). Location, the symbolic icon and keyword tags were
	 * dropped with the job board's search bar.
	 */
	function tnys_register_open_role_meta() {
		$auth = function () {
			return current_user_can( 'edit_posts' );
		};

		register_post_meta(
			'open-role',
			'tnys_role_vacancies',
			array(
				'single'            => true,
				'type'              => 'integer',
				'show_in_rest'      => false,
				'default'           => 0,
				'description'       => __( 'How many positions are open for this role.', 'tiny-steps' ),
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'tnys_role_apply_link',
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => false,
				'description'       => __( 'Where the Apply button sends candidates.', 'tiny-steps' ),

				/*
				 * Stored as plain text, not run through sanitize_url, so a
				 * relative path, an anchor or a mailto: can be entered without
				 * being silently emptied. Escaping still happens on output,
				 * where esc_url() drops anything dangerous.
				 */
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'tnys_role_active',
			array(
				'single'            => true,
				'type'              => 'boolean',
				'show_in_rest'      => false,
				'default'           => true,
				'description'       => __( 'Whether the role is listed by the shortcode.', 'tiny-steps' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth,
			)
		);
	}
endif;
add_action( 'init', 'tnys_register_open_role_meta' );


// ── Edit screen ───────────────────────────────────────────────────────────────

if ( ! function_exists( 'tnys_add_open_role_meta_box' ) ) :
	/**
	 * Adds the open role details box.
	 */
	function tnys_add_open_role_meta_box() {
		add_meta_box(
			'tnys-open-role-details',
			__( 'Role Details', 'tiny-steps' ),
			'tnys_render_open_role_meta_box',
			'open-role',
			'normal',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'tnys_add_open_role_meta_box' );


if ( ! function_exists( 'tnys_render_open_role_meta_box' ) ) :
	/**
	 * Renders the open role fields.
	 *
	 * @param WP_Post $post Current post.
	 */
	function tnys_render_open_role_meta_box( $post ) {
		wp_nonce_field( 'tnys_save_open_role', 'tnys_open_role_nonce' );

		$vacancies  = get_post_meta( $post->ID, 'tnys_role_vacancies', true );
		$apply_link = get_post_meta( $post->ID, 'tnys_role_apply_link', true );

		// A brand new draft has no meta row yet, and register_post_meta
		// defaults do not apply to one, so active starts checked.
		$active = metadata_exists( 'post', $post->ID, 'tnys_role_active' )
			? (bool) get_post_meta( $post->ID, 'tnys_role_active', true )
			: true;

		?>
		<p>
			<label>
				<input type="checkbox" name="tnys_role_active" value="1" <?php checked( $active ); ?> />
				<strong><?php esc_html_e( 'Role is active', 'tiny-steps' ); ?></strong>
			</label>
			<span class="description"><?php esc_html_e( 'Only active roles are listed by the shortcode.', 'tiny-steps' ); ?></span>
		</p>

		<p>
			<label for="tnys-role-vacancies"><strong><?php esc_html_e( 'Vacancies', 'tiny-steps' ); ?></strong></label><br />
			<input
				type="number"
				id="tnys-role-vacancies"
				name="tnys_role_vacancies"
				class="small-text"
				min="0"
				step="1"
				value="<?php echo esc_attr( '' === $vacancies ? '0' : (string) (int) $vacancies ); ?>"
			/>
			<span class="description"><?php esc_html_e( 'Shown on the card as "2 open". Leave at 0 to hide it.', 'tiny-steps' ); ?></span>
		</p>

		<p>
			<label for="tnys-role-apply-link"><strong><?php esc_html_e( 'Apply Link', 'tiny-steps' ); ?></strong></label><br />
			<?php // `text`, not `url`, so the browser does not refuse to save a relative path or an anchor. ?>
			<input
				type="text"
				id="tnys-role-apply-link"
				name="tnys_role_apply_link"
				class="widefat"
				value="<?php echo esc_attr( $apply_link ); ?>"
				placeholder="https://"
			/>
			<span class="description"><?php esc_html_e( 'Where "Apply now" sends candidates: a job board, an email (mailto:) or a page on this site. Leave empty to hide the link.', 'tiny-steps' ); ?></span>
		</p>
		<p class="description"><?php esc_html_e( 'Set the role\'s Job Types in the box on the right.', 'tiny-steps' ); ?></p>
		<?php
	}
endif;


if ( ! function_exists( 'tnys_save_open_role_meta' ) ) :
	/**
	 * Saves the open role fields.
	 *
	 * @param int $post_id Post ID.
	 */
	function tnys_save_open_role_meta( $post_id ) {
		if ( ! isset( $_POST['tnys_open_role_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tnys_open_role_nonce'] ) ), 'tnys_save_open_role' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta(
			$post_id,
			'tnys_role_vacancies',
			isset( $_POST['tnys_role_vacancies'] ) ? absint( wp_unslash( $_POST['tnys_role_vacancies'] ) ) : 0
		);

		update_post_meta(
			$post_id,
			'tnys_role_apply_link',
			isset( $_POST['tnys_role_apply_link'] ) ? sanitize_text_field( wp_unslash( $_POST['tnys_role_apply_link'] ) ) : ''
		);

		update_post_meta( $post_id, 'tnys_role_active', isset( $_POST['tnys_role_active'] ) );
	}
endif;

add_action( 'save_post_open-role', 'tnys_save_open_role_meta' );


// ── Active toggle column on the Open Roles admin screen ───────────────────────

if ( ! function_exists( 'tnys_open_role_is_active' ) ) :
	/**
	 * Whether a role is active.
	 *
	 * A role saved before this field existed has no stored value; those count
	 * as active, matching the field's default.
	 *
	 * @param  int $post_id Post ID.
	 * @return bool
	 */
	function tnys_open_role_is_active( $post_id ) {
		if ( ! metadata_exists( 'post', $post_id, 'tnys_role_active' ) ) {
			return true;
		}

		return (bool) get_post_meta( $post_id, 'tnys_role_active', true );
	}
endif;


if ( ! function_exists( 'tnys_open_role_columns' ) ) :
	/**
	 * Adds an "Active" column, placed just after the title.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function tnys_open_role_columns( $columns ) {
		$reordered = array();

		foreach ( $columns as $key => $label ) {
			$reordered[ $key ] = $label;

			if ( 'title' === $key ) {
				$reordered['tnys_role_active'] = __( 'Active', 'tiny-steps' );
			}
		}

		// If there was no title column to anchor to, fall back to appending.
		if ( ! isset( $reordered['tnys_role_active'] ) ) {
			$reordered['tnys_role_active'] = __( 'Active', 'tiny-steps' );
		}

		return $reordered;
	}
endif;
add_filter( 'manage_open-role_posts_columns', 'tnys_open_role_columns' );


if ( ! function_exists( 'tnys_open_role_column_content' ) ) :
	/**
	 * Renders the toggle switch in the Active column.
	 *
	 * @param  string $column_name Column key.
	 * @param  int    $post_id     Post ID.
	 * @return void
	 */
	function tnys_open_role_column_content( $column_name, $post_id ) {
		if ( 'tnys_role_active' !== $column_name ) {
			return;
		}

		$active   = tnys_open_role_is_active( $post_id );
		$disabled = ! current_user_can( 'edit_post', $post_id );

		/* translators: %s: role title. */
		$label = sprintf( __( 'Toggle whether %s is active', 'tiny-steps' ), get_the_title( $post_id ) );
		?>
		<button
			type="button"
			class="tnys-role-toggle"
			role="switch"
			aria-checked="<?php echo $active ? 'true' : 'false'; ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
			data-id="<?php echo esc_attr( (string) $post_id ); ?>"
			<?php disabled( $disabled ); ?>
		>
			<span class="tnys-role-toggle__track" aria-hidden="true">
				<span class="tnys-role-toggle__thumb"></span>
			</span>
		</button>
		<?php
	}
endif;
add_action( 'manage_open-role_posts_custom_column', 'tnys_open_role_column_content', 10, 2 );


if ( ! function_exists( 'tnys_open_role_admin_assets' ) ) :
	/**
	 * Loads the toggle script and styles on the Open Roles list table only.
	 *
	 * @param  string $hook Current admin page.
	 * @return void
	 */
	function tnys_open_role_admin_assets( $hook ) {
		global $typenow;

		if ( 'edit.php' !== $hook || 'open-role' !== $typenow ) {
			return;
		}

		wp_enqueue_style(
			'tnys-open-role-admin',
			get_theme_file_uri( 'assets/css/open-role-admin.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'tnys-open-role-toggle',
			get_theme_file_uri( 'assets/js/open-role-toggle.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_add_inline_script(
			'tnys-open-role-toggle',
			'window.hangOpenRole = ' . wp_json_encode(
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'tnys_open_role_toggle' ),
				)
			) . ';',
			'before'
		);
	}
endif;
add_action( 'admin_enqueue_scripts', 'tnys_open_role_admin_assets' );


if ( ! function_exists( 'tnys_toggle_open_role_active_ajax' ) ) :
	/**
	 * Flips the active flag for a single role.
	 *
	 * @return void
	 */
	function tnys_toggle_open_role_active_ajax() {
		check_ajax_referer( 'tnys_open_role_toggle', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id || 'open-role' !== get_post_type( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Unknown role.', 'tiny-steps' ) ), 400 );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'You cannot edit this role.', 'tiny-steps' ) ), 403 );
		}

		$active = isset( $_POST['active'] ) && '1' === $_POST['active'];

		update_post_meta( $post_id, 'tnys_role_active', $active );

		wp_send_json_success( array( 'active' => $active ) );
	}
endif;
add_action( 'wp_ajax_tnys_toggle_open_role_active', 'tnys_toggle_open_role_active_ajax' );


// =============================================================================
// SVG uploads
// =============================================================================

if ( ! function_exists( 'tnys_allow_svg_upload' ) ) :
	/**
	 * Permits SVG uploads for users who can already publish unfiltered markup.
	 *
	 * WordPress blocks image/svg+xml because an SVG is a script carrier. The
	 * capability check plus the sanitiser below are what make this safe: the
	 * file is rewritten on upload with scripts, event handlers and external
	 * references removed.
	 *
	 * @param array $mimes Allowed mime types.
	 * @return array Filtered mime types.
	 */
	function tnys_allow_svg_upload( $mimes ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $mimes;
		}

		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;
	}
endif;
add_filter( 'upload_mimes', 'tnys_allow_svg_upload' );


if ( ! function_exists( 'tnys_sanitize_svg_upload' ) ) :
	/**
	 * Rewrites an uploaded SVG through the theme's sanitiser before it lands
	 * in the media library.
	 *
	 * Runs on every upload, so an SVG that slips past `upload_mimes` — through
	 * a plugin, say — is still cleaned. Reuses tnys_sanitize_svg_markup() from
	 * inc/my-icons.php, which strips scripts, event handlers and external
	 * references. An unparseable file is rejected outright.
	 *
	 * @param array $file Upload array.
	 * @return array Possibly rejected upload array.
	 */
	function tnys_sanitize_svg_upload( $file ) {
		if ( empty( $file['tmp_name'] ) || empty( $file['type'] ) ) {
			return $file;
		}

		if ( 'image/svg+xml' !== $file['type'] ) {
			return $file;
		}

		if ( ! function_exists( 'tnys_sanitize_svg_markup' ) ) {
			$file['error'] = __( 'SVG uploads are unavailable: the sanitiser is missing.', 'tiny-steps' );
			return $file;
		}

		$raw = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local temp file.

		if ( false === $raw ) {
			$file['error'] = __( 'The SVG could not be read.', 'tiny-steps' );
			return $file;
		}

		$clean = tnys_sanitize_svg_markup( $raw );

		if ( '' === $clean ) {
			$file['error'] = __( 'That SVG could not be sanitised, so it was not uploaded.', 'tiny-steps' );
			return $file;
		}

		file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents -- Local temp file.

		return $file;
	}
endif;
add_filter( 'wp_handle_upload_prefilter', 'tnys_sanitize_svg_upload' );
