<?php
/**
 * Plugin Name: Permalinks Customizer
 * Plugin URI: https://wordpress.org/plugins/permalinks-customizer/
 * Description: Set permalinks for default post-type and custom post-type which can be changed from the single post edit page.
 * Version: 2.8.2
 * Author: YAS Global Team
 * Author URI: https://www.yasglobal.com/web-design-development/wordpress/permalinks-customizer/
 * License: GPLv3
 *
 * Text Domain: permalinks-customizer
 * Domain Path: /languages/
 *
 * @package PermalinksCustomizer
 */

/**
 *  Permalinks Customizer - Auto Create Permalinks/Update existing Permalinks Plugin
 *  Copyright (C) 2016-2020, Sami Ahmed Siddiqui <sami.siddiqui@yasglobal.com>
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

/**
 * Main Class
 *
 * Define contants, include relevant files, create databse tables if required.
 *
 * @since 2.0.0
 */
final class Permalinks_Customizer {

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->setup_constants();
    $this->includes();
  }

  /**
   * Setup plugin constants.
   *
   * @since 2.0.0
   * @access private
   */
  private function setup_constants() {
    if ( ! defined( 'PERMALINKS_CUSTOMIZER_FILE' ) ) {
      define( 'PERMALINKS_CUSTOMIZER_FILE', __FILE__ );
    }

    if ( ! defined( 'PERMALINKS_CUSTOMIZER_PLUGIN_VERSION' ) ) {
      define( 'PERMALINKS_CUSTOMIZER_PLUGIN_VERSION', '2.8.2' );
    }

    if ( ! defined( 'PERMALINKS_CUSTOMIZER_PATH' ) ) {
      define( 'PERMALINKS_CUSTOMIZER_PATH', plugin_dir_path( PERMALINKS_CUSTOMIZER_FILE ) );
    }

    if ( ! defined( 'PERMALINKS_CUSTOMIZER_BASENAME' ) ) {
      define( 'PERMALINKS_CUSTOMIZER_BASENAME', plugin_basename( PERMALINKS_CUSTOMIZER_FILE ) );
    }
  }

  /**
   * Include required files.
   *
   * @since 2.0.0
   * @access private
   */
  private function includes() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-frontend.php'
    );

    $permalinks_customizer_frontend = new Permalinks_Customizer_Frontend();
    $permalinks_customizer_frontend->init();

    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-form.php'
    );

    $permalinks_customizer_form = new Permalinks_Customizer_Form();
    $permalinks_customizer_form->init();

    if ( is_admin() ) {
      require_once(
        PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-admin.php'
      );
      new Permalinks_Customizer_Admin();

      add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
      register_activation_hook(
        PERMALINKS_CUSTOMIZER_FILE,
        array( 'Permalinks_Customizer', 'plugin_activate' )
      );
    }
  }

  /**
   * Assign Capabilities to Administrator role (if found) on activating
   * the plugin and create a custom role with the name of
   * `Permalinks Customizer Manager` and assign the capabilities to that role.
   *
   * @since 2.0.0
   * @access public
   */
  public static function plugin_activate() {
    $role = get_role( 'administrator' );
    if ( ! empty( $role ) ) {
      $role->add_cap( 'pc_manage_permalinks' );
      $role->add_cap( 'pc_manage_permalink_settings' );
      $role->add_cap( 'pc_manage_permalink_redirects' );

      $added_capability = array();
      $added_capability['administrator'] = array(
        'pc_manage_permalinks',
        'pc_manage_permalink_settings',
        'pc_manage_permalink_redirects'
      );
      update_option( 'permalinks_customizer_capabilities', serialize( $added_capability ) );
    }

    add_role(
      'permalinks_customizer_manager',
      __( 'Permalinks Customizer Manager' ),
      array(
        'pc_manage_permalinks'          => true,
        'pc_manage_permalink_settings'  => true,
        'pc_manage_permalink_redirects' => true
      )
    );

    self::create_table();
  }

  /**
   * Creates a redirect table to the database.
   *
   * @since 2.0.0
   * @access private
   */
  private static function create_table() {
    global $wpdb;

    if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}permalinks_customizer_redirects';" ) ) {
      $collate = '';
      if ( $wpdb->has_cap( 'collation' ) ) {
        $collate = $wpdb->get_charset_collate();
      }

      $sql = "CREATE TABLE {$wpdb->prefix}permalinks_customizer_redirects (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        redirect_from text NOT NULL,
        redirect_to text NOT NULL,
        type varchar(20) NOT NULL DEFAULT 'post',
        redirect_status varchar(20) NOT NULL DEFAULT 'auto',
        enable tinyint(1) NOT NULL DEFAULT '1',
        redirect_type tinyint(1) NOT NULL DEFAULT '0',
        count BIGINT UNSIGNED NOT NULL DEFAULT '0',
        last_accessed datetime,
        PRIMARY KEY (id)
      ) $collate";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
    }
  }

  /**
   * Loads the plugin language files.
   *
   * @since 2.0.0
   * @access public
   */
  public function load_textdomain() {
    load_plugin_textdomain( 'permalinks-customizer', FALSE,
      basename( dirname( PERMALINKS_CUSTOMIZER_FILE ) ) . '/languages/'
    );
    $this->update_taxonomy_table();

    $capability = get_option( 'permalinks_customizer_capabilities', -1 );
    if ( -1 === $capability ) {
      self::plugin_activate();
    }
  }

  /**
   * Check Version if the version is not defined or less than to the current
   * plugin function then update the `permalinks_customizer_table`.
   *
   * @since 2.0.0
   * @access private
   */
  private function update_taxonomy_table() {
    $current_version = get_option( 'permalinks_customizer_plugin_version', -1 );
    if ( -1 === $current_version
      || PERMALINKS_CUSTOMIZER_PLUGIN_VERSION < $current_version ) {
      require_once(
        PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-update-taxonomy-table.php'
      );
      new Permalinks_Customizer_Update_Taxonomy();
    } elseif ( PERMALINKS_CUSTOMIZER_PLUGIN_VERSION !== $current_version  ) {
      update_option( 'permalinks_customizer_plugin_version',
        PERMALINKS_CUSTOMIZER_PLUGIN_VERSION
      );
    }
  }
}

new Permalinks_Customizer();
