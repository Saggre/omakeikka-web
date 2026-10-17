<?php

/**
 * Registers the private 'help_request' custom post type.
 *
 * Households submit these from the public form on the marketing site. The type
 * is deliberately not public: it holds personal data and must never be
 * queryable, searchable or exposed over the REST API.
 *
 * Records are created only by App\Services\HelpRequestForm. 'Add New' is
 * disabled in the admin so the only write path is the validated one.
 *
 * Meta uses register_post_meta(), which is the first use of that function in
 * this repository. The older pattern in occupation-cpt.php and
 * marketplace-category-cpt.php relies on 'custom-fields' support plus bare
 * update_post_meta() calls, which puts sanitisation at whichever call site
 * remembers it. That is not acceptable for personal data, so these keys
 * declare a sanitize_callback that runs on every write path instead.
 */

const OMAKEIKKA_HELP_REQUEST_STATUSES = array(
	'uusi'       => 'Uusi',
	'yhteydessa' => 'Yhteydessä',
	'sovittu'    => 'Sovittu',
	'valmis'     => 'Valmis',
	'peruttu'    => 'Peruttu',
);

const OMAKEIKKA_HELP_REQUEST_BUDGETS = array(
	'ei-tiedossa' => 'En osaa sanoa',
	'alle-100'    => 'Alle 100 euroa',
	'100-300'     => '100 euroa, 300 euroa',
	'yli-300'     => 'Yli 300 euroa',
);

// Retention. See the privacy policy; these three values are the schedule.
const OMAKEIKKA_HELP_REQUEST_IP_DAYS        = 30;
const OMAKEIKKA_HELP_REQUEST_ANONYMISE_DAYS = 365;
const OMAKEIKKA_HELP_REQUEST_DELETE_DAYS    = 730;

add_action( 'init', 'omakeikka_register_help_request_cpt' );
add_action( 'init', 'omakeikka_register_help_request_meta', 11 );
add_action( 'init', 'omakeikka_help_request_schedule_cleanup', 12 );
add_filter( 'manage_help_request_posts_columns', 'omakeikka_help_request_columns' );
add_action( 'manage_help_request_posts_custom_column', 'omakeikka_help_request_column', 10, 2 );
add_filter( 'manage_edit-help_request_sortable_columns', 'omakeikka_help_request_sortable_columns' );
add_action( 'restrict_manage_posts', 'omakeikka_help_request_filters' );
add_action( 'pre_get_posts', 'omakeikka_help_request_admin_query' );
add_action( 'add_meta_boxes_help_request', 'omakeikka_help_request_meta_boxes' );
add_action( 'save_post_help_request', 'omakeikka_help_request_save_status', 10, 2 );
add_filter( 'dashboard_glance_items', 'omakeikka_help_request_glance' );
add_action( 'omakeikka_help_request_cleanup', 'omakeikka_help_request_run_cleanup' );
add_action( 'admin_head', 'omakeikka_help_request_admin_styles' );

/**
 * Register the help_request CPT.
 *
 * rewrite and query_var are both false, so this type generates no rewrite
 * rules and never needs a flush. deploy.php does not run `wp rewrite flush`,
 * so a design that needed one would be a deploy hazard.
 */
function omakeikka_register_help_request_cpt(): void {
	register_post_type(
		'help_request',
		array(
			'labels'              => array(
				'name'               => __( 'Avunpyynnöt', 'omakeikka-theme' ),
				'singular_name'      => __( 'Avunpyyntö', 'omakeikka-theme' ),
				'edit_item'          => __( 'Muokkaa avunpyyntöä', 'omakeikka-theme' ),
				'view_item'          => __( 'Näytä avunpyyntö', 'omakeikka-theme' ),
				'search_items'       => __( 'Etsi avunpyyntöjä', 'omakeikka-theme' ),
				'not_found'          => __( 'Avunpyyntöjä ei löydy', 'omakeikka-theme' ),
				'not_found_in_trash' => __( 'Avunpyyntöjä ei löydy roskakorista', 'omakeikka-theme' ),
				'menu_name'          => __( 'Avunpyynnöt', 'omakeikka-theme' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-megaphone',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'editor' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'delete_with_user'    => false,
		)
	);
}

/**
 * Register the meta keys.
 *
 * Every key is underscore-prefixed, which makes it protected meta: invisible
 * to the Custom Fields UI and not settable through $_POST['meta_input'].
 */
function omakeikka_register_help_request_meta(): void {
	$auth = function () {
		return current_user_can( 'edit_posts' );
	};

	$keys = array(
		'_hr_name'             => 'sanitize_text_field',
		'_hr_email'            => 'sanitize_email',
		'_hr_phone'            => 'omakeikka_sanitize_phone',
		'_hr_municipality_id'  => 'omakeikka_sanitize_municipality_id',
		'_hr_group_slug'       => 'sanitize_key',
		'_hr_category_slug'    => 'sanitize_key',
		'_hr_budget'           => 'omakeikka_sanitize_help_request_budget',
		'_hr_status'           => 'omakeikka_sanitize_help_request_status',
		'_hr_status_changed'   => 'sanitize_text_field',
		'_hr_consent_at'       => 'sanitize_text_field',
		'_hr_consent_version'  => 'sanitize_key',
		'_hr_source_url'       => 'esc_url_raw',
		'_hr_ip_hash'          => 'sanitize_text_field',
	);

	foreach ( $keys as $key => $callback ) {
		register_post_meta(
			'help_request',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => $callback,
				'auth_callback'     => $auth,
			)
		);
	}

	foreach ( array( '_hr_consent', '_hr_mail_failed' ) as $key ) {
		register_post_meta(
			'help_request',
			$key,
			array(
				'type'          => 'boolean',
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => $auth,
			)
		);
	}
}

/**
 * Keep digits, spaces and the handful of punctuation marks a Finnish phone
 * number can legitimately carry.
 */
function omakeikka_sanitize_phone( $value ): string {
	$value = preg_replace( '/[^0-9+()\- ]/', '', (string) $value );

	return trim( substr( $value, 0, 32 ) );
}

/**
 * Municipality ids are zero-padded three-character Statistics Finland codes.
 * Anything not in the bundled list is discarded rather than stored.
 */
function omakeikka_sanitize_municipality_id( $value ): string {
	$value = sanitize_text_field( (string) $value );

	if ( ! class_exists( '\App\Services\Municipalities' ) ) {
		return $value;
	}

	return \App\Services\Municipalities::isValid( $value ) ? $value : '';
}

/**
 * Fall back to 'uusi' rather than storing an unknown status.
 */
function omakeikka_sanitize_help_request_status( $value ): string {
	$value = sanitize_key( (string) $value );

	return array_key_exists( $value, OMAKEIKKA_HELP_REQUEST_STATUSES ) ? $value : 'uusi';
}

/**
 * The budget field is optional, so an unknown value becomes empty.
 */
function omakeikka_sanitize_help_request_budget( $value ): string {
	$value = sanitize_key( (string) $value );

	return array_key_exists( $value, OMAKEIKKA_HELP_REQUEST_BUDGETS ) ? $value : '';
}

/**
 * Admin list columns.
 */
function omakeikka_help_request_columns( array $columns ): array {
	return array(
		'cb'              => $columns['cb'] ?? '',
		'title'           => __( 'Otsikko', 'omakeikka-theme' ),
		'hr_status'       => __( 'Tila', 'omakeikka-theme' ),
		'hr_group'        => __( 'Palveluryhmä', 'omakeikka-theme' ),
		'hr_municipality' => __( 'Paikkakunta', 'omakeikka-theme' ),
		'hr_contact'      => __( 'Yhteystiedot', 'omakeikka-theme' ),
		'date'            => __( 'Saapunut', 'omakeikka-theme' ),
	);
}

/**
 * Render a single admin list column.
 */
function omakeikka_help_request_column( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'hr_status':
			$status = (string) get_post_meta( $post_id, '_hr_status', true );
			$label  = OMAKEIKKA_HELP_REQUEST_STATUSES[ $status ] ?? $status;

			printf(
				'<span class="hr-status hr-status--%s">%s</span>',
				esc_attr( $status ),
				esc_html( $label )
			);

			if ( get_post_meta( $post_id, '_hr_mail_failed', true ) ) {
				printf(
					' <span class="dashicons dashicons-warning" title="%s"></span>',
					esc_attr__( 'Ilmoitussähköpostin lähetys epäonnistui', 'omakeikka-theme' )
				);
			}
			break;

		case 'hr_group':
			$slug = (string) get_post_meta( $post_id, '_hr_group_slug', true );
			$term = $slug ? get_term_by( 'slug', $slug, 'marketplace_group' ) : false;

			echo esc_html( $term ? html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) : $slug );
			break;

		case 'hr_municipality':
			$code = (string) get_post_meta( $post_id, '_hr_municipality_id', true );

			echo esc_html( class_exists( '\App\Services\Municipalities' ) ? \App\Services\Municipalities::name( $code ) : $code );
			break;

		case 'hr_contact':
			$name  = (string) get_post_meta( $post_id, '_hr_name', true );
			$email = (string) get_post_meta( $post_id, '_hr_email', true );
			$phone = (string) get_post_meta( $post_id, '_hr_phone', true );

			if ( $name ) {
				echo esc_html( $name ) . '<br>';
			}

			if ( $email ) {
				printf( '<a href="mailto:%1$s">%1$s</a><br>', esc_attr( $email ) );
			}

			if ( $phone ) {
				printf( '<a href="tel:%1$s">%2$s</a>', esc_attr( preg_replace( '/\s+/', '', $phone ) ), esc_html( $phone ) );
			}
			break;
	}
}

/**
 * Allow sorting by status and municipality.
 */
function omakeikka_help_request_sortable_columns( array $columns ): array {
	$columns['hr_status']       = 'hr_status';
	$columns['hr_municipality'] = 'hr_municipality';

	return $columns;
}

/**
 * Status and group dropdowns above the admin list.
 */
function omakeikka_help_request_filters( string $post_type ): void {
	if ( 'help_request' !== $post_type ) {
		return;
	}

	$status = isset( $_GET['hr_status'] ) ? sanitize_key( wp_unslash( $_GET['hr_status'] ) ) : '';

	echo '<select name="hr_status">';
	printf( '<option value="">%s</option>', esc_html__( 'Kaikki tilat', 'omakeikka-theme' ) );

	foreach ( OMAKEIKKA_HELP_REQUEST_STATUSES as $key => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $key ),
			selected( $status, $key, false ),
			esc_html( $label )
		);
	}

	echo '</select>';

	$group = isset( $_GET['hr_group'] ) ? sanitize_key( wp_unslash( $_GET['hr_group'] ) ) : '';
	$terms = get_terms( array(
		'taxonomy'   => 'marketplace_group',
		'hide_empty' => false,
	) );

	if ( is_wp_error( $terms ) ) {
		return;
	}

	echo '<select name="hr_group">';
	printf( '<option value="">%s</option>', esc_html__( 'Kaikki palveluryhmät', 'omakeikka-theme' ) );

	foreach ( $terms as $term ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $term->slug ),
			selected( $group, $term->slug, false ),
			esc_html( html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) )
		);
	}

	echo '</select>';
}

/**
 * Apply the admin list filters and sorting.
 */
function omakeikka_help_request_admin_query( \WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || 'help_request' !== $query->get( 'post_type' ) ) {
		return;
	}

	$meta_query = array();

	if ( ! empty( $_GET['hr_status'] ) ) {
		$meta_query[] = array(
			'key'   => '_hr_status',
			'value' => sanitize_key( wp_unslash( $_GET['hr_status'] ) ),
		);
	}

	if ( ! empty( $_GET['hr_group'] ) ) {
		$meta_query[] = array(
			'key'   => '_hr_group_slug',
			'value' => sanitize_key( wp_unslash( $_GET['hr_group'] ) ),
		);
	}

	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query );
	}

	$orderby = $query->get( 'orderby' );

	if ( 'hr_status' === $orderby ) {
		$query->set( 'meta_key', '_hr_status' );
		$query->set( 'orderby', 'meta_value' );
	}

	if ( 'hr_municipality' === $orderby ) {
		$query->set( 'meta_key', '_hr_municipality_id' );
		$query->set( 'orderby', 'meta_value' );
	}
}

/**
 * Read-only detail box plus the editable status control.
 */
function omakeikka_help_request_meta_boxes(): void {
	add_meta_box(
		'omakeikka_help_request_details',
		__( 'Avunpyynnön tiedot', 'omakeikka-theme' ),
		'omakeikka_help_request_details_box',
		'help_request',
		'normal',
		'high'
	);

	add_meta_box(
		'omakeikka_help_request_status',
		__( 'Tila', 'omakeikka-theme' ),
		'omakeikka_help_request_status_box',
		'help_request',
		'side',
		'high'
	);
}

/**
 * Render the read-only submission details.
 */
function omakeikka_help_request_details_box( \WP_Post $post ): void {
	$municipality = (string) get_post_meta( $post->ID, '_hr_municipality_id', true );
	$budget       = (string) get_post_meta( $post->ID, '_hr_budget', true );
	$consent_at   = (string) get_post_meta( $post->ID, '_hr_consent_at', true );

	$rows = array(
		__( 'Nimi', 'omakeikka-theme' )         => (string) get_post_meta( $post->ID, '_hr_name', true ),
		__( 'Sähköposti', 'omakeikka-theme' )   => (string) get_post_meta( $post->ID, '_hr_email', true ),
		__( 'Puhelin', 'omakeikka-theme' )      => (string) get_post_meta( $post->ID, '_hr_phone', true ),
		__( 'Paikkakunta', 'omakeikka-theme' )  => class_exists( '\App\Services\Municipalities' ) ? \App\Services\Municipalities::name( $municipality ) : $municipality,
		__( 'Palveluryhmä', 'omakeikka-theme' ) => (string) get_post_meta( $post->ID, '_hr_group_slug', true ),
		__( 'Palvelu', 'omakeikka-theme' )      => (string) get_post_meta( $post->ID, '_hr_category_slug', true ),
		__( 'Budjetti', 'omakeikka-theme' )     => OMAKEIKKA_HELP_REQUEST_BUDGETS[ $budget ] ?? '',
		__( 'Lähetetty sivulta', 'omakeikka-theme' ) => (string) get_post_meta( $post->ID, '_hr_source_url', true ),
		__( 'Suostumus annettu', 'omakeikka-theme' ) => $consent_at,
	);

	echo '<table class="widefat striped"><tbody>';

	foreach ( $rows as $label => $value ) {
		if ( '' === $value ) {
			continue;
		}

		printf(
			'<tr><th scope="row" style="width:12rem">%s</th><td>%s</td></tr>',
			esc_html( $label ),
			esc_html( $value )
		);
	}

	echo '</tbody></table>';
}

/**
 * Render the status select.
 *
 * Any status may move to any other. This is a triage field, not a workflow:
 * an operator needs to be able to move a request back to 'yhteydessa' when
 * the household calls again.
 */
function omakeikka_help_request_status_box( \WP_Post $post ): void {
	$current = (string) get_post_meta( $post->ID, '_hr_status', true );
	$changed = (string) get_post_meta( $post->ID, '_hr_status_changed', true );

	wp_nonce_field( 'omakeikka_help_request_status', '_hr_status_nonce' );

	echo '<select name="_hr_status" id="_hr_status" class="widefat">';

	foreach ( OMAKEIKKA_HELP_REQUEST_STATUSES as $key => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $key ),
			selected( $current, $key, false ),
			esc_html( $label )
		);
	}

	echo '</select>';

	if ( $changed ) {
		printf(
			'<p class="description">%s</p>',
			esc_html( sprintf( __( 'Muutettu %s', 'omakeikka-theme' ), $changed ) )
		);
	}
}

/**
 * Persist the status change.
 */
function omakeikka_help_request_save_status( int $post_id, \WP_Post $post ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! isset( $_POST['_hr_status'] ) ) {
		return;
	}

	if ( ! isset( $_POST['_hr_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_hr_status_nonce'] ) ), 'omakeikka_help_request_status' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$from = (string) get_post_meta( $post_id, '_hr_status', true );
	$to   = omakeikka_sanitize_help_request_status( wp_unslash( $_POST['_hr_status'] ) );

	if ( $from === $to ) {
		return;
	}

	update_post_meta( $post_id, '_hr_status', $to );
	update_post_meta( $post_id, '_hr_status_changed', gmdate( 'Y-m-d H:i:s' ) );

	do_action( 'omakeikka_help_request_status_changed', $post_id, $from, $to );
}

/**
 * Surface the new-request count on the dashboard.
 *
 * This is the safety net for a notification email that never arrives: a lead
 * is still visible to anyone who opens wp-admin.
 */
function omakeikka_help_request_glance( array $items ): array {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $items;
	}

	$query = new \WP_Query( array(
		'post_type'      => 'help_request',
		'post_status'    => 'private',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => false,
		'meta_query'     => array(
			array(
				'key'   => '_hr_status',
				'value' => 'uusi',
			),
		),
	) );

	$count = (int) $query->found_posts;

	if ( $count < 1 ) {
		return $items;
	}

	$items[] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'edit.php?post_type=help_request&hr_status=uusi' ) ),
		esc_html( sprintf(
			/* translators: %d is the number of new help requests. */
			_n( '%d uusi avunpyyntö', '%d uutta avunpyyntöä', $count, 'omakeikka-theme' ),
			$count
		) )
	);

	return $items;
}

/**
 * Minimal status colouring in the admin list.
 */
function omakeikka_help_request_admin_styles(): void {
	$screen = get_current_screen();

	if ( ! $screen || 'help_request' !== $screen->post_type ) {
		return;
	}

	echo '<style>
		.hr-status { display:inline-block; padding:2px 8px; border-radius:10px; font-size:12px; background:#f0f0f1; }
		.hr-status--uusi { background:#d7f0d7; }
		.hr-status--yhteydessa { background:#fff3cd; }
		.hr-status--valmis { background:#e2e3e5; }
		.hr-status--peruttu { background:#f8d7da; }
	</style>';
}

/**
 * Schedule the daily retention job.
 */
function omakeikka_help_request_schedule_cleanup(): void {
	if ( ! wp_next_scheduled( 'omakeikka_help_request_cleanup' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'omakeikka_help_request_cleanup' );
	}
}

/**
 * Retention, in three stages.
 *
 * Anonymise before deleting so the funnel counts, how many requests came from
 * which municipality and which group, survive the personal data purge.
 * Batched so a backlog cannot time out the request that triggers cron.
 */
function omakeikka_help_request_run_cleanup(): void {
	$now = time();

	$ip_cutoff = gmdate( 'Y-m-d H:i:s', $now - ( OMAKEIKKA_HELP_REQUEST_IP_DAYS * DAY_IN_SECONDS ) );
	$ip_stale  = get_posts( array(
		'post_type'      => 'help_request',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'date_query'     => array( array( 'before' => $ip_cutoff ) ),
		'meta_query'     => array(
			array(
				'key'     => '_hr_ip_hash',
				'compare' => 'EXISTS',
			),
		),
	) );

	foreach ( $ip_stale as $post_id ) {
		delete_post_meta( $post_id, '_hr_ip_hash' );
	}

	$anon_cutoff = gmdate( 'Y-m-d H:i:s', $now - ( OMAKEIKKA_HELP_REQUEST_ANONYMISE_DAYS * DAY_IN_SECONDS ) );
	$to_anon     = get_posts( array(
		'post_type'      => 'help_request',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'date_query'     => array( array( 'before' => $anon_cutoff ) ),
		'meta_query'     => array(
			array(
				'key'     => '_hr_email',
				'compare' => 'EXISTS',
			),
		),
	) );

	foreach ( $to_anon as $post_id ) {
		omakeikka_help_request_anonymise( $post_id );
	}

	$delete_cutoff = gmdate( 'Y-m-d H:i:s', $now - ( OMAKEIKKA_HELP_REQUEST_DELETE_DAYS * DAY_IN_SECONDS ) );
	$to_delete     = get_posts( array(
		'post_type'      => 'help_request',
		'post_status'    => 'any',
		'posts_per_page' => 100,
		'fields'         => 'ids',
		'date_query'     => array( array( 'before' => $delete_cutoff ) ),
	) );

	foreach ( $to_delete as $post_id ) {
		wp_delete_post( $post_id, true );
	}
}

/**
 * Strip the personal data from one request, keeping the countable fields.
 */
function omakeikka_help_request_anonymise( int $post_id ): void {
	foreach ( array( '_hr_name', '_hr_email', '_hr_phone', '_hr_ip_hash' ) as $key ) {
		delete_post_meta( $post_id, $key );
	}

	wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => __( 'Sisältö poistettu säilytysajan päätyttyä.', 'omakeikka-theme' ),
	) );
}

add_filter( 'wp_privacy_personal_data_exporters', 'omakeikka_help_request_register_exporter' );
add_filter( 'wp_privacy_personal_data_erasers', 'omakeikka_help_request_register_eraser' );

/**
 * Register the GDPR exporter so a subject access request is one click in
 * Tools, Export Personal Data.
 */
function omakeikka_help_request_register_exporter( array $exporters ): array {
	$exporters['omakeikka-help-request'] = array(
		'exporter_friendly_name' => __( 'Avunpyynnöt', 'omakeikka-theme' ),
		'callback'               => 'omakeikka_help_request_export',
	);

	return $exporters;
}

/**
 * Export every request submitted from one email address.
 */
function omakeikka_help_request_export( string $email, int $page = 1 ): array {
	$posts = omakeikka_help_request_find_by_email( $email, $page );
	$items = array();

	foreach ( $posts as $post_id ) {
		$data = array(
			array(
				'name'  => __( 'Otsikko', 'omakeikka-theme' ),
				'value' => get_the_title( $post_id ),
			),
			array(
				'name'  => __( 'Kuvaus', 'omakeikka-theme' ),
				'value' => get_post_field( 'post_content', $post_id ),
			),
		);

		foreach ( array(
			'_hr_name'            => __( 'Nimi', 'omakeikka-theme' ),
			'_hr_email'           => __( 'Sähköposti', 'omakeikka-theme' ),
			'_hr_phone'           => __( 'Puhelin', 'omakeikka-theme' ),
			'_hr_municipality_id' => __( 'Paikkakunta', 'omakeikka-theme' ),
			'_hr_consent_at'      => __( 'Suostumus annettu', 'omakeikka-theme' ),
		) as $key => $label ) {
			$value = (string) get_post_meta( $post_id, $key, true );

			if ( '' !== $value ) {
				$data[] = array(
					'name'  => $label,
					'value' => $value,
				);
			}
		}

		$items[] = array(
			'group_id'    => 'omakeikka-help-request',
			'group_label' => __( 'Avunpyynnöt', 'omakeikka-theme' ),
			'item_id'     => 'help-request-' . $post_id,
			'data'        => $data,
		);
	}

	return array(
		'data' => $items,
		'done' => count( $posts ) < 20,
	);
}

/**
 * Register the GDPR eraser.
 */
function omakeikka_help_request_register_eraser( array $erasers ): array {
	$erasers['omakeikka-help-request'] = array(
		'eraser_friendly_name' => __( 'Avunpyynnöt', 'omakeikka-theme' ),
		'callback'             => 'omakeikka_help_request_erase',
	);

	return $erasers;
}

/**
 * Anonymise in place rather than deleting, so the request itself still counts
 * towards the funnel while the personal data is gone.
 */
function omakeikka_help_request_erase( string $email, int $page = 1 ): array {
	$posts = omakeikka_help_request_find_by_email( $email, $page );

	foreach ( $posts as $post_id ) {
		omakeikka_help_request_anonymise( $post_id );
	}

	return array(
		'items_removed'  => count( $posts ) > 0,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => count( $posts ) < 20,
	);
}

/**
 * @return int[]
 */
function omakeikka_help_request_find_by_email( string $email, int $page ): array {
	return get_posts( array(
		'post_type'      => 'help_request',
		'post_status'    => 'any',
		'posts_per_page' => 20,
		'paged'          => max( 1, $page ),
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'   => '_hr_email',
				'value' => sanitize_email( $email ),
			),
		),
	) );
}
