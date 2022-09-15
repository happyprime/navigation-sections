<?php
/**
 * Plugin Name:  Navigation Sections
 * Description:  Categorize content in sections to be grouped in menus.
 * Version:      0.0.1
 * Plugin URI:   https://github.com/happyprime/navigation-sections/
 * Author:       Happy Prime
 * Author URI:   https://happyprime.co
 * Text Domain:  navigation-sections
 * Requires PHP: 7.4
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package navigation-sections
 */

namespace NavigationSections;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/menu.php';
require_once __DIR__ . '/includes/page.php';
require_once __DIR__ . '/includes/taxonomy.php';
