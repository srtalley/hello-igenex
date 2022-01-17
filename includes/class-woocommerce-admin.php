<?php 
/**
 * v1.0
 */
namespace IGeneX\Theme\WooCommerce;

class Admin {

    public function __construct() {
        add_filter( 'product_type_options', array($this, 'add_test_panel_product_option') );
        add_action( 'woocommerce_process_product_meta_simple', array($this, 'save_test_panel_product_option')  );

        add_filter( 'acf/load_field/name=test_panel_individual_tests', array($this, 'acf_get_product_titles_for_test_panel') );
        // add_filter( 'acf/load_field/name=individual_test_selection', array($this, 'acf_get_product_titles_for_test_panel') );

        
        add_action( 'admin_head', array($this, 'admin_product_scripts') );


        add_action( 'init', array($this, 'remove_woocommerce_editor') );

        add_action( 'current_screen', array($this,'gl_woocommerce_product_admin'), 10, 1 );

    }
    /** 
     * Remove the regular post editor as it's not being used
     */
    public function remove_woocommerce_editor() {
        remove_post_type_support( 'product', 'editor' );
    }
    /**
    * Move the product metaboxes to the left in the admin area.
    */
    public function gl_woocommerce_product_admin($current_screen) {
        if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

            if($current_screen->id == 'product') {

                // prevent loading user custom order
                add_filter( 'get_user_option_meta-box-order_product', '__return_empty_string' );

                // change the order
                add_action( 'add_meta_boxes', array($this, 'woocommerce_change_product_metaboxes'), 99 );

                // add theCSS
                add_action('admin_head', array($this, 'woocommerce_product_admin_metaboxes'), 1000);

            }
        } // end if 
    } // end function gl_woocommerce_product_admin

    /**
     * Move the position of the product metaboxes
     */
    public function woocommerce_change_product_metaboxes() {
        global $wp_meta_boxes;
        // Set up the 'normal' location with 'high' priority.
        if ( empty( $wp_meta_boxes['product']['normal'] ) ) {
            $wp_meta_boxes['product']['normal'] = [];
        }
        if ( empty( $wp_meta_boxes['product']['normal']['high'] ) ) {
            $wp_meta_boxes['product']['normal']['high'] = [];
        }

        // Move the post excerpt
        $mainarea_excerpt_metabox = $wp_meta_boxes['product']['normal']['default']['postexcerpt'];
        unset($wp_meta_boxes['product']['normal']['default']['postexcerpt']);
        $wp_meta_boxes['product']['normal']['high']['postexcerpt'] = $mainarea_excerpt_metabox;
        
        // Move the product metabox
        $mainarea_woo_product_data_metabox = $wp_meta_boxes['product']['normal']['high']['woocommerce-product-data'];
        unset($wp_meta_boxes['product']['normal']['high']['woocommerce-product-data']);
        $wp_meta_boxes['product']['normal']['high']['woocommerce-product-data'] = $mainarea_woo_product_data_metabox;

        // Move the test panel box
        $mainarea_test_panel_metabox = $wp_meta_boxes['product']['normal']['high']['acf-group_61c3e29078196'];
        unset($wp_meta_boxes['product']['normal']['high']['acf-group_61c3e29078196']);
        $wp_meta_boxes['product']['normal']['high']['acf-group_61c3e29078196'] = $mainarea_test_panel_metabox;

        // Move the additional tab boxes
        $mainarea_product_tabs_metabox = $wp_meta_boxes['product']['normal']['high']['acf-group_61b6c3a104497'];
        unset($wp_meta_boxes['product']['normal']['high']['acf-group_61b6c3a104497']);
        $wp_meta_boxes['product']['normal']['high']['acf-group_61b6c3a104497'] = $mainarea_product_tabs_metabox;

        // Move the Yoast boxes
        $mainarea_yoast_metabox = $wp_meta_boxes['product']['normal']['high']['wpseo_meta'];
        unset($wp_meta_boxes['product']['normal']['high']['wpseo_meta']);
        $wp_meta_boxes['product']['normal']['low']['wpseo_meta'] = $mainarea_yoast_metabox;

    }
    
    /**
     * CSS for product metaboxes & JS to remove the ability to move or collapse
     * boxes in the admin screen
     */
    public function woocommerce_product_admin_metaboxes() {
        ?>
        <style type="text/css">
            #acf-group_61b6c3a104497 .inside.acf-fields {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
            }
            #acf-group_61b6c3a104497 .inside.acf-fields .acf-field {
                flex: 1 1 30%;
                min-width: 250px;
            }
            #acf-group_61c3e29078196 .inside.acf-fields .acf-input {
                /* column-count: 3; */
                /* column-width: 200px; */
            }
            #acf-group_61c3e29078196 .inside.acf-fields .acf-input li {
                padding-left: 24px;
            }
            #acf-group_61c3e29078196 .inside.acf-fields .acf-input li input[type="checkbox"] {
                margin-left: -24px;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                setTimeout(function() {
                    // disable dragging and dropping
                    $('.meta-box-sortables').sortable({
                        disabled: true
                    });
                    $('.postbox .hndle').css('cursor', 'auto');

                    // open closed metaboxes
                    $('.postbox .hndle').unbind('click.postboxes');
                    $('.postbox .handlediv').remove();
                    $('.postbox').removeClass('closed');
                }, 5000);
            });
        </script>
        <?php
    }
    /**
     * Add 'Test Panel' product option
     */
    public function add_test_panel_product_option( $product_type_options ) {

        $product_type_options['test_panel'] = array(
            'id'            => '_test_panel',
            'wrapper_class' => 'show_if_simple show_if_variable',
            'label'         => __( 'Test Panel', 'woocommerce' ),
            'description'   => __( 'Enables the test panel selections.', 'woocommerce' ),
            'default'       => 'no'
        );

        return $product_type_options;

    } // end function add_test_panel_product_option

    /**
     * Save the custom fields.
     */
    public function save_test_panel_product_option( $post_id ) {

        //test panel is checked
        $is_test_panel = isset( $_POST['_test_panel'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_test_panel', $is_test_panel );

    }
    /**
     * Show the product titles in the test panel box
     */
    function acf_get_product_titles_for_test_panel( $field ) {
        $field['choices'] = array();

        // Get the current Product titles
        //see if there are other posts with the same post title
        $product_title_query = new \WP_Query(
            array(
              'post_type' => 'product',
              'post_status' => 'published',
              'posts_per_page' => -1,
              'orderby' => 'title',
              'order' => 'ASC',
              'tax_query' => array(
                  array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms' => 'individual-tests',
                  )
              )
            //   'fields' => 'ids'
            )
        );
        wp_reset_postdata();
        while ($product_title_query->have_posts()) : $product_title_query->the_post();
            $value = get_the_ID();
            $sku = get_post_meta($value, '_sku', true);
            $label = $sku . ' - ' . get_the_title();
            
            $field['choices'][ $value ] = $label;
            
        endwhile;
        wp_reset_query();
        return $field;
    }
    public function admin_product_scripts() {
        $screen = get_current_screen();
        if( is_object($screen) && 'product' == $screen->post_type ) {
        ?>
            <script>
                jQuery( document ).ready( function( $ ) {
    
                    // show or hide the tab based on the checkbox
                    $( 'input#_test_panel' ).change( function() {
                        if($(this).prop('checked')) {
                            $("#acf-group_61c3e29078196").show();
                        } else {
                            $("#acf-group_61c3e29078196").hide();
                        }
                    });
                    $( 'input#_test_panel' ).trigger( 'change' );

                });
            </script><?php
        } // end if
    }
} // end class

$igenex_theme_woocommerce_admin = new Admin();