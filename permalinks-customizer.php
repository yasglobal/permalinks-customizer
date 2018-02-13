<?php
/**
 * Plugin Name: Permalinks Customizer
 * Plugin URI: https://wordpress.org/plugins/permalinks-customizer/
 * Description: Set permalinks for default post-type and custom post-type which can be changed from the single post edit page.
 * Version: 1.3.9
 * Author: YAS Global Team
 * Author URI: https://www.yasglobal.com/web-design-development/wordpress/permalinks-customizer/
 * Donate link: https://www.paypal.me/yasglobal
 * License: GPLv3
 *
 * Text Domain: permalinks-customizer
 * Domain Path: /languages/
 *
 * @package PermalinksCustomizer
 */

/**
 *  Permalinks Customizer - Auto Create Permalinks/Update existing Permalinks Plugin
 *  Copyright (C) 2016-2018, Sami Ahmed Siddiqui <sami.siddiqui@yasglobal.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if ( ! defined( 'PERMALINKS_CUSTOMIZER_FILE' ) ) {
	define( 'PERMALINKS_CUSTOMIZER_FILE', __FILE__ );
}

require_once(
	dirname( PERMALINKS_CUSTOMIZER_FILE ) . '/permalinks-customizer-main.php'
);
