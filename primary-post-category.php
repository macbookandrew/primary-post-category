<?php
/**
 * Plugin Name: Primary Post Category
 * Plugin URI: https://github.com/macbookandrew/primary-post-category
 * Description: A simple plugin to assign a primary and multiple secondary categories to posts
 * Version: 1.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Flush rerite rules on plugin activation
 */
function ppc_flush_rewrite_rules() {
    ppc_register_primary_category();
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'ppc_flush_rewrite_rules' );

if ( ! function_exists( 'ppc_register_primary_category' ) ) {

/**
 * Add primary category tax
 */
function ppc_register_primary_category() {
    $labels = array(
        'name'                       => _x( 'Primary Categories', 'Taxonomy General Name', 'primary-category' ),
        'singular_name'              => _x( 'Primary Category', 'Taxonomy Singular Name', 'primary-category' ),
        'menu_name'                  => __( 'Primary Category', 'primary-category' ),
        'all_items'                  => __( 'All Categories', 'primary-category' ),
        'parent_item'                => __( 'Parent Category', 'primary-category' ),
        'parent_item_colon'          => __( 'Parent Category:', 'primary-category' ),
        'new_item_name'              => __( 'New Category Name', 'primary-category' ),
        'add_new_item'               => __( 'Add New Category', 'primary-category' ),
        'edit_item'                  => __( 'Edit Category', 'primary-category' ),
        'update_item'                => __( 'Update Category', 'primary-category' ),
        'view_item'                  => __( 'View Category', 'primary-category' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'primary-category' ),
        'add_or_remove_items'        => __( 'Add or remove categories', 'primary-category' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'primary-category' ),
        'popular_items'              => __( 'Popular Categories', 'primary-category' ),
        'search_items'               => __( 'Search Categories', 'primary-category' ),
        'not_found'                  => __( 'Not Found', 'primary-category' ),
        'no_terms'                   => __( 'No categories', 'primary-category' ),
        'items_list'                 => __( 'Categories list', 'primary-category' ),
        'items_list_navigation'      => __( 'Categories list navigation', 'primary-category' ),
    );
    $rewrite = array(
        'slug'                       => 'primary-category',
        'with_front'                 => true,
        'hierarchical'               => false,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $rewrite,
        'show_in_rest'               => true,
    );
    register_taxonomy( 'primary_category', array( 'post' ), $args );
}
add_action( 'init', 'ppc_register_primary_category', 0 );

}

