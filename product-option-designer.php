<?php
/*
Plugin Name: Custom color selector
Plugin URI: https://sitiweb.nl
Description: This plugin adds custom color selector for WooCommerce products
Version: 2.0.2
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
    if (is_product()) {
        $product_id = get_the_ID();
        $color = new Color_group($product_id);
        $data = $color->get_colors();
        $product = wc_get_product($product_id);

        $additional_data = array();
        if ($product) {
            $additional_data = array(
                'productType' => $product->get_type(),        // Get the product type
                'regularPrice' => $product->get_regular_price()  // Get the regular price
            );
        }

        // Retrieve custom fields data for each variation
        $custom_fields_data = array();
        if ($product->is_type('variable')) {
            $available_variations = $product->get_available_variations();
            foreach ($available_variations as $variation) {
                $variation_id = $variation['variation_id'];
             
                $custom_fields = array();
                if ($data) {
                    $terms = get_terms(array(
                        'taxonomy' => 'pa_pricegroup', // Change this to your additional attribute slug
                        'hide_empty' => false,
                    ));
        
                    foreach ($terms as $term) {
                        
                        $field_value = get_post_meta($variation_id, 'custom_price_' . $term->slug, true);
                        if ($field_value) {
                            $custom_fields[str_replace("pricegroup-","", $term->slug)] = $field_value;
                        }
                    }
                }
                $custom_fields_data[$variation_id] = $custom_fields;
            }
        }
        // Enqueue Popup.js CSS
        wp_enqueue_style('popup-css', PRODUCT_OPTION_DESIGNER_URL . 'assets/css/popup.css', array(), '1.0', 'all');
        
        wp_enqueue_script('color-plugin-frontend', PRODUCT_OPTION_DESIGNER_URL . 'assets/js/color-frontend.js', array('jquery'), '1.0', true);
        
        // Localize the script with the data
        wp_localize_script('color-plugin-frontend', 'colorData', $data);
        wp_localize_script('color-plugin-frontend', 'productData', $additional_data);
        wp_localize_script('color-plugin-frontend', 'customFieldsData', $custom_fields_data);
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




add_action('woocommerce_product_after_variable_attributes', 'add_custom_fields_to_variations', 10, 3);
function add_custom_fields_to_variations($loop, $variation_data, $variation) {
    // Define the attribute to check
    $attribute_id = get_option('your_plugin_name_attribute');
    $product_attribute = wc_get_attribute($attribute_id);
    $product_slug = ('attribute_'.$product_attribute->slug);

    // Get the terms for the additional attribute
    $terms = get_terms(array(
        'taxonomy' => 'pa_pricegroup', // Change this to your additional attribute slug
        'hide_empty' => false,
    ));

    // Check if the specific attribute is set
    $selected_attribute = get_post_meta($variation->ID, $product_slug, true);
    if ($selected_attribute) {
        echo '<div class="options_group">';
        foreach ($terms as $term) {
            woocommerce_wp_text_input(array(
                'id' => 'custom_price_' . $term->slug . '[' . $loop . ']',
                'label' => __('Price: ' . $term->name, 'woocommerce'),
                'desc_tip' => 'true',
                'description' => __('Enter the value for ' . $term->name, 'woocommerce'),
                'value' => get_post_meta($variation->ID, 'custom_price_' . $term->slug, true)
            ));
        }
        echo '</div>';
    }
    
}



add_action('woocommerce_save_product_variation', 'save_custom_fields_variations', 10, 2);
function save_custom_fields_variations($variation_id, $i) {
    $terms = get_terms('pa_pricegroup'); // Change this to your additional attribute slug

    foreach ($terms as $term) {
        if (isset($_POST['custom_price_' . $term->slug][$i])) {
            update_post_meta($variation_id, 'custom_price_' . $term->slug, sanitize_text_field($_POST['custom_price_' . $term->slug][$i]));
        }
    }
}
