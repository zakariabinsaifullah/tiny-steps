<?php
/**
 * Office Hours
 *
 * Appearance → Office Hours lets the clinic set its opening hours per day.
 * Blocks can show them through placeholders such as {{open_status}} or
 * {{closing_time}}, which are replaced with live values when the block renders.
 *
 * @package Tiny_Steps
 */

if ( ! defined( 'TNYS_OFFICE_HOURS_OPTION' ) ) {
	define( 'TNYS_OFFICE_HOURS_OPTION', 'tnys_office_hours' );
}

if ( ! function_exists( 'tnys_office_hours_days' ) ) :
	/**
	 * Day keys (matching PHP's `D` format, lowercased) and their labels, Monday first.
	 *
	 * @return array<string, string>
	 */
	function tnys_office_hours_days() {
		return array(
			'mon' => __( 'Monday', 'tiny-steps' ),
			'tue' => __( 'Tuesday', 'tiny-steps' ),
			'wed' => __( 'Wednesday', 'tiny-steps' ),
			'thu' => __( 'Thursday', 'tiny-steps' ),
			'fri' => __( 'Friday', 'tiny-steps' ),
			'sat' => __( 'Saturday', 'tiny-steps' ),
			'sun' => __( 'Sunday', 'tiny-steps' ),
		);
	}
endif;

if ( ! function_exists( 'tnys_office_hours_defaults' ) ) :
	/**
	 * Default hours: weekdays 9 AM–9 PM (phones 8:30 AM), weekends 8 AM–8 PM (phones 7:30 AM).
	 *
	 * @return array<string, array{closed: bool, open: string, close: string, phones: string}>
	 */
	function tnys_office_hours_defaults() {
		$defaults = array();

		foreach ( array_keys( tnys_office_hours_days() ) as $day ) {
			$weekend          = in_array( $day, array( 'sat', 'sun' ), true );
			$defaults[ $day ] = array(
				'closed' => false,
				'open'   => $weekend ? '08:00' : '09:00',
				'close'  => $weekend ? '20:00' : '21:00',
				'phones' => $weekend ? '07:30' : '08:30',
			);
		}

		return $defaults;
	}
endif;

if ( ! function_exists( 'tnys_get_office_hours' ) ) :
	/**
	 * Saved hours merged over the defaults.
	 *
	 * @return array<string, array{closed: bool, open: string, close: string, phones: string}>
	 */
	function tnys_get_office_hours() {
		$saved    = get_option( TNYS_OFFICE_HOURS_OPTION, array() );
		$defaults = tnys_office_hours_defaults();

		foreach ( $defaults as $day => $values ) {
			if ( isset( $saved[ $day ] ) && is_array( $saved[ $day ] ) ) {
				$defaults[ $day ] = array_merge( $values, $saved[ $day ] );
			}
		}

		return $defaults;
	}
endif;

if ( ! function_exists( 'tnys_office_hours_sanitize_time' ) ) :
	/**
	 * Keeps a 24-hour "HH:MM" time, or returns an empty string.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	function tnys_office_hours_sanitize_time( $value ) {
		$value = is_string( $value ) ? trim( $value ) : '';

		return preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $value ) ? $value : '';
	}
endif;

if ( ! function_exists( 'tnys_office_hours_sanitize' ) ) :
	/**
	 * Sanitises the submitted hours table.
	 *
	 * @param mixed $input Raw option value.
	 * @return array
	 */
	function tnys_office_hours_sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$clean = array();

		foreach ( tnys_office_hours_defaults() as $day => $defaults ) {
			$row           = isset( $input[ $day ] ) && is_array( $input[ $day ] ) ? $input[ $day ] : array();
			$clean[ $day ] = array(
				'closed' => ! empty( $row['closed'] ),
				'open'   => tnys_office_hours_sanitize_time( $row['open'] ?? '' ),
				'close'  => tnys_office_hours_sanitize_time( $row['close'] ?? '' ),
				'phones' => tnys_office_hours_sanitize_time( $row['phones'] ?? '' ),
			);

			// A day without both times can't be open.
			if ( '' === $clean[ $day ]['open'] || '' === $clean[ $day ]['close'] ) {
				$clean[ $day ]['closed'] = true;
			}
		}

		return $clean;
	}
endif;

if ( ! function_exists( 'tnys_office_hours_format' ) ) :
	/**
	 * Formats a "HH:MM" time for display.
	 *
	 * @param string $time  24-hour "HH:MM" time.
	 * @param bool   $short Drop ":00" on the hour ("9 PM" instead of "9:00 PM").
	 * @return string
	 */
	function tnys_office_hours_format( $time, $short = false ) {
		if ( '' === $time ) {
			return '';
		}

		list( $hour, $minute ) = array_map( 'intval', explode( ':', $time ) );

		$suffix = $hour >= 12 ? 'PM' : 'AM';
		$hour12 = $hour % 12 ? $hour % 12 : 12;

		if ( $short && 0 === $minute ) {
			return $hour12 . ' ' . $suffix;
		}

		return sprintf( '%d:%02d %s', $hour12, $minute, $suffix );
	}
endif;

if ( ! function_exists( 'tnys_office_hours_range' ) ) :
	/**
	 * "9:00 AM to 9:00 PM" (or "9 AM to 9 PM" when short) for a day, or "Closed".
	 *
	 * @param array $day   Day settings.
	 * @param bool  $short Drop ":00" on the hour.
	 * @return string
	 */
	function tnys_office_hours_range( $day, $short = false ) {
		if ( ! empty( $day['closed'] ) ) {
			return __( 'Closed', 'tiny-steps' );
		}

		/* translators: 1: opening time, 2: closing time. */
		return sprintf( __( '%1$s to %2$s', 'tiny-steps' ), tnys_office_hours_format( $day['open'], $short ), tnys_office_hours_format( $day['close'], $short ) );
	}
endif;

if ( ! function_exists( 'tnys_office_hours_status' ) ) :
	/**
	 * A sentence describing whether the clinic is open right now.
	 *
	 * "Open today until 9 PM", "Opens today at 9 AM", "Opens tomorrow at 8 AM",
	 * "Opens Monday at 9 AM" or "Closed today" (when no day is open).
	 *
	 * @param DateTimeImmutable|null $now Current time in the site timezone.
	 * @return string
	 */
	function tnys_office_hours_status( $now = null ) {
		$now   = $now ? $now : new DateTimeImmutable( 'now', wp_timezone() );
		$hours = tnys_get_office_hours();
		$days  = tnys_office_hours_days();
		$time  = $now->format( 'H:i' );
		$today = $hours[ strtolower( $now->format( 'D' ) ) ];

		if ( empty( $today['closed'] ) ) {
			if ( $time < $today['open'] ) {
				/* translators: %s: opening time. */
				return sprintf( __( 'Opens today at %s', 'tiny-steps' ), tnys_office_hours_format( $today['open'], true ) );
			}

			if ( $time < $today['close'] ) {
				/* translators: %s: closing time. */
				return sprintf( __( 'Open today until %s', 'tiny-steps' ), tnys_office_hours_format( $today['close'], true ) );
			}
		}

		// Closed for the rest of today: find the next open day within a week.
		for ( $offset = 1; $offset <= 7; $offset++ ) {
			$date = $now->modify( "+{$offset} day" );
			$key  = strtolower( $date->format( 'D' ) );

			if ( ! empty( $hours[ $key ]['closed'] ) ) {
				continue;
			}

			$at = tnys_office_hours_format( $hours[ $key ]['open'], true );

			if ( 1 === $offset ) {
				/* translators: %s: opening time. */
				return sprintf( __( 'Opens tomorrow at %s', 'tiny-steps' ), $at );
			}

			/* translators: 1: day name, 2: opening time. */
			return sprintf( __( 'Opens %1$s at %2$s', 'tiny-steps' ), $days[ $key ], $at );
		}

		return __( 'Closed today', 'tiny-steps' );
	}
endif;

if ( ! function_exists( 'tnys_office_hours_placeholders' ) ) :
	/**
	 * Placeholder → value map for the current moment.
	 *
	 * @return array<string, string>
	 */
	function tnys_office_hours_placeholders() {
		static $map = null;

		if ( null !== $map ) {
			return $map;
		}

		$now   = new DateTimeImmutable( 'now', wp_timezone() );
		$hours = tnys_get_office_hours();
		$today = $hours[ strtolower( $now->format( 'D' ) ) ];
		$open  = empty( $today['closed'] );

		$map = array(
			'{{open_status}}'          => tnys_office_hours_status( $now ),
			'{{opening_time}}'         => $open ? tnys_office_hours_format( $today['open'], true ) : __( 'Closed', 'tiny-steps' ),
			'{{closing_time}}'         => $open ? tnys_office_hours_format( $today['close'], true ) : __( 'Closed', 'tiny-steps' ),
			'{{hours_today}}'          => tnys_office_hours_range( $today ),
			'{{hours_weekdays}}'       => tnys_office_hours_range( $hours['mon'] ),
			'{{hours_weekend}}'        => tnys_office_hours_range( $hours['sat'] ),
			'{{hours_weekdays_short}}' => tnys_office_hours_range( $hours['mon'], true ),
			'{{hours_weekend_short}}'  => tnys_office_hours_range( $hours['sat'], true ),
			'{{closing_weekdays}}'     => tnys_office_hours_format( $hours['mon']['close'] ),
			'{{closing_weekend}}'      => tnys_office_hours_format( $hours['sat']['close'] ),
			'{{phones_weekdays}}'      => tnys_office_hours_format( $hours['mon']['phones'] ),
			'{{phones_weekend}}'       => tnys_office_hours_format( $hours['sat']['phones'] ),
		);

		foreach ( $hours as $key => $day ) {
			$map[ '{{hours_' . $key . '}}' ]  = tnys_office_hours_range( $day );
			$map[ '{{phones_' . $key . '}}' ] = tnys_office_hours_format( $day['phones'] );
		}

		$map = array_map( 'esc_html', $map );

		return $map;
	}
endif;

if ( ! function_exists( 'tnys_render_office_hours_placeholders' ) ) :
	/**
	 * Replaces office-hours placeholders in rendered block HTML.
	 *
	 * @param string $block_content Rendered block HTML.
	 * @return string
	 */
	function tnys_render_office_hours_placeholders( $block_content ) {
		if ( false === strpos( $block_content, '{{' ) ) {
			return $block_content;
		}

		return strtr( $block_content, tnys_office_hours_placeholders() );
	}
endif;
add_filter( 'render_block', 'tnys_render_office_hours_placeholders', 20 );

// ── Settings page: Appearance → Office Hours ─────────────────────────────────

if ( ! function_exists( 'tnys_office_hours_register_setting' ) ) :
	/**
	 * Registers the hours option.
	 */
	function tnys_office_hours_register_setting() {
		register_setting(
			'tnys_office_hours_group',
			TNYS_OFFICE_HOURS_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'tnys_office_hours_sanitize',
				'default'           => tnys_office_hours_defaults(),
			)
		);
	}
endif;
add_action( 'init', 'tnys_office_hours_register_setting' );

if ( ! function_exists( 'tnys_office_hours_add_menu' ) ) :
	/**
	 * Adds Appearance → Office Hours.
	 */
	function tnys_office_hours_add_menu() {
		add_theme_page(
			__( 'Office Hours', 'tiny-steps' ),
			__( 'Office Hours', 'tiny-steps' ),
			'manage_options',
			'tnys-office-hours',
			'tnys_office_hours_render_page'
		);
	}
endif;
add_action( 'admin_menu', 'tnys_office_hours_add_menu' );

if ( ! function_exists( 'tnys_office_hours_render_page' ) ) :
	/**
	 * Renders the settings page.
	 */
	function tnys_office_hours_render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$hours  = tnys_get_office_hours();
		$name   = TNYS_OFFICE_HOURS_OPTION;
		$tz     = wp_timezone_string();
		$sample = tnys_office_hours_placeholders();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Office Hours', 'tiny-steps' ); ?></h1>

			<p>
				<?php
				printf(
					/* translators: 1: timezone, 2: link to General Settings. */
					esc_html__( 'Times use the site timezone (%1$s), set in %2$s.', 'tiny-steps' ),
					'<strong>' . esc_html( $tz ) . '</strong>',
					'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Settings → General', 'tiny-steps' ) . '</a>'
				);
				?>
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'tnys_office_hours_group' ); ?>

				<table class="widefat striped" style="max-width: 760px;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Day', 'tiny-steps' ); ?></th>
							<th><?php esc_html_e( 'Closed', 'tiny-steps' ); ?></th>
							<th><?php esc_html_e( 'Opens', 'tiny-steps' ); ?></th>
							<th><?php esc_html_e( 'Closes', 'tiny-steps' ); ?></th>
							<th><?php esc_html_e( 'Phones open', 'tiny-steps' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( tnys_office_hours_days() as $key => $label ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $label ); ?></th>
								<td>
									<input type="checkbox" name="<?php echo esc_attr( "{$name}[{$key}][closed]" ); ?>" value="1" <?php checked( ! empty( $hours[ $key ]['closed'] ) ); ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: day name. */ __( 'Closed on %s', 'tiny-steps' ), $label ) ); ?>" />
								</td>
								<?php foreach ( array( 'open', 'close', 'phones' ) as $field ) : ?>
									<td>
										<input type="time" name="<?php echo esc_attr( "{$name}[{$key}][{$field}]" ); ?>" value="<?php echo esc_attr( $hours[ $key ][ $field ] ); ?>" />
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'Placeholders', 'tiny-steps' ); ?></h2>
			<p><?php esc_html_e( 'Type these in any block text (buttons, paragraphs, headings). Visitors see the live value; the editor shows the placeholder.', 'tiny-steps' ); ?></p>

			<table class="widefat striped" style="max-width: 760px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Placeholder', 'tiny-steps' ); ?></th>
						<th><?php esc_html_e( 'Shows right now', 'tiny-steps' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $sample as $token => $value ) : ?>
						<tr>
							<td><code><?php echo esc_html( $token ); ?></code></td>
							<td><?php echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in tnys_office_hours_placeholders(). ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
endif;
