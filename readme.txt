=== Permalinks Customizer ===
Contributors: aliya-yasir, sasiddiqui
Donate link: https://www.paypal.me/yasglobal
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: address, category, custom, custom permalink, custom post permalinks, link, permalink, rewrite slug, redirects, slug, tags, url, custom taxonomy
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 1.3.8

Set permalinks for default PostTypes, custom PostTypes, default Taxonomies (Category/Tags) and custom Taxonomies which can be changed from the edit page.

== Description ==

Customize your URL and set the slug. You can use basic keywords which is defined by the wordpress for defining the permalinks as well as some new keywords which is defined by this plugin. All the keywords are defined on the Tags page under Permalinks Customizer.

By using <strong>Permalinks Customizer</strong> you can set the different permalink structure for each post-type and taxonomy.

= How to set the Permalinks for the PostTypes seperately =

Let's assume that you have 6 <strong>PostTypes</strong> and they all have different style of <strong>permalinks</strong>. Like: 

1. <strong>Blog :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/blog/year-month-date-postname/
2. <strong>Customers :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/customers/postname/
3. <strong>Events :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/events/year-month-date-postname/
4. <strong>Press :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/press/category/year/postname/
5. <strong>News :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/news/year/postname/
6. <strong>Sponsors :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/company/sponsor/post_title/

This plugin allows you to do this very easily. You just need to go on <strong>Permalinks Customizer</strong> Settings Page. Where text fields are shown with post-type name. You can define your permalinks you want to create for each post type. 

If you leave the some post-type fields empty. So, <strong>Permalinks Customizer</strong> would create a permalink for that post-type by using the default <strong>permalink</strong> settings.

> <strong>How to Configure Permalinks Customizer</strong>
> 
> You can configure the plugin by going to the menu `Permalinks Customizer` that appears in your admin menu.<br><br><strong>                                   OR</strong><br><br> http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-posts-settings

== Structure Tags ==

=== Tags for PostTypes ===

* <strong>%title%</strong> : Title of the post. let's say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.
* <strong>%year%</strong> : The year of the post, four digits, for example 2004
* <strong>%monthnum%</strong> : Month of the year, for example 05
* <strong>%day%</strong> : Day of the month, for example 28
* <strong>%hour%</strong> : Hour of the day, for example 15
* <strong>%minute%</strong> : Minute of the hour, for example 43
* <strong>%second%</strong> : Second of the minute, for example 33
* <strong>%post_id%</strong> : The unique ID # of the post, for example 423
* <strong>%postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.
* <strong>%parent_postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Page Slug</strong> if any parent page is selected before publishing.
* <strong>%all_parents_postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains all the <strong>Parent Page Slugs</strong> if any parent page is selected before publishing.
* <strong>%category%</strong> : A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.
* <strong>%child-category%</strong> : A sanitized version of the category name (category slug field on New/Edit Category panel).
* <strong>%product_cat%</strong> : A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI. <i>This <strong>tag</strong> is specially used for WooCommerce Products.</i>
* <strong>%author%</strong> : A sanitized version of the author name.
* <strong>%author_firstname%</strong> : A sanitized version of the author first name. If author first name is not available so, it uses the author\'s username.
* <strong>%author_lastname%</strong> : A sanitized version of the author last name. If author last name is not available so, it uses the author\'s username.

<strong>Note</strong>: *%postname%* is similar as of the *%title%* tag but the difference is that *%postname%* can only be set once whereas *%title%* can be changed. let's say the title is "This Is A Great Post!" so, it becomes "this-is-a-great-post" in the URI(At the first time, *%postname%* and *%title%* works same) but if you edit and change title let's say "This Is A WordPress Post!" so, *%postname%* in the URI remains same "this-is-a-great-post" whereas *%title%* in the URI becomes "this-is-a-wordpress-post"

=== Tags for Taxonomies ===

* <strong>%name%</strong> : Name of the Term/Category. let's say the name is "External API" so, it becomes external-api in the URI.
* <strong>%term_id%</strong> : The unique ID # of the Term/Category, for example 423
* <strong>%slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI.
* <strong>%parent_slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This Tag contains Immediate Parent Term/Category Slug if any parent Term/Category is selected before adding it.
* <strong>%all_parents_slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This Tag contains all the Parent Term/Category Slug if any parent Term/Category is selected before adding it.

<strong>Be warned:</strong> *This plugin is not a replacement for WordPress's built-in permalink system*. Check your WordPress administration's "Permalinks" settings page first, to make sure that this doesn't already meet your needs.

=== Filter ===

If you want to exclude some Permalink to processed with the plugin so, just add the filter looks like this:
`
function yasglobal_exclude_url( $permalink ) {
  if ( strpos( $permalink, '/contact-us/' ) !== false ) {
    return '__true';
  }
  return;
}
add_filter( 'permalinks_customizer_exclude_request', 'yasglobal_exclude_url' );
`

== Installation ==

1. Upload the `permalinks-customizer` folder to the `/wp-content/plugins/` directory or Directly install the plugin through the WordPress plugins screen.
2. Activate the Permalinks Customizer plugin through the `Plugins` menu in WordPress.
3. Configure the plugin by going to the menu `Permalinks Customizer` that appears in your admin menu.

== Screenshots ==

* Permalinks can be set for each and every PostType from [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-posts-settings). The empty permalink field for the PostType will use the Wordpress Permalink Settings.

* All the available tags which can be used for defining the permalinks for PostTypes are listed [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-post-tags). Some of the tags are defined here may only be use with [Permalinks Customizer](https://wordpress.org/plugins/permalinks-customizer/) plugin only.

* Permalinks can easily be changed for the single post from its post edit page.

* Permalinks can be set for each and every Taxonomies from [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-taxonomies-settings). The empty permalink field for the taxonomy would not create teh permalink for it.

* All the available tags which can be used for defining the permalinks for Taxonomies are listed [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags). These tags may work with this Plugin Only.

* Permalinks can easily be changed for the single Term from its Term edit page.

* You can easily convert the custom permalink URLs to permalink customizer URLs by going on [permalinks settings page](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-convert-url)

* Permalink conversion varies from servr to server. So, make sure to convert the url at a time on depending on your server capability. 

== Frequently Asked Questions ==

= Q. How to define slug of the post type? =
A. Go to Settings, there is a field with the post type name. On this fields, you can define slug for the post type.

= Q. Can i use tags? =
A. Yes, you can use all the tags as defined on the [Permalinks Customizer page](https://wordpress.org/plugins/permalinks-customizer/).

= Q. May this plugin works with custom permalinks? =
A. No, This plugin does not work with [custom permalinks](https://wordpress.org/plugins/custom-permalinks/).

== Changelog ==

= 1.3.8 =

  * Updated Postname if it's not available at the Created Post time

= 1.3.7 =

  * Resolved the Yoast SEO Slug Issue

= 1.3.6 =

  * Resolved the Draft Permalink Issue on editing the description under the snippet of Yoast SEO

= 1.3.5 =

  * Resolved the Pagination Issue for Taxonomies

= 1.3.4 =

  * Applied PHP Coding Standards on WordPress

= 1.3.3 =

  * Enhancements
    * Added Filter to Exclude/Ignore URL to be processed
  * Bugs
    * Fixed Vulnerability Issues

= 1.3.2 =

  * Fixed Setting Custom Post Type Permalink doesn't work and redirects to /wp-admin/option

= 1.3.1 =

  * Added Translation Capability

= 1.3 =

  * Enhancements
    * Added PostTypes Permalinks Page
      * View all the PostTypes permalinks
      * Search Permalinks
      * Sort by Title
      * Pagination
    * Added Taxonomies Permalinks Page
      * View all the Category/Tags permalinks
      * Search Permalinks
      * Sort by Title
      * Pagination

  * Bug Fixes
     * Replaced Deprecated Actions with the newer Actions
     * Breaking the tag permalinks

= 1.2.5 =

  * Added Regenerate Permalink Button

= 1.2.4 =

  * Resolves the Deletes first character of permalink issue using the use-case

= 1.2.3 =

  * Add Auto regenerate Permalink Functionality on Inherit and Add the Compatibility with the Previously (Old) Created Posts

= 1.2.2 =

  * Fixed Permalink Issue for Preview of Draft Post and PHP Notice

= 1.2.1 =

  * Resolved Issues of AMP Pages

= 1.2 =
  
  * Resolved issues with AMP Pages and Added Functionality to regenerate the Permalinks until you publish the post/page or updated the permalink manually

= 1.1 =

  * Added Functionality to create Permalinks automtically on Creating the Term (Default + Custom Taxonomies) and updated DB Query for PostTypes

= 1.0.2 =

  * Fixed PHP Warning of Undefined Variable

= 1.0.1 =

  * Fixed Convert Custom Permalink Empty Page Issue

= 1.0 =

  * Fixed Quick Edit Bug, HomePage does not load Bug, Removed some Deprecated Filters/Actions and Added Permalinks for Custom Taxonomy

= 0.4 =

  * Fixed PolyLang Conflicts 

= 0.3.10 =

 * Fixed TrailingSlash Function Name

= 0.3.9 =

 * Fixed WPML Conflicts

= 0.3.8 =

 * Fixed post_status error under the request() function 

= 0.3.7 =

 * Added 4 new Tags (author_firstname, author_lastname, parent_postname, all_parents_postname)

= 0.3.6 =

 * Added 1 new Tag

= 0.3.5 =

 * Fixed Table Prefix Issue and some PHP Warnings

= 0.3.4 =

 * Fixed draft preview issue 

= 0.3.3 =

 * Fixed PHP undefined index error

= 0.3.2 =

 * Convert custom permalink to permalink customizer

= 0.3.1 =

 * Add product_cat tag which is specially used for WooCommerce Products

= 0.3 =

 * Add functionality to create a permalink for category and tag for default post type

= 0.2 =

 * Front Page Displays as Static Page Functionality

= 0.1 =

 * First release on wordpress.org