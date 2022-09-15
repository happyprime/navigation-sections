<?php
/**
 * Manage the navigation section taxonomy.
 *
 * @package navigation-sections
 */

namespace NavigationSections\Taxonomy;

add_action( 'init', __NAMESPACE__ . '\register', 15 );
add_action( get_slug() . '_edit_form_fields', __NAMESPACE__ . '\display_edit_form_fields' );
add_action( get_slug() . '_add_form_fields', __NAMESPACE__ . '\display_add_form_fields' );
add_action( 'edit_' . get_slug(), __NAMESPACE__ . '\save_term_meta' );
add_action( 'create_' . get_slug(), __NAMESPACE__ . '\save_term_meta' );
add_filter( 'manage_edit-' . get_slug() . '_columns', __NAMESPACE__ . '\filter_columns' );
add_filter( 'manage_' . get_slug() . '_custom_column', __NAMESPACE__ . '\filter_column_data', 10, 3 );

/**
 * Retrieve the navigation taxonomy slug.
 *
 * @return string The taxonomy slug.
 */
function get_slug() : string {
	return 'navigation-section';
}

/**
 * Retrieve the meta key used to capture a section's URL.
 *
 * @return string The meta key.
 */
function get_url_meta_key() : string {
	return '_navigation_section_url';
}

/**
 * Register the navigation section taxonomy.
 */
function register() {
	register_taxonomy(
		get_slug(),
		[
			'page',
		],
		[
			'labels'            => [
				'name'          => 'Navigation Sections',
				'singular_name' => 'Navigation Section',
				'edit_item'     => 'Edit Section',
				'view_item'     => 'View Section',
				'update_item'   => 'Update Section',
				'add_new_item'  => 'Add New Section',
				'new_item_name' => 'New Section Name',
			],
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'public'            => false,
			'show_ui'           => true,
		]
	);
}

/**
 * Add a form field to capture a section's URL when creating a new section.
 */
function display_add_form_fields() : void {
	wp_nonce_field( get_url_meta_key(), get_url_meta_key() . '_nonce' );
	?>
	<div class="form-field">
		<label for="navigation-section-url">Section URL:</label>
		<input type="text" name="navigation_section_url" id="navigation-section-url" />
		<p class="description">The URL to associate with this section.</p>
	</div>
	<?php
}

/**
 * Add a form field to capture a section's URL when editing an existing seciton.
 *
 * @param \WP_Term $term The term being edited.
 */
function display_edit_form_fields( \WP_Term $term ) : void {
	$url = get_term_meta( $term->term_id, get_url_meta_key(), true );

	wp_nonce_field( get_url_meta_key(), get_url_meta_key() . '_nonce' );

	?>
	<tr class="form-field">
		<th scope="row">
			<label for="navigation-section-url">Section URL</label>
		</th>
		<td>
			<input type="text" name="navigation_section_url" id="navigation-section-url" value="<?php echo esc_attr( $url ); ?>" />
			<p class="description">The URL to associate with this section.</p>
		</td>
	</tr>
	<?php
}

/**
 * Save an associated URL when a section is saved or edited.
 *
 * @param int $term_id ID of the term.
 */
function save_term_meta( int $term_id ) : void {
	if ( ! isset( $_POST[ get_url_meta_key() . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ get_url_meta_key() . '_nonce' ], get_url_meta_key() ) ) {
		return;
	}

	if ( isset( $_POST['navigation_section_url'] ) && '' !== $_POST['navigation_section_url'] ) {
		update_term_meta( $term_id, get_url_meta_key(), esc_url_raw( $_POST['navigation_section_url'] ) );
	} else {
		delete_term_meta( $term_id, get_url_meta_key() );
	}
}

/**
 * Filter the list of columns displayed in the navigation section
 * taxonomy list table.
 *
 * @param array $columns Current columns.
 * @return array Modified list of columns.
 */
function filter_columns( array $columns ) : array {
	unset( $columns['posts'] );

	$columns['url']   = 'URL';
	$columns['posts'] = 'Count';

	return $columns;
}

/**
 * Output custom column content for term meta.
 *
 * @param string $content Column value.
 * @param string $column  The current column.
 * @param int    $term_id The current term ID.
 * @return string Modified column content.
 */
function filter_column_data( string $content, string $column, int $term_id ) : string {
	if ( 'url' === $column ) {
		$url = get_term_meta( $term_id, get_url_meta_key(), true );

		if ( $url ) {
			return esc_url( $url );
		}
	}

	return $content;
}
