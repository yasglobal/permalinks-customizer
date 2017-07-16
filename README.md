# Permalinks Customizer

## Description

This plugin is a fork of [Custom Permalink](https://wordpress.org/plugins/custom-permalinks/). It has some expanded bug patches and some features which were not in the [Custom Permalink](https://wordpress.org/plugins/custom-permalinks/).

Customize your URL and set the slug. You can use basic keywords which is defined by the wordpress for defining the permalinks as well as someother new keywords which is defined by this plugin. All the keywords is defined on the Tags page under Permalinks Customizer.

By using <strong>Permalinks Customizer</strong> you can set the permalinks for each post-type seperately. 

### How to set the Permalinks for the post-types seperately

Let's assume that you have 6 <strong>post-types</strong> and they all have different style of <strong>permalinks</strong>. Like: 

1. <strong>Blog :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/blog/year-month-date-postname/
2. <strong>Customers :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/customers/postname/
3. <strong>Events :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/events/year-month-date-postname/
4. <strong>Press :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/press/category/year/postname/
5. <strong>News :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/news/year/postname/
6. <strong>Sponsors :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/company/sponsor/post_title/

This plugin allows you to do this very easily. You just need to go on <strong>Permalinks Customizer</strong> Settings Page. Where text fields are shown with post-type name. You can define your permalinks you want to create for each post type. 

If you leave the some post-type fields empty. So, <strong>Permalinks Customizer</strong> would create a permalink for that post-type by using the default <strong>permalink</strong> settings.

### How to Configure Permalinks Customizer

> You can configure the plugin by going to the menu `Permalinks Customizer` that appears in your admin menu.<br><br><strong>                                   OR</strong><br><br> http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-settings

## Structure Tags

* <strong>%title%</strong> : Title of the post. let's say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.
* <strong>%year%</strong> : The year of the post, four digits, for example 2004
* <strong>%monthnum%</strong> : Month of the year, for example 05
* <strong>%day%</strong> : Day of the month, for example 28
* <strong>%hour%</strong> : Hour of the day, for example 15
* <strong>%minute%</strong> : Minute of the hour, for example 43
* <strong>%second%</strong> : Second of the minute, for example 33
* <strong>%post_id%</strong> : The unique ID # of the post, for example 423
* <strong>%postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.
* <strong>%category%</strong> : A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.
* <strong>%product_cat%</strong> : A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI. <i>This <strong>tag</strong> is specially used for WooCommerce Products.</i>
* <strong>%author%</strong> : A sanitized version of the author name.

<strong>Note</strong>: *%postname%* is similar as of the *%title%* tag but the difference is that *%postname%* can only be set once whereas *%title%* can be changed. let's say the title is "This Is A Great Post!" so, it becomes "this-is-a-great-post" in the URI(At the first time, *%postname%* and *%title%* works same) but if you edit and change title let's say "This Is A WordPress Post!" so, *%postname%* in the URI remains same "this-is-a-great-post" whereas *%title%* in the URI becomes "this-is-a-wordpress-post"

<strong>Be warned:</strong> *This plugin is not a replacement for WordPress's built-in permalink system*. Check your WordPress administration's "Permalinks" settings page first, to make sure that this doesn't already meet your needs.

## Installation 

1. Upload the `permalinks-customizer` folder to the `/wp-content/plugins/` directory or Directly install the plugin through the WordPress plugins screen.
2. Activate the Permalinks Customizer plugin through the `Plugins` menu in WordPress.
3. Configure the plugin by going to the menu `Permalinks Customizer` that appears in your admin menu

## Frequently Asked Questions

= Q. How to define slug of the post type? =
A. Go to Settings, there is a field with the post type name. On this fields, you can define slug for the post type.

= Q. Can i use tags? =
A. Yes, you can use all the tags as defined on the [Permalinks Customizer page](https://wordpress.org/plugins/permalinks-customizer/).

= Q. May this plugin works with custom permalinks? =
A. No, This plugin does not work with [custom permalinks](https://wordpress.org/plugins/custom-permalinks/).
