<?php
class Your_WooCommerce_Integration
{
 

    public function register() 
    {
        add_action ('woocommerce_before_add_to_cart_button', [$this, 'sw_render_frontend'],29);
		add_action ('woocommerce_single_variation', [$this, 'sw_render_frontend'],15);
        // Add actions and filters related to WooCommerce integration.
        add_action('woocommerce_product_data_tabs', array($this, 'add_custom_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_custom_product_data_panel'));
        // Add action to save product data.
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_product_data'));
        // Add custom data to cart item
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_custom_data_to_cart_item'), 10, 2);
        // Display custom meta data in cart
        add_filter('woocommerce_get_item_data',  array($this, 'display_custom_meta_in_cart'), 10, 2);
        add_action( 'woocommerce_before_calculate_totals', array($this,'add_custom_price') , 100);
        add_filter('woocommerce_order_item_get_formatted_meta_data', array($this,'display_custom_meta_in_admin_order'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this,'save_custom_color_meta_to_order_items'), 10, 4);


    }

    public function display_custom_meta_in_cart($item_data, $cart_item) {
        
        if (isset($cart_item['custom_color'])) {
            // Get the custom data
            $custom_data = $cart_item['custom_color'];
            $term = get_term_by('term_id',  $custom_data,'color_group');
            if ($term){
                // Add the custom data to the item data
                $item_data[] = array(
                    'key'   => __('Kleur', 'your-text-domain'),
                    'value' => $term->name,
                );
            }
        }
    
        return $item_data;
    }

    public function add_custom_data_to_cart_item($cart_item_data, $product_id) {
   
        if (isset($_POST['custom_color_field'])) {
            $custom_data = sanitize_text_field($_POST['custom_color_field']);
            // Add custom data to cart item
            $cart_item_data['custom_color'] = $custom_data;
        }
        return $cart_item_data;
    }
    

    public function sw_render_frontend() {
        $template_path = PRODUCT_OPTION_DESIGNER_DIR. 'template/single-product-page.php';
        $color = new Color_group(get_the_ID());
        $colors = $color->get_colors();
        // $json = json_encode($colors);
        // var_dump($colors);
        // wp_die();
        if ($colors){
            if (file_exists($template_path)) {
                include $template_path;
            } else {
                echo 'Template file not found.';
            }
        }

       
    }
    

    // Add custom product data tab.
    public function add_custom_product_data_tab($tabs) 
    {
        // Add your custom tab here.
        $tabs['custom_product_color_group'] = array(
            'label'  => __('Color group', 'your-plugin-name'),
            'target' => 'custom_product_color_group',
            'class'  => array(),
        );
        return $tabs;
    }

    // Add custom product data panel.
    public function add_custom_product_data_panel()
    {
        global $post;
        $selected_color_group = get_post_meta($post->ID, 'color_group', true);
?>
        <div id="custom_product_color_group" class="panel woocommerce_options_panel">
            <div class="options_group">
                <p class="form-field">
                    <label for="color_group"><?php _e('Color Group', 'your-plugin-name'); ?></label>
                    <select name="color_group" id="color_group">
                        <option value=""><?php _e('Select Color Group', 'your-plugin-name'); ?></option>
                        <?php
                        $color_groups = new WP_Query(array(
                            'post_type' => 'color_group_cpt',
                            'posts_per_page' => -1,
                        ));

                        if ($color_groups->have_posts()) {
                            while ($color_groups->have_posts()) {
                                $color_groups->the_post();
                                $group_id = get_the_ID();
                        ?>
                                <option value="<?php echo $group_id; ?>" <?php selected($selected_color_group, $group_id); ?>><?php the_title(); ?></option>
                        <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </select>
                </p>
            </div>
        </div>
<?php
    }

    // Save custom product data.
    public function save_custom_product_data($post_id)
    {
        if (isset($_POST['color_group'])) {
            update_post_meta($post_id, 'color_group', $_POST['color_group']);
        }
    }

    public function add_custom_price( $cart_object ) {
        foreach ( $cart_object->cart_contents as $key => $value ) {
         
            if (isset($value['custom_color'])){
                $attribute_id = get_option('your_plugin_name_attribute');
                $product_attribute = wc_get_attribute($attribute_id);
                $product_slug = ('attribute_'.$product_attribute->slug);
                $term = false;
                if (isset($value['variation']) && isset($value['variation'][$product_slug])){
                    $term_slug = $value['variation'][key($value['variation'])];
                    $term = get_term_by('slug', $term_slug, $product_attribute->slug);
                }
                
                
                if ($term){
                    $pricegroup = get_term_meta($value['custom_color'], 'pricegroup', true);
                    $colorgroup = new Color_group($value['product_id']);
                    $data = $colorgroup->get_price_group($pricegroup);
                    foreach ($data as $item) {
                        if ($item['slug'] === $term_slug) {
                      
                            $value['data']->set_price($item['price'] + $value['data']->get_price());
                            break; // Exit the loop once the price is found
                        }
                    }
                }
                else{
                    $pricegroup = get_term_meta($value['custom_color'], 'pricegroup', true);
                 
                    $colorgroup = new Color_group($value['product_id']);
                    $data = $colorgroup->get_price_group($pricegroup);
                    
                    foreach ($data as $item) {
                        if ($item['slug'] === 'single') {
                            
                            $value['data']->set_price($item['price'] + $value['data']->get_price());
                         
                            break; // Exit the loop once the price is found
                        }
                    }
                }
            }
        }
    }



    public function display_custom_meta_in_admin_order($formatted_meta, $item) {
        foreach ($formatted_meta as $key => $meta) {
            
            if ($meta->key === 'custom_color') {
                $term = get_term_by('term_id', $meta->value, 'color_group');
                if ($term && !is_wp_error($term)) {
                    $formatted_meta[$key]->display_key = __('Kleur', 'your-text-domain');
                    $formatted_meta[$key]->display_value = $term->name;
                }
            }
        }
        return $formatted_meta;
    }
    

    public function save_custom_color_meta_to_order_items($item, $cart_item_key, $values, $order) {
        if (isset($values['custom_color'])) {
            $item->update_meta_data('custom_color', $values['custom_color']);
        }
    }


}
