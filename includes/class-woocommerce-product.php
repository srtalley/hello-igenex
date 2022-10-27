<?php
namespace IGeneX\Theme\WooCommerce;

class Product {

    public function __construct() {

        add_action( 'woocommerce_single_product_summary', array($this, 'add_test_panel_info'), 10, 1);
        add_filter( 'woocommerce_short_description', array($this, 'add_test_panel_info'), 10, 1);
        add_filter( 'woocommerce_product_tabs', array($this, 'add_product_information_tabs') );
        add_action( 'woocommerce_product_meta_end', array($this, 'show_additional_info'), 10, 1);
        add_filter( 'woocommerce_breadcrumb_home_url', array($this, 'woo_custom_breadrumb_home_url') );

    } // end function 

    /**
     * Add the additional test information tabs
     */
    public function add_product_information_tabs( $tabs ) {

        unset($tabs['additional_information']);
        // first see if the product info exists before showing the
        // additional tabs
        if($this->get_specific_tab_content('tab_advantages') != '') {
            $tabs['advantages_tab'] = array(
                'title'     => __( 'Advantages', 'woocommerce' ),
                'priority'  => 10,
                'callback'  => array($this, 'tab_content_advantages')
            );
        }
        if($this->get_specific_tab_content('tab_test_interpretations') != '') {
            $tabs['test_interpretations_tab'] = array(
                'title'     => __( 'Test Interpretations', 'woocommerce' ),
                'priority'  => 10,
                'callback'  => array($this, 'tab_content_test_interpretations')
            );
        }
        if($this->get_specific_tab_content('tab_limitations') != '') {
            $tabs['limitations_tab'] = array(
                'title'     => __( 'Limitations', 'woocommerce' ),
                'priority'  => 10,
                'callback'  => array($this, 'tab_content_limitations')
            );
        }
        return $tabs;
    }
    public function add_test_panel_info($excerpt) {
        // first see if this is an individual test or a test panel
        $is_test_panel = get_post_meta( get_the_ID(), '_test_panel', true );

        if( !$is_test_panel ) {
            // this is not a test panel; do nothing else.
            return $excerpt;
        }

        // get the product object
        $product = wc_get_product(get_the_ID());
        
        // get the test product IDs
        $test_panel_individual_tests = get_field('test_panel_individual_tests', $product->get_id(), true, true);

        // if no tests are selected, then return
        if(empty($test_panel_individual_tests) || $test_panel_individual_tests == '') {
            return $excerpt;
        }

        // get the description
        $test_panel_description = get_field('test_panel_description', $product->get_id(), true, true);

        // get the categories
        // if ($product->post_type == 'product_variation') {
        //     $terms = get_the_terms($product->get_parent_id(), 'product_cat');
        // } else {
        //     $terms = get_the_terms($product->get_id(), 'product_cat');
        // }
        // if ($terms) {
        //     foreach ($terms as $term) {
        //         if($term->slug == 'individual-tests' || $term->slug == 'test-panels') {
        //             continue;
        //         } else {
        //             $parent_term_link = '';
        //             // see if there's a parent term
        //             if($term->parent > 0) {
        //                 $parent_term_object = get_term($term->parent);
        //                 $parent_term_link = '<a href="' . get_term_link($parent_term_object->term_id) . '" data-filter-type="category" data-filter="' . $parent_term_object->slug . '">' . $parent_term_object->name . '</a> &raquo; ';
        //             }
        //             $term_links[] = $parent_term_link . '<a href="' . get_term_link($term->term_id) . '" data-filter-type="category" data-filter="' . $term->slug . '">' . $term->name . '</a>';
        //         }
        //     }
        // }

        // set up the wrapper
        $html = '<div class="test_panel_individual_test_wrapper">';
        $html .= '<div class="test_panel_description"><p>' . $test_panel_description . '</p></div>';
        
        // set up the header bar
        // $html .= '<div class="clear"></div><div class="igenex_test_heading"><div class="test_number">Test No.</div><div class="title">Test Name</div><div class="categories">Category</div><div class="price_wrapper">Price</div><div class="product_link"></div></div>';
        $html .= '<div class="clear"></div><div class="igenex_test_heading"><div class="test_number">Test No.</div><div class="title">Test Name</div><div class="price_wrapper">Individual Price</div><div class="product_link"></div></div>';

        $html .= '<div class="test_panel_individual_tests shop-products-grid elementor-products-grid"><ul class="products elementor-grid">';
        
        // show each test
        foreach($test_panel_individual_tests as $product_id) {
            $individual_test_product = wc_get_product($product_id);

            $html .= '<li class="product type-product post-' . $product_id . ' status-publish">';
            $html .= '<div class="test_number"><a href="' . get_permalink( $individual_test_product->get_id() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' . $individual_test_product->get_sku() . '</a></div>';
            $html .= '<div class="igx_product_title_wrapper"><a href="' . get_permalink( $individual_test_product->get_id() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><h2 class="woocommerce-loop-product__title">' . $individual_test_product->get_name() . '</h2></a></div>';
            // $html .= '<div class="categories">' . implode(', ', $term_links) . '</div>';
            $html .= '<a href="' . get_permalink( $product->get_id() ) . '" class="price_wrapper"><span class="price">' . wc_price($individual_test_product->get_price()) . '</span></a>';
            $html .= '<div class="product_link"><a href="' . get_permalink( $product->get_id() ) .'"><svg width="100%" height="100%" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" class="svg-inline--fa fa-chevron-right fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="#303031" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg></a></div></li>';

        }
        $html .= '</ul></div>';
        $html .= '</div>';
        return $html . $excerpt;
    }
    /**
     * Get the additional product attributes and display them with the tags
     */
    public function show_additional_info() {
        $product = wc_get_product(get_the_ID());

        $attributes = array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' );
        foreach ( $attributes as $attribute ) {
            $values = array();

            if ( $attribute->is_taxonomy() ) {
                $attribute_taxonomy = $attribute->get_taxonomy_object();
                $attribute_values   = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

                foreach ( $attribute_values as $attribute_value ) {
                    $value_name = esc_html( $attribute_value->name );

                    if ( $attribute_taxonomy->attribute_public ) {
                        $values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
                    } else {
                        $values[] = $value_name;
                    }
                }
            } else {
                $values = $attribute->get_options();

                foreach ( $values as &$value ) {
                    $value = make_clickable( esc_html( $value ) );
                }
            }

            $product_attributes[ 'attribute_' . sanitize_title_with_dashes( $attribute->get_name() ) ] = array(
                'label' => wc_attribute_label( $attribute->get_name() ),
                'value' => apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values ),
            );
        }
        foreach($product_attributes as $key => $product_attribute) {
            echo ' <span class="' . $key . ' detail-container"><span class="detail-label">' . $product_attribute['label'] . '</span> <span class="detail-content">' . $product_attribute['value'] . '</span></span>';
        }
    }
    /**
     * Get the content for the advantages tab
     */
    public function tab_content_advantages()  {
        echo $this->get_specific_tab_content('tab_advantages');
    }
    /**
     * Get the content for the test interpretations tab
     */
    public function tab_content_test_interpretations()  {
        echo $this->get_specific_tab_content('tab_test_interpretations');
    }
    /**
     * Get the content for the limitations tab
     */
    public function tab_content_limitations()  {
        echo $this->get_specific_tab_content('tab_limitations');
    }

    /**
     * Get a specific field from ACF
     */
    public function get_specific_tab_content($field) {
        if(function_exists('get_field')) {
            $product_id = get_the_ID();
            $content = get_field($field, $product_id, true);
    
            return $content;
        }
    }
    /**
     * Replace the home link URL
     */
    function woo_custom_breadrumb_home_url() {
        return site_url('/');
    }
    public function wl ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    } // end public function wl 
} // end class Product

$igenex_woocommerce_product = new Product();
