<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Display About page.
 *
 * Create the About page HTML and print it.
 *
 * @since 1.3.9
 */
class Permalinks_Customizer_About {

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->more_plugins();
  }

  /**
   * More Plugins HTML.
   *
   * @since 1.3.9
   * @access private
   */
  private function more_plugins() {
    $plugin_url = plugins_url( '/admin', PERMALINKS_CUSTOMIZER_FILE );
    $img_src    = $plugin_url . '/images';
    wp_enqueue_style( 'style', $plugin_url . '/css/about-plugins.min.css' );
    $plugin_name = __( 'Permalinks Customizer', 'permalinks-customizer' );
    $button_text = __( 'Check it out', 'permalinks-customizer' );
    $five_star   = '<span class="star">
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="15" height="15">
                      <polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798
                          10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
                      </svg>
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="15" height="15">
                      <polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798
                          10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
                      </svg>
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="15" height="15">
                      <polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798
                          10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
                      </svg>
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="15" height="15">
                      <polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798
                          10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
                      </svg>
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="15" height="15">
                      <polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798
                          10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
                      </svg>
                    </span>';
    ?>

    <div class="wrap">
      <div class="float">
        <h1><?php echo $plugin_name . ' ' . PERMALINKS_CUSTOMIZER_PLUGIN_VERSION; ?></h1>
        <div class="tagline">
          <p><?php _e('Thank you for choosing Permalinks Customizer! We hope that your experience with our plugin for auto creating permalinks and updating permalinks is quick and easy. We are trying to make it more feasible for you and provide capabilities in it.', 'permalinks-customizer' ); ?></p>
          <p><?php printf( __( 'To support future development and help to make it even better by just leaving a <a href="%s" title="Permalinks Customizer Rating" target="_blank">%s</a> rating with a nice message to me :)', 'permalinks-customizer' ), 'https://wordpress.org/support/plugin/permalinks-customizer/reviews/?rate=5#new-post', $five_star ); ?></p>
        </div>
      </div>

      <div class="float">
        <object type="image/svg+xml" data="<?php echo $img_src;?>/permalinks-customizer.svg" width="128" height="128"></object>
      </div>

      <div class="product">
        <h2><?php _e( 'More from YAS Global', 'permalinks-customizer' ); ?></h2>
        <span><?php _e('Our List of Plugins provides the services which helps you to manage your site URLs(Permalinks), Prevent your site from XSS Attacks, Brute force attacks, increase your site visitors by adding Structured JSON Markup and so on.', 'permalinks-customizer' ); ?></span>

        <div class="box recommended">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/prevent-xss-vulnerability.png" style="transform:scale(1.5)" />
          </div>

          <h3><?php _e( 'Prevent XSS Vulnerability', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Secure your site from the <strong>XSS Attacks</strong> so, your users won\'t lose any kind of information or not redirected to any other site by visiting to your site with the <strong>malicious code</strong> in the URL or so. In this way, users can open your site URLs without any hesitation.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/prevent-xss-vulnerability/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/custom-permalinks.svg" />
          </div>

          <h3><?php _e( 'Custom Permalinks', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Custom Permalinks helps you to make your permalinks customized for <em>individual</em> posts, pages, tags or categories. It will <strong>NOT</strong> apply whole permalink structures, or automatically apply a category\'s custom permalink to the posts within that category.', 'permalinks-customizer' ); ?></p>
          <a href="https://www.custompermalinks.com/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box recommended">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/http-auth.svg" />
          </div>

          <h3><?php _e( 'HTTP Auth', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Allows you apply <strong>HTTP Auth</strong> on your site. You can apply HTTP Authentication all over the site or only the admin pages. It helps to stop cralwing on your site while on development or persist the <strong>Brute Attacks</strong> by locking the Admin Pages.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/http-auth/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/schema-for-article.svg" />
          </div>

          <h3><?php _e( 'SCHEMA for Article', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Simply the easiest solution to add valid schema.org as a JSON script in the head of blog posts or articles. You can choose the schema either to show with the type of Article or NewsArticle from the settings page.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/schema-for-article/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/make-paths-relative.svg" />
          </div>

          <h3><?php _e( 'Make Paths Relative', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Convert the paths(URLs) to relative instead of absolute. You can make <strong>Post</strong>, <strong>Category</strong>, <strong>Archive</strong>, <strong>Image</strong> URLs and <strong>Script</strong> and <strong>Style</strong> src as per your requirement. You can choose which you want to be relative from the settings Page.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/make-paths-relative/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/json-structuring-markup.svg" />
          </div>

          <h3><?php _e( 'JSON Structuring Markup', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'Simply the easiest solution to add valid schema.org as a JSON script in the head of posts and pages. It provides you multiple <strong>SCHEMA</strong> types like Article, News Article, Organization and Website Schema.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/json-structuring-markup/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/remove-links-and-scripts.svg" />
          </div>

          <h3><?php _e( 'Remove Links and Scripts', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'It removes some meta data from the wordpress header so, your header keeps clean of useless information like <strong>shortlink</strong>, <strong>rsd_link</strong>, <strong>wlwmanifest_link</strong>, <strong>emoji_scripts</strong>, <strong>wp_embed</strong>, <strong>wp_json</strong>, <strong>emoji_styles</strong>, <strong>generator</strong> and so on.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/remove-links-and-scripts/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>

        <div class="box">
          <div class="img">
            <img src= "<?php echo $img_src; ?>/media-post-permalink.png" style="transform:scale(1.5)" />
          </div>

          <h3><?php _e( 'Media Post Permalink', 'permalinks-customizer' ); ?></h3>
          <p><?php _e( 'On uploading  any image,  let\'s say services.png, WordPress creates the <strong>attachment post</strong> with the permalink of <strong>/services/</strong> and doesn\'t allow you to use that permalink to point your page. In this case, we comes up with this great solution.', 'permalinks-customizer' ); ?></p>
          <a href="https://wordpress.org/plugins/media-post-permalink/" class="checkout-button" target="_blank"><?php echo $button_text; ?></a>
        </div>
      </div>
    </div>
    <?php
  }
}
