<?php
/**
 * Manage menu customizations.
 *
 * @package navigation-sections
 */

namespace NavigationSections\Menu;

use NavigationSections\Page;
use NavigationSections\Taxonomy;

add_filter( 'nav_menu_link_attributes', __NAMESPACE__ . '\filter_menu_link_attributes', 10, 2 );
add_filter( 'nav_menu_item_title', __NAMESPACE__ . '\filter_menu_item_title', 10, 2 );
add_filter( 'nav_menu_item_args', __NAMESPACE__ . '\filter_nav_menu_item_args', 99, 2 );

/**
 * Replace the menu item's anchor href with a URL stored in meta.
 *
 * @param array    $atts      A list of menu link attributes.
 * @param \WP_Post $menu_item The menu item object.
 * @return array A modified list of menu link attributes.
 */
function filter_menu_link_attributes( array $atts, \WP_Post $menu_item ) : array {
	if ( Taxonomy\get_slug() === $menu_item->object ) {
		$atts['href'] = get_term_meta( (int) $menu_item->object_id, Taxonomy\get_url_meta_key(), true );
	}

	return $atts;
}

/**
 * Filter the title used for a navigation section menu item.
 *
 * @param string   $title     The menu item title.
 * @param \WP_Post $menu_item The menu item.
 * @return string Modified menu item title.
 */
function filter_menu_item_title( string $title, \WP_Post $menu_item ) : string {
	if ( Taxonomy\get_slug() !== $menu_item->object ) {
		return $title;
	}

	$label = get_term_meta( $menu_item->object_id, Taxonomy\get_label_meta_key(), true );

	return $label ? $label : $title;
}

/**
 * Filter a navigation section nav item to include its sub menu.
 *
 * @param \stdClass $args      An object of wp_nav_menu() arguments.
 * @param \WP_Post  $menu_item Menu item data object.
 * @return \stdClass Modified nav menu arguments.
 */
function filter_nav_menu_item_args( \stdClass $args, \WP_Post $menu_item ) : \stdClass {
	if ( Taxonomy\get_slug() !== $menu_item->object ) {
		return $args;
	}

	$items = new \WP_Query(
		[
			'post_type'      => 'page',
			'posts_per_page' => 100,
			'order'          => 'ASC',
			'orderby'        => 'menu_order name',
			'tax_query'      => [
				[
					'taxonomy' => \NavigationSections\Taxonomy\get_slug(),
					'field'    => 'id',
					'terms'    => [ (int) $menu_item->object_id ],
				],
			],
		]
	);

	if ( $items->posts ) {
		$sub_menu  = '<button class="toggle-sub-menu"><span class="screen-reader-text">Open menu</span></button>';
		$sub_menu .= '<ul class="sub-menu">';

		foreach ( $items->posts as $item ) {
			$item_title = get_post_meta( $item->ID, Page\get_title_meta_key(), true );
			$item_title = $item_title ? $item_title : get_the_title( $item );

			ob_start();
			?>
			<li><a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>"><?php echo esc_html( $item_title ); ?></a></li>
			<?php
			$sub_menu .= ob_get_clean();
		}

		$sub_menu .= '</ul>';

		$args->after = $sub_menu;
	}

	return $args;
}
