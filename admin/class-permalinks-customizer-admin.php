<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Main Admin Class.
 *
 * Create Plugin menu, attach it's respective file and initialize it's class.
 *
 * @since 1.0.0
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
   * @since 1.0.0
   * @access public
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
      'Set Taxonomies Permalinks', 'Taxonomies Settings',
      'pc_manage_permalink_settings', 'permalinks-customizer-taxonomies-settings',
      array( $this, 'taxonomies_settings_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'PostTypes Permalinks', 'PostTypes Permalinks', 'pc_manage_permalinks',
      'permalinks-customizer-post-permalinks',
      array( $this, 'post_permalinks_page' )
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
      'Structure Tags', 'Tags', 'pc_manage_permalink_settings',
      'permalinks-customizer-tags', array( $this, 'post_tags_page' )
    );
    add_submenu_page( 'permalinks-customizer-posts-settings',
      'About Permalinks Customizer', 'About', 'install_plugins',
      'permalinks-customizer-about-plugins', array( $this, 'about_plugin' )
    );

    add_filter( 'plugin_action_links_' . PERMALINKS_CUSTOMIZER_BASENAME,
      array( $this, 'settings_link' )
    );

    add_action( 'admin_init', array( $this, 'pc_privacy_policy' ) );
  }

  /**
   * This Function Calls the another Function which shows
   * the PostTypes Settings Page.
   *
   * @since 1.0.0
   * @access public
   */
  public function posts_settings_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttypes-settings.php'
    );
    new Permalinks_Customizer_PostTypes_Settings();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Calls another Function which shows the Taxonomies Settings Page.
   *
   * @since 1.1.0
   * @access public
   */
  public function taxonomies_settings_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomies-settings.php'
    );
    new Permalinks_Customizer_Taxonomies_Settings();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Calls another Function which shows the PostTypes Permalinks Page.
   *
   * @since 1.3.0
   * @access public
   */
  public function post_permalinks_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttype-permalinks.php'
    );
    new Permalinks_Customizer_PostType_Permalinks();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Calls another Function which shows the Taxonomies Permalinks Page.
   *
   * @since 1.3.0
   * @access public
   */
  public function taxonomy_permalinks_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomy-permalinks.php'
    );
    new Permalinks_Customizer_Taxonomy_Permalinks();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Calls another Function which shows the Redirects Page.
   *
   * @access public
   * @since 2.0.0
   */
  public function redirects_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-redirects.php'
    );
    new Permalinks_Customizer_Redirects();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Calls another Function which shows the Tags Page.
   *
   * @since 2.7.0
   * @access public
   */
  public function post_tags_page() {
    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-tags.php'
    );
    new Permalinks_Customizer_Tags();
    add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
  }

  /**
   * Add About Plugins Page.
   *
   * @since 1.3.9
   * @access public
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
   * @since 1.3.3
   * @access public
   *
   * @return string Shows version, website link and twiiter.
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
   * @param array $links Contains the Plugin Basic Link (Activate/Deactivate/Delete)
   *
   * @return array Plugin Basic Links and added some custome link for Settings,
   *   Contact, and About.
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
   * Add Privacy Policy about the Plugin.
   *
   * @since 2.3.0
   * @access public
   */
  public function pc_privacy_policy() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
      return;
    }
     $content = sprintf(
      __( 'This plugin doesn\'t collects/store any user related information.
       To have any kind of further query please feel free to
      <a href="%s" target="_blank">contact us</a>.',
      'permalinks-customizer' ),
      'https://www.yasglobal.com/#request-form'
    );
     wp_add_privacy_policy_content(
      'Permalinks Customizer',
      wp_kses_post( wpautop( $content, false ) )
    );
  }

  /**
   * Print the Admin Notice.
   *
   * @since 2.0.0
   * @access public
   */
  public function show_admin_notice() {
    if ( isset( $_REQUEST['regenerated_permalink_error'] ) ) {
      if ( 1 == $_REQUEST['regenerated_permalink_error'] ) {
        echo '<div id="message" class="error notice notice-success is-dismissible">' .
                '<p>' . __(
                  'Permalink Structure not found for the selected Taxonomy in the Plugin Settings. Please define the Permalink Structure in the Plugin <a href="/wp-admin/admin.php?page=permalinks-customizer-taxonomies-settings" title="Taxonomies Permalinks Settings" target="_blank">Settings Page</a> to use <i>Regenerate Permalink</i> Action.',
                  'permalinks-customizer'
                ) . '</p>' .
              '</div>';
      } elseif ( 2 == $_REQUEST['regenerated_permalink_error'] ) {
        echo '<div id="message" class="error notice notice-success is-dismissible">' .
                '<p>' . __(
                  'Permalink Structure not found for the selected Post Type in the Plugin Settings and the Permalink Settings of WordPress are set to Plain. Please either define the Permalink Structure in the Plugin <a href="/wp-admin/admin.php?page=permalinks-customizer-posts-settings" title="PostTypes Permalinks Settings" target="_blank">Settings Page</a> or change the <a href="/wp-admin/options-permalink.php" title="Permalink Settings" target="_blank">Permalink Settings</a> of WordPress to use <i>Regenerate Permalink</i> Action.',
                  'permalinks-customizer'
                ) . '</p>' .
              '</div>';
      }
    }

    if ( isset( $_REQUEST['regenerated_permalink'] )
      && is_numeric( $_REQUEST['regenerated_permalink'] )
    ) {
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
