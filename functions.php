<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		wp_get_theme()->get('Version')
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );


// Classes
require_once( dirname( __FILE__ ) . '/includes/class-menu.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-admin.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-product.php');
require_once( dirname( __FILE__ ) . '/includes/class-woocommerce-shop.php');


// Logging function 
function wl ( $log )  {
	if ( true === WP_DEBUG ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
} // end public function wl 