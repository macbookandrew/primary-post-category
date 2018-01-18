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

/**
 * Register Primary Category widget
 */
add_action( 'widgets_init', function() {
    register_widget( 'WP_Widget_Primary_Categories' );
});

/**
 * Core class used to implement a Categories widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Primary_Categories extends WP_Widget {

    /**
     * Sets up a new Categories widget instance.
     *
     * @since 2.8.0
     * @access public
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'widget_categories widget_primary_categories',
            'description' => __( 'A list or dropdown of primary categories.' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'categories', __( 'Primary Categories' ), $widget_ops );
    }

    /**
     * Outputs the content for the current Categories widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Categories widget instance.
     */
    public function widget( $args, $instance ) {
        static $first_dropdown = true;

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Primary Categories' ) : $instance['title'], $instance, $this->id_base );

        $c = ! empty( $instance['count'] ) ? '1' : '0';
        $h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
        $d = ! empty( $instance['dropdown'] ) ? '1' : '0';

        echo $args['before_widget'];
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'taxonomy'     => 'primary_category',
            'orderby'      => 'name',
            'show_count'   => $c,
            'hierarchical' => $h,
        );

        if ( $d ) {
            $dropdown_id = ( $first_dropdown ) ? 'cat' : "{$this->id_base}-dropdown-{$this->number}";
            $first_dropdown = false;

            echo '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

            $cat_args['show_option_none'] = __( 'Select Primary Category' );
            $cat_args['id'] = $dropdown_id;

            /**
             * Filters the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             */
            wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
            ?>

<script type='text/javascript'>
/* <![CDATA[ */
(function() {
    var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
    function onCatChange() {
        if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
            location.href = "<?php echo home_url(); ?>/?cat=" + dropdown.options[ dropdown.selectedIndex ].value;
        }
    }
    dropdown.onchange = onCatChange;
})();
/* ]]> */
</script>

<?php
        } else {
?>
        <ul>
<?php
        $cat_args['title_li'] = '';

        /**
         * Filters the arguments for the Categories widget.
         *
         * @since 2.8.0
         *
         * @param array $cat_args An array of Categories widget options.
         */
        wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
?>
        </ul>
<?php
        }

        echo $args['after_widget'];
    }

    /**
     * Handles updating settings for the current Categories widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

        return $instance;
    }

    /**
     * Outputs the settings form for the Categories widget.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        //Defaults
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = sanitize_text_field( $instance['title'] );
        $count = isset($instance['count']) ? (bool) $instance['count'] :false;
        $hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

        <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
        <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
        <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
        <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
        <?php
    }

}
