<?php
class Product_option_designer {
    public function __construct() {
        // Add actions and filters.
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('init', array($this, 'add_custom_product_data_tab'));
        add_action('init', array($this, 'register_custom_color_options'));
        // add_action('woocommerce_product_data_tabs', array($this, 'add_custom_product_data_tab'));
        // add_action('woocommerce_product_data_panels', array($this, 'add_custom_product_data_panel'));
    }

    // Initialize the plugin.
    public function init() {
        // Load text domain for translation.
        load_plugin_textdomain('your-plugin-name', false, dirname(plugin_basename(__FILE__)) . '/languages');
    } 

    // Register custom post type.
    public function register_custom_post_type() {
        require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-cpt.php';
        $custom_post_type = new Color_Group_CPT();
        $custom_post_type->register();
    }

    // Add custom product data tab.
    public function add_custom_product_data_tab() {
        require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-product.php';
        $woocommerce_integration = new Your_WooCommerce_Integration();
        $woocommerce_integration->register();
    }

    // Add custom product data panel.
    public function add_custom_product_data_panel() {
        require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-product.php';
        $woocommerce_integration = new Your_WooCommerce_Integration();
        $woocommerce_integration->add_product_data_panel();
    }

    // Add custom product data tab.
    public function register_custom_color_options() {
        require_once PRODUCT_OPTION_DESIGNER_DIR . 'includes/class-color-cpt-settings.php';
        $woocommerce_integration = new Product_option_designer_settings();
        $woocommerce_integration->register();
    }
}
