<?php
/**
 * Manage custom meta on pages for navigation section title.
 *
 * @package navigation-sections
 */

namespace NavigationSections\Page;

add_action( 'init', __NAMESPACE__ . '\register' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_editor_assets', 11 );

/**
 * Provide the key used for capturing navigation section title meta.
 *
 * @return string The meta key.
 */
function get_title_meta_key() : string {
	return '_navigation_section_title';
}

/**
 * Register the post meta used for navigation section titles.
 */
function register() {
	register_meta(
		'post',
		get_title_meta_key(),
		[
			'object_subtype'    => 'page',
			'show_in_rest'      => true,
			'auth_callback'     => '__return_true',
			'sanitize_callback' => 'sanitize_text_field',
			'type'              => 'string',
			'single'            => true,
		]
	);
}

/**
 * Enqueue block editor assets to control the "From the department of" scripting.
 */
function enqueue_editor_assets() {
	$post = get_post();

	if ( 'page' !== $post->post_type ) {
		return;
	}

	$asset_data = require_once dirname( __DIR__ ) . '/build/index.asset.php';

	wp_enqueue_script(
		'navigation-section-meta',
		plugins_url( 'build/index.js', __DIR__ ),
		$asset_data['dependencies'],
		$asset_data['version'],
		true
	);
}
