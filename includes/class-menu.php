<?php 
/**
 * v1.0
 */
namespace IGeneX\Theme;

class Menu {

    public function __construct() {
        add_filter( 'wp_nav_menu_objects', array($this, 'filter_primary_menu'), 10, 2 );
        // add_filter( 'wp_nav_menu_igenex-main-site-mirrored-menu_items', array($this, 'igx_woo_cart_button_icon'), 10, 2 );
        add_shortcode ('igx_woo_cart_button', array($this, 'igx_woo_cart_button_shortcode') );
 
    }
    /**
     * Get the Patient menu from the primary site.
     */
    public function filter_primary_menu($items, $args) {
        if($args->menu == 'igenex-main-site-mirrored-menu') {

            // we have to temporarily register the 
            // custom post type from the main site
            // or it won't show in the menu
            register_post_type( 'disease',
                array(
                'labels' => array(
                    'name' => __( 'Diseases' ),
                    'singular_name' => __( 'Disease' )
                ),
                'public' => true,
                        'template' => 'disease',
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => 'disease',
                    'with_front' => false
                )
                )
            );
            global $wpdb;
            
            $wpdb_backup = $wpdb;
           
            if(site_url() == "https://igenex.site" || site_url() == "https://igenex.com/test-directory") {
                $wpdb = new \wpdb('igenex_user', 'AHkXWwTBJaofs6mWALRDtirN', 'igenex_main20190605', 'localhost');
            } else if (site_url() == "https://stage1.igenex.com/test-directory") {
                $wpdb = new \wpdb('igenex_stage1', 'e,q2[R6t2}*l,eK[eq3Fgxko', 'igenex_stage1', 'localhost');
            } else {
                $wpdb = new \wpdb('root', 'cavyn2g-wp-36', 'wp-igenexstd', 'db');
            }            

            if(is_wp_error($wpdb->error)) {
                // get out of this without showing an error on the frontend
                $wpdb = $wpdb_backup;
                wp_cache_flush();
                wp_reset_query();
                return $items;
            } else {
               
                $wpdb->set_prefix('wp_');

                wp_cache_flush();

                $menu = wp_get_nav_menu_items(41);
            
                $wpdb = $wpdb_backup;

                wp_cache_flush();

                wp_reset_query();
                
                unregister_post_type( 'disease' );

                return $menu;
            }
        }
        return $items;
    }
    /**
     * Shortcode to show the cart button
     */
    public function igx_woo_cart_button_shortcode() {
        ob_start();
        ?>
        <a class="cart-link" style="position: relative;" href="https://igenex.com/cart/">

        <svg xmlns="http://www.w3.org/2000/svg" class="cart-icon" width="38" height="60" viewBox="0 0 29 23">
        <title>Shopping Cart</title>
          <path class="cart-fill" fill="" fill-rule="evenodd" d="M1089.92402,54.1656758 L1085.60437,54.1656758 L1086.49977,50.9537127 L1091.0253,50.9537127 L1089.92402,54.1656758 Z M1088.24883,59.0491992 L1084.24179,59.0491992 L1085.13787,55.8372361 L1089.35012,55.8372361 L1088.24883,59.0491992 Z M1077.95077,54.1656758 L1076.85496,50.9537127 L1084.79654,50.9537127 L1083.89977,54.1656758 L1077.95077,54.1656758 Z M1082.53787,59.0491992 L1079.61844,59.0491992 L1078.52125,55.8372361 L1083.43395,55.8372361 L1082.53787,59.0491992 Z M1074.41571,59.0491992 L1073.14205,55.8372361 L1076.78724,55.8372361 L1077.88305,59.0491992 L1074.41571,59.0491992 Z M1071.23019,51.0151056 L1071.20625,50.9537127 L1075.11958,50.9537127 L1076.21607,54.1656758 L1072.47922,54.1656758 L1071.23019,51.0151056 Z M1076.51089,64.1775965 C1077.07522,64.1775965 1077.53488,64.6457171 1077.53488,65.2212752 C1077.53488,65.7968333 1077.07522,66.2656515 1076.51089,66.2656515 C1075.94588,66.2656515 1075.48758,65.7968333 1075.48758,65.2212752 C1075.48758,64.6457171 1075.94588,64.1775965 1076.51089,64.1775965 L1076.51089,64.1775965 Z M1086.56543,64.1775965 C1087.12976,64.1775965 1087.58874,64.6457171 1087.58874,65.2212752 C1087.58874,65.7968333 1087.12976,66.2656515 1086.56543,66.2656515 C1086.00111,66.2656515 1085.54144,65.7968333 1085.54144,65.2212752 C1085.54144,64.6457171 1086.00111,64.1775965 1086.56543,64.1775965 L1086.56543,64.1775965 Z M1092.84824,49.6344637 C1092.69502,49.4140075 1092.44672,49.28285 1092.18063,49.28285 L1070.54684,49.28285 L1069.06387,45.5232347 C1068.93869,45.2072009 1068.6384,45 1068.30391,45 L1064.81947,45 C1064.36732,45 1064,45.3739384 1064,45.8357802 C1064,46.2969243 1064.36732,46.6708627 1064.81947,46.6708627 L1067.74985,46.6708627 L1069.22735,50.4165251 C1069.2294,50.4214086 1069.2294,50.4269898 1069.23145,50.4318733 L1069.62203,51.4169498 L1071.16384,55.3258614 C1071.16384,55.3265591 1071.16452,55.3265591 1071.16452,55.3272567 L1074.38835,63.4987867 C1074.01761,63.9738838 1073.7864,64.566883 1073.7864,65.2212752 C1073.7864,66.7560968 1075.00603,68 1076.51089,68 C1078.01576,68 1079.23538,66.7560968 1079.23538,65.2212752 C1079.23538,64.8215239 1079.14919,64.4433997 1079.00007,64.1001577 L1084.07557,64.1001577 C1083.92645,64.4433997 1083.84026,64.8215239 1083.84026,65.2212752 C1083.84026,66.7560968 1085.06057,68 1086.56543,68 C1088.06961,68 1089.28924,66.7560968 1089.28924,65.2212752 C1089.28924,63.6864535 1088.06961,62.4425504 1086.56543,62.4425504 C1086.53192,62.4425504 1086.50114,62.4516198 1086.4683,62.4523174 C1086.43,62.4467362 1086.39511,62.4292951 1086.35407,62.4292951 L1075.73315,62.4292951 L1075.05938,60.7200619 L1079.03427,60.7200619 C1079.03496,60.7200619 1079.03564,60.7207595 1079.03633,60.7207595 C1079.03701,60.7207595 1079.03769,60.7200619 1079.03838,60.7200619 L1083.15487,60.7200619 C1083.15555,60.7200619 1083.15623,60.7207595 1083.15692,60.7207595 C1083.1576,60.7207595 1083.15829,60.7200619 1083.15897,60.7200619 L1088.82957,60.7200619 C1089.17774,60.7200619 1089.48829,60.4954198 1089.60321,60.1605496 L1092.95427,50.3942004 C1093.04182,50.1388619 1093.00215,49.8556176 1092.84824,49.6344637 L1092.84824,49.6344637 Z" transform="translate(-1064 -45)"/>
            </svg>
        </a>

        <?php

        return ob_get_clean();
    }
    /**
     * Add WooCommerce Cart Menu Item Shortcode to particular menu
     */
    // public function igx_woo_cart_button_icon ( $items, $args ) {
    //     $items .= '<li class="igx-woo-cart-items-wrapper">';
    //     $items .= do_shortcode('[igx_woo_cart_button]');
    //     $items .= '</ul>'; // Adding the created Icon via the shortcode already created
    //     return $items;
    // }
    public function wl ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    } // end public function wl 

} // end class

$igenex_theme_menu = new Menu();