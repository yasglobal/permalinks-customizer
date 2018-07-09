<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Admin {

  /**
   * Initializes WordPress hooks.
   */
  function __construct() {
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
  }

  /**
   * Added Pages in Menu for Settings.
   *
   * @access public
   * @since 0.1
   *
   * @return void
   */
  public function admin_menu() {
    add_menu_page( 'Set Your Permalinks', 'Permalinks Customizer',
      'pc_manage_permalink_settings', 'permalinks-customizer-posts-settings',
      array( $this,'posts_settings_page' ), 'dashicons-admin-links'
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'PostTypes Settings', 'PostTypes Settings',
      'pc_manage_permalink_settings', 'permalinks-customizer-posts-settings',
      array( $this, 'posts_settings_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Structure Tags for PostTypes', 'PostTypes Tags',
      'pc_manage_permalink_settings', 'permalinks-customizer-post-tags',
      array( $this, 'post_tags_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'PostTypes Permalinks', 'PostTypes Permalinks', 'pc_manage_permalinks',
      'permalinks-customizer-post-permalinks',
      array( $this, 'post_permalinks_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Set Taxonomies Permalinks', 'Taxonomies Settings',
      'pc_manage_permalink_settings', 'permalinks-customizer-taxonomies-settings',
      array( $this, 'taxonomies_settings_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Structure Tags for Taxonomies', 'Taxonomies Tags',
      'pc_manage_permalink_settings', 'permalinks-customizer-taxonomy-tags',
      array( $this, 'taxonomy_tags_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Taxonomies Permalinks', 'Taxonomies Permalinks', 'pc_manage_permalinks',
      'permalinks-customizer-taxonomy-permalinks',
      array( $this, 'taxonomy_permalinks_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Redirects', 'Redirects', 'pc_manage_permalink_redirects',
      'permalinks-customizer-redirects',
      array( $this, 'redirects_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'Convert Custom Permalinks', 'Convert CP', 'pc_manage_permalink_settings',
      'permalinks-customizer-convert-url', array( $this, 'convert_url' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'About Permalinks Customizer', 'About', 'install_plugins',
      'permalinks-customizer-about-plugins', array( $this, 'about_plugin' )
    );

    add_filter( 'plugin_action_links_' . PERMALINKS_CUSTOMIZER_BASENAME,
      array( $this, 'settings_link' )
    );
  }

  /**
   * This Function Calls the another Function which shows
   * the PostTypes Settings Page.
   *
   * @access public
   * @since 0.1
   *
   * @return void
   */
  public function posts_settings_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttypes-settings.php'
    );
    new Permalinks_Customizer_PostTypes_Settings();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the PostTypes Tags Page.
   *
   * @access public
   * @since 0.1
   *
   * @return void
   */
  public function post_tags_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-post-tags.php'
    );
    new Permalinks_Customizer_Post_Tags();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the PostTypes Permalinks Page.
   *
   * @access public
   * @since 1.3
   *
   * @return void
   */
  public function post_permalinks_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttype-permalinks.php'
    );
    new Permalinks_Customizer_PostType_Permalinks();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the Taxonomies Settings Page.
   *
   * @access public
   * @since 1.1
   *
   * @return void
   */
  public function taxonomies_settings_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomies-settings.php'
    );
    new Permalinks_Customizer_Taxonomies_Settings();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the Taxonomies Tags Page.
   *
   * @access public
   * @since 1.1
   *
   * @return void
   */
  public function taxonomy_tags_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomy-tags.php'
    );
    new Permalinks_Customizer_Taxonomy_Tags();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the Taxonomies Permalinks Page.
   *
   * @access public
   * @since 1.3
   *
   * @return void
   */
  public function taxonomy_permalinks_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomy-permalinks.php'
    );
    new Permalinks_Customizer_Taxonomy_Permalinks();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which shows
   * the Redirects Page.
   *
   * @access public
   * @since 2.0.0
   *
   * @return void
   */
  public function redirects_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-redirects.php'
    );
    new Permalinks_Customizer_Redirects();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * This Function Calls the another Function which provide the functionality
   * to convert Custom Permalink URLs to Permalinks Customizer.
   *
   * @access public
   * @since 1.0
   *
   * @return void
   */
  public function convert_url() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-batch-script.php'
    );
    new Permalinks_Customizer_Batch_Script();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Add About Plugins Page.
   *
   * @access public
   * @since 1.3.9
   *
   * @return void
   */
  public function about_plugin() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-about.php'
    );
    new Permalinks_Customizer_About();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Add Plugin Support and Follow Message in the footer of Admin Pages.
   *
   * @access public
   * @since 1.3.3
   *
   * @return string
   */
  public function admin_footer_text() {
    $footer_text = sprintf(
      __( 'Permalinks Customizer version %s by <a href="%s" title="YAS Global Website" target="_blank">YAS Global</a> - <a href="%s" title="Support forums" target="_blank">Support forums</a> - Follow on Twitter: <a href="%s" title="Follow YAS Global on Twitter" target="_blank">YAS Global</a>', 'permalinks-customizer' ),
      PERMALINKS_CUSTOMIZER_PLUGIN_VERSION, 'https://www.yasglobal.com',
      'https://wordpress.org/support/plugin/permalinks-customizer',
      'https://twitter.com/samisiddiqui91'
    );
    return $footer_text;
  }

  /**
   * Plugin About, Contact and Settings Link on the Plugin Page under
   * the Plugin Name.
   *
   * @access public
   * @since 1.3.9
   *
   * @param array $links
   *   Contains the Plugin Basic Link (Activate/Deactivate/Delete)
   *
   * @return array $links
   *   Returns the Plugin Basic Links and added some custome link for Settings,
   *   Contact and About.
   */
  public function settings_link( $links ) {
    $about = sprintf(
      __( '<a href="%s" title="About">About</a>', 'permalinks-customizer' ),
      'admin.php?page=permalinks-customizer-about-plugins'
    );
    $contact = sprintf(
      __( '<a href="%s" title="Contact">Contact</a>', 'permalinks-customizer' ),
      'https://www.yasglobal.com/#request-form'
    );
    $settings_link = sprintf(
      __( '<a href="%s" title="Settings">Settings</a>', 'permalinks-customizer' ),
      'admin.php?page=permalinks-customizer-posts-settings'
    );
    array_unshift( $links, $settings_link );
    array_unshift( $links, $contact );
    array_unshift( $links, $about );

    return $links;
  }

  /**
   * Print the Admin Notice.
   *
   * @access public
   * @since 4.0.0
   *
   * @return void
   */
  public function show_admin_notice() {
    if ( isset( $_REQUEST['regenerated_permalink_error'] ) ) {
      if ( 1 == $_REQUEST['regenerated_permalink_error'] ) {
        print '<div id="message" class="error notice notice-success is-dismissible">' .
                '<p>' . __(
                  'Permalink Structure not found for the selected Taxonomy in the Plugin Settings. Please define the Permalink Structure in the Plugin <a href="/wp-admin/admin.php?page=permalinks-customizer-taxonomies-settings" title="Taxonomies Permalinks Settings" target="_blank">Settings Page</a> to use <i>Regenerate Permalink</i> Action.',
                  'permalinks-customizer'
                ) . '</p>' .
              '</div>';
      } elseif ( 2 == $_REQUEST['regenerated_permalink_error'] ) {
        print '<div id="message" class="error notice notice-success is-dismissible">' .
                '<p>' . __(
                  'Permalink Structure not found for the selected Post Type in the Plugin Settings and the Permalink Settings of WordPress are set to Plain. Please either define the Permalink Structure in the Plugin <a href="/wp-admin/admin.php?page=permalinks-customizer-posts-settings" title="PostTypes Permalinks Settings" target="_blank">Settings Page</a> or change the <a href="/wp-admin/options-permalink.php" title="Permalink Settings" target="_blank">Permalink Settings</a> of WordPress to use <i>Regenerate Permalink</i> Action.',
                  'permalinks-customizer'
                ) . '</p>' .
              '</div>';
      }
    }

    if ( isset( $_REQUEST['regenerated_permalink'] )
      && is_numeric( $_REQUEST['regenerated_permalink'] ) ) {
      $permalink = intval( $_REQUEST['regenerated_permalink'] );
      printf( '<div id="message" class="updated notice notice-success is-dismissible"><p>' .
        _n( '%s Permalink has been regenerated.',
          '%s Permalinks have been regenerated.',
          $permalink,
          'permalinks-customizer'
        ) . '</p></div>', $permalink );
    }
  }
}
