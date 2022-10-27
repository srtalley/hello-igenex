<?php
namespace IGeneX\Theme\WooCommerce;

class Shop {

    public function __construct() {

        add_filter( 'gettext', array($this,'igx_change_wc_text_strings'), 20, 3 );

        add_action( 'woocommerce_before_shop_loop_item', array($this, 'shop_show_test_number'), 1 );

        add_action( 'woocommerce_before_shop_loop_item', array($this, 'wrap_product_link_start'), 9);

        add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'wrap_product_link_end'), 6 );
        add_action( 'woocommerce_after_shop_loop_item', array($this, 'shop_show_forward_arrow_link'), 10 );

        add_action( 'woocommerce_init', array($this, 'setup_shop_page'), 10, 1);

        add_action( 'woocommerce_before_shop_loop', array($this, 'shop_page_heading'), 60 );

        // add_filter( 'aws_search_results_all', array($this, 'modify_aws_search_results_all'), 10, 2);
        // add_filter( 'aws_title_search_result', array($this, 'modify_aws_title_search_result'), 1, 3);
        add_filter( 'woocommerce_catalog_orderby', array($this, 'shop_remove_default_sorting_options'), 10, 1 );

        add_filter( 'wpseo_sitemap_post_type_archive_link', array($this, 'remove_shop_from_sitemap_archive_link'), 10, 2 );

    } // end function      

    /**
     * Remove certain sorting items
     */
    public function shop_remove_default_sorting_options( $options ){
    
        unset( $options[ 'popularity' ] );
        // unset( $options[ 'menu_order' ] );
        //unset( $options[ 'rating' ] );
        unset( $options[ 'date' ] );
        //unset( $options[ 'price' ] );
        //unset( $options[ 'price-desc' ] );
    
        return $options;
    
    }
    /**
    * Change WooCommerce Single Product Page Strings
    *
    * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
    */
    public function igx_change_wc_text_strings( $translated_text, $text, $domain ) {

        if($domain == 'woocommerce') {
            switch ( $translated_text ) {

                // Switching these headings but they need to be slightly different
                // or it enters a loop where it keeps trying to replace the other
                // one over and over.
                case 'Related products':
                    $translated_text = __( 'Related Tests', 'woocommerce' );
                    break;

            }
        } 
        if($domain == 'advanced-woo-search') {
            switch ( $translated_text ) {

                case 'SKU':
                    $translated_text = __( 'Test No', 'advanced-woo-search');
                    break;
            }
        } 
        


        return $translated_text;
    } // end function gm_change_wc_text_strings

    /**
     * Remove the add to cart buttons
     */
    public function setup_shop_page() {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart');
    }

    /**
     * Show the test number in the shop
     */
    public function shop_show_test_number() {
        $product = wc_get_product(get_the_ID());
        $product->get_sku();

        echo '<div class="test_number"><a href="' . get_permalink( $product->get_id() ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><span class="test_number_description">Test No. </span><span class="test_number_numeral">' . $product->get_sku() . '</span></a></div>';
    }
    /**
     * Wrap the product title in a div 
     */
    public function wrap_product_link_start() {
        echo '<div class="igx_product_title_wrapper">';
    }
  
    /**
     * Add the categories beneath the title in the shop and archive pages
     */
    public function wrap_product_link_end(){
        // close the product link and title wrapper
        echo '</a></div>';
        $product = wc_get_product(get_the_ID());
        if ($product->post_type == 'product_variation') {
            $terms = get_the_terms($product->get_parent_id(), 'product_cat');
        } else {
            $terms = get_the_terms($product->get_id(), 'product_cat');
        }
        if ($terms) {
            foreach ($terms as $term) {
                if($term->slug == 'individual-tests' || $term->slug == 'test-panels') {
                    continue;
                } else {
                    $parent_term_link = '';
                    // see if there's a parent term
                    if($term->parent > 0) {
                        $parent_term_object = get_term($term->parent);
                        $parent_term_link = '<a href="' . get_term_link($parent_term_object->term_id) . '" data-filter-type="category" data-filter="' . $parent_term_object->slug . '">' . $parent_term_object->name . '</a> &raquo; ';
                    }
                    $term_links[] = $parent_term_link . '<a href="' . get_term_link($term->term_id) . '" data-filter-type="category" data-filter="' . $term->slug . '">' . $term->name . '</a>';
                }
            }
        }
        // show the categories
        echo '<div class="categories">' . implode(', ', $term_links) . '</div>';

        // wrap the price in an anchor
        echo '<a href="' . get_permalink( $product->get_id() ) . '" class="price_wrapper">';
    }


    /**
     * shop_show_forward_arrow_link
     */
    public function shop_show_forward_arrow_link() {
        echo '<div class="product_link"><a href="' . get_the_permalink() . '"><svg width="100%" height="100%" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" class="svg-inline--fa fa-chevron-right fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="#303031" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg></a></div>';
    }
    /**
     * Shortcode to add the test heading
     */
    public function shop_page_heading() {
        echo do_shortcode('[aws_search_form]');
        echo '<div class="clear"></div><div class="igenex_test_heading"><div class="test_number">Test No.</div><div class="title">Test Name</div><div class="categories">Category</div><div class="price_wrapper">Price</div><div class="product_link"></div></div>';
    }

    // public function modify_aws_search_results_all($result_array, $s) {
    //     $this->wl($result_array);
    // }
    // public function modify_aws_title_search_result($title, $post_id, $product) {
    //     return $title;
    // }

    /**
     * Remove the shop page from showing up in the product cat sitemap
     */
    public function remove_shop_from_sitemap_archive_link( $link, $post_type ) {
        // Disable product/post archives in the sitemaps
        if ( $post_type === 'product' )
                return false;

        return $link;
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
} // end class Shop

$igenex_woocommerce_shop = new Shop();
