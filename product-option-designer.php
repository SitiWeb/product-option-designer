<?php
/*
Plugin Name: Custom color selector
Plugin URI: https://sitiweb.nl
Description: This plugin adds custom color selector for WooCommerce products
Version: 1.7
Author: Roberto van SitiWeb
Author URI: https://sitiweb.nl
License: GPL v2 or later
Text Domain: custom-color-selector
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
// Define plugin constants.
define('SW-PRODUCT-OPTION-DESIGNER', '1.0');
define('PRODUCT_OPTION_DESIGNER_DIR', plugin_dir_path(__FILE__));
define('PRODUCT_OPTION_DESIGNER_URL', plugin_dir_url(__FILE__));
if( ! class_exists( 'SitiWeb_Updater' ) ){
	include_once( PRODUCT_OPTION_DESIGNER_DIR . 'updater.php' );
}

$updater = new SitiWeb_Updater( __FILE__ );
$updater->set_username( 'SitiWeb' );
$updater->set_repository( 'product-option-designer' );
$updater->initialize();



// Include the main plugin class.
require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-product-option-designer.php';
require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-cpt.php';
require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-cpt-settings.php';
require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-import.php'; 
require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-group.php'; 

// Initialize the plugin.
function your_plugin_name_init() {
    $product_options = new Product_option_designer();
    $product_options->init();
}
add_action('plugins_loaded', 'your_plugin_name_init');

function enqueue_color_frontend_script() {
    // Define the data to pass to the script
    if (is_product()){

        
        $color = new Color_group(get_the_ID());
      // Define additional data
       
        $data = $color->get_colors();
        $product = wc_get_product(get_the_ID());
        
        $additional_data = array();
        if ($product){
            $additional_data = array(
                'productType' => $product->get_type(),        // Get the product type
                'regularPrice' => $product->get_regular_price()  // Get the regular price
            );
        }
        

        // Enqueue Popup.js CSS
        wp_enqueue_style('popup-css', PRODUCT_OPTION_DESIGNER_URL . 'assets/css/popup.css', array(), '1.0', 'all');
        
        wp_enqueue_script('popup-js', PRODUCT_OPTION_DESIGNER_URL . 'assets/js/popup.js', array('jquery'), '1.0', true);
        wp_enqueue_script('color-plugin-frontend', PRODUCT_OPTION_DESIGNER_URL . 'assets/js/color-frontend.js', array('jquery'), '1.0', true);
        // Localize the script with the data
        wp_localize_script('color-plugin-frontend', 'colorData', $data);
        wp_localize_script('color-plugin-frontend', 'productData', $additional_data);
    
        
    }
    
}
add_action('wp_enqueue_scripts', 'enqueue_color_frontend_script');



add_action( 'woocommerce_variation_price_html' , 'custom_from' );
add_filter('woocommerce_variable_price_html','custom_from',10);
add_filter('woocommerce_grouped_price_html','custom_from',10);
add_filter('woocommerce_variable_sale_price_html','custom_from',10);
function custom_from($price){
    if (is_product()){
        $color = new Color_group(get_the_ID());
        if ($color->get_color_group()){
            return false;
        }
    }
    return $price;
}
