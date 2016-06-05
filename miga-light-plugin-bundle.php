<?php
/*
Plugin Name:  Miga Light Plugin Bundle
Plugin URI:   http://webgeckos.com
Description:  Theme specific plugins selection for WebGeckos WordPress Theme Miga Light
Version:      1.0.0
Author:       Danijel Rose
Author URI:   http://webgeckos.com
Text Domain:  miga
License:      GPL-2.0+
License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
*/

/*
    1. ENQUEUE SCRIPTS
    2. ACTION HOOKS
    3. FILTER HOOKS
    4. SHORTCODES
    5. CUSTOM POST TYPES
    6. CUSTOM TAXONOMIES
    7. OTHER FUNCTIONS
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
  exit;
}

/* 1. ENQUEUE SCRIPTS */

/* 2. ACTION HOOKS */

// hook for registering the shortcodes
add_action('init', 'miga_register_shortcodes');

// hook for registering custom post types and taxonomies
add_action( 'init', 'miga_init' );

// hook for setting default term for taxonomy display-locations
add_action( 'save_post', 'miga_set_default_object_terms', 100, 2 );

/* 3. FILTER HOOKS */

/* 4. SHORTCODES */

// register shortcodes
function miga_register_shortcodes() {
  add_shortcode('miga_btn', 'miga_button_shortcode');
}

// adding shortcode for custom button -> doesn't work with excerpt -> use the_content!!!
function miga_button_shortcode($atts) {
    extract(shortcode_atts(array(
        'text' => 'more',
        'url' => 'http://yourdomain.com',
        ),$atts));
    return sprintf('<br><a href="%1$s" class="btn btn-lg">%2$s</a><br><br>',
        esc_url( $url ),
        esc_html( $text )
    );
}

/* 5. CUSTOM POST TYPES */

if ( ! function_exists( 'miga_init' ) ) :

  function miga_init() {

  /*
  * Register custom post types
  */

  register_post_type('sections', array(
      'labels'        => array(
              'name' => __( 'Sections', 'miga' ),
              'singular_name' => __( 'Section', 'miga' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
      'menu_icon'     => 'dashicons-admin-generic',
      'has_archive'   => true,
      'show_in_menu'  => true,
      'taxonomies'    => array( 'display-locations', 'display-options' )
  ));

/* 6. CUSTOM TAXONOMIES */

  // Use categories and tags with attachments
  register_taxonomy_for_object_type( 'category', 'attachment' );
  register_taxonomy_for_object_type( 'post_tag', 'attachment' );

/*
* Register custom taxonomies
*/

  register_taxonomy('display-locations', 'sections', array(
      'labels' =>
          array(
              'name' => __( 'Display Locations', 'miga' ),
              'singular_name' => __( 'Display Location', 'miga' ),
              'add_new_item' => __( 'Add New Display Location', 'miga' ),
              'edit_item' => __( 'Edit Display Location', 'miga' ),
      'update_item' => __( 'Update Display Location', 'miga' )
          ),
      'hierarchical' => true,
      'show_admin_column' => true
  ));

  }
endif;

/* 7. OTHER FUNCTIONS */

/**
 * Define default terms for custom taxonomies in WordPress 3.0.1
 *
 * @author    Michael Fields     http://wordpress.mfields.org/
 * @props     John P. Bloch      http://www.johnpbloch.com/
 *
 * @since     2010-09-13
 * @alter     2010-09-14
 *
 * @license   GPLv2
 */
function miga_set_default_object_terms( $post_id, $post ) {
    if ( $post->post_type === 'sections' ) {
        $defaults = array(
            'display-locations' => array( 'home' ), // more default display locations can be defined here
            );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
}
