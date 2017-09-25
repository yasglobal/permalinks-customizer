<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Admin {

	/**
	 * Initializes WordPress hooks
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
  }

	/**
	 * Added Pages in Menu for Settings
	 */
	public function admin_menu() {
		add_menu_page( 'Set Your Permalinks', 'Permalinks Customizer', 'administrator', 'permalinks-customizer-posts-settings', array( $this,'posts_settings_page' ), 'dashicons-admin-links' );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'PostTypes Settings', 'PostTypes Settings', 'administrator', 'permalinks-customizer-posts-settings', array( $this, 'posts_settings_page' ) );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Structure Tags for PostTypes', 'PostTypes Tags', 'administrator', 'permalinks-customizer-post-tags', array( $this, 'post_tags_page' ) );
    add_submenu_page( 'permalinks-customizer-posts-settings', 'PostTypes Permalinks', 'PostTypes Permalinks', 'administrator', 'permalinks-customizer-post-permalinks', array( $this, 'post_permalinks_page' ) );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Set Taxonomies Permalinks', 'Taxonomies Settings', 'administrator', 'permalinks-customizer-taxonomies-settings', array( $this, 'taxonomies_settings_page' ) );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Structure Tags for Taxonomies', 'Taxonomies Tags', 'administrator', 'permalinks-customizer-taxonomy-tags', array( $this, 'taxonomy_tags_page' ) );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Taxonomies Permalinks', 'Taxonomies Permalinks', 'administrator', 'permalinks-customizer-taxonomy-permalinks', array( $this, 'taxonomy_permalinks_page' ) );
		add_submenu_page( 'permalinks-customizer-posts-settings', 'Convert Custom Permalink', 'Convert Custom Permalink', 'administrator', 'permalinks-customizer-convert-url', array( $this, 'convert_url' ) );
	}

	/**
	 * This Function Calls the another Function which shows the PostTypes Settings Page
	 */
	public function posts_settings_page() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttypes-settings.php' );
		new Permalinks_Customizer_PostTypes_Settings();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which shows the PostTypes Tags Page
	 */
	public function post_tags_page() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-post-tags.php' );
		new Permalinks_Customizer_Post_Tags();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which shows the PostTypes Permalinks Page
	 */
	public function post_permalinks_page() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-posttype-permalinks.php' );
		new Permalinks_Customizer_PostType_Permalinks();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which shows the Taxonomies Settings Page
	 */
	public function taxonomies_settings_page() {
		require_once(PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomies-settings.php' );
		new Permalinks_Customizer_Taxonomies_Settings();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which shows the Taxonomies Tags Page
	 */
	public function taxonomy_tags_page() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomy-tags.php' );
		new Permalinks_Customizer_Taxonomy_Tags();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which shows the Taxonomies Settings Page
	 */
	public function taxonomy_permalinks_page() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-taxonomy-permalinks.php' );
		new Permalinks_Customizer_Taxonomy_Permalinks();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * This Function Calls the another Function which provide the functionality to convert Custom Permalink URLs to Permalinks Customizer
	 */
	public function convert_url() {
		require_once( PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-batch-script.php' );
		new Permalinks_Customizer_Batch_Script();
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Add Rating Message in the footer of Admin Pages of Permalinks Customizer
	 */
  public function admin_footer_text() {
		/* translators: %s: five stars */
		$footer_text = sprintf( __( 'If you like <strong>Permalinks Customizer</strong> please leave us a %s rating. A huge thanks in advance!', 'permalinks-customizer' ), '<a href="https://wordpress.org/support/plugin/permalinks-customizer/reviews?rate=5#new-post" target="_blank" data-rated="' . esc_attr__( 'Thanks :)', 'permalinks-customizer' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>' );
		return $footer_text;
  }
}