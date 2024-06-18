<?php
class Color_Group_CPT
{
    private $post_type_colors;
    private $post_label_colors;
    private $post_slug_colors;
    // Default color values
    private $default_color_values = [
        'yellow'        => ['code' => 1, 'hex' => '#F7D143', 'label' => 'Yellow', 'value'=> 'yellow'],
        'orange'        => ['code' => 2, 'hex' => '#D9AE4F', 'label' => 'Orange', 'value'=> 'orange'], 
        'light_green'   => ['code' => 3, 'hex' => '#D1E39D', 'label' => 'Light Green', 'value'=> 'light_green'],
        'green'         => ['code' => 4, 'hex' => '#7FA05B', 'label' => 'Green', 'value'=> 'green'],
        'light_blue'    => ['code' => 5, 'hex' => '#61CCD7', 'label' => 'Light Blue', 'value'=> 'light_blue'],
        'blue'          => ['code' => 6, 'hex' => '#5989D6', 'label' => 'Blue', 'value'=> 'blue'],
        'pink'          => ['code' => 7, 'hex' => '#D1A8CE', 'label' => 'Pink', 'value'=> 'pink'],
        'rose'          => ['code' => 8, 'hex' => '#D9A59B', 'label' => 'Rose', 'value'=> 'rose'],
        'red'           => ['code' => 9, 'hex' => '#B26448', 'label' => 'Red', 'value'=> 'red'],
        'grey'          => ['code' => 10, 'hex' => '#7C7C7C', 'label' => 'Grey', 'value'=> 'grey'],  
        'white'         => ['code' => 11, 'hex' => '#D6D6D6', 'label' => 'White', 'value'=> 'white'],
    ];

    public function __construct()
    {
        $this->post_type_colors = 'color_group_cpt';
        $this->post_label_colors = 'color_group_cpt';
        $this->post_slug_colors = 'color-group-cpt';
    }


    

    // Method to get color values from options
    public function get_color_values() {
        $color_values = get_option('color_values');
        if (empty($color_values)) {
            $color_values = $this->default_color_values;
        }
        return $color_values;
    }

    public function register()
    {
        $labels = array(
            'name'               => __('Color group Type', 'your-plugin-name'),
            'singular_name'      => __('Color group Type', 'your-plugin-name'),
            'menu_name'          => __('Color group Type', 'your-plugin-name'),
            'name_admin_bar'     => __('Color group Type', 'your-plugin-name'),
            'add_new'            => __('Add New', 'your-plugin-name'),
            'add_new_item'       => __('Add New Color group', 'your-plugin-name'),
            'new_item'           => __('New Color group ', 'your-plugin-name'),
            'edit_item'          => __('Edit Color group ', 'your-plugin-name'),
            'view_item'          => __('View Color group ', 'your-plugin-name'),
            'all_items'          => __('All Color group ', 'your-plugin-name'),
            'search_items'       => __('Search Color group ', 'your-plugin-name'),
            'parent_item_colon'  => __('Parent Color group :', 'your-plugin-name'),
            'not_found'          => __('No Color group found.', 'your-plugin-name'),
            'not_found_in_trash' => __('No Color group found in Trash.', 'your-plugin-name')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' =>  $this->post_slug_colors),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title')
        );

        register_post_type($this->post_type_colors, $args);

        $labels = array(
            'name'               => __('Price group CPT', 'your-plugin-name'),
            'singular_name'      => __('Price group CPT', 'your-plugin-name'),
            'menu_name'          => __('Price group CPT', 'your-plugin-name'),
            'name_admin_bar'     => __('Price group CPT', 'your-plugin-name'),
            'add_new'            => __('Add New', 'your-plugin-name'),
            'add_new_item'       => __('Add New Price group CPT', 'your-plugin-name'),
            'new_item'           => __('New Price group CPT', 'your-plugin-name'),
            'edit_item'          => __('Edit Price group CPT', 'your-plugin-name'),
            'view_item'          => __('View Price group CPT', 'your-plugin-name'),
            'all_items'          => __('All Price group CPT', 'your-plugin-name'),
            'search_items'       => __('Search Price group CPT', 'your-plugin-name'),
            'parent_item_colon'  => __('Parent Price group CPT:', 'your-plugin-name'),
            'not_found'          => __('No Price group CPT found.', 'your-plugin-name'),
            'not_found_in_trash' => __('No Price group CPT found in Trash.', 'your-plugin-name')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' =>  $this->post_slug_colors),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title')
        );

        register_post_type('price_group_cpt', $args);

        // Add taxonomy support
        $this->register_taxonomy();
        // Add custom field to the add term form
        add_action('color_group_add_form_fields', array($this, 'add_colorpicker_field'));
        // Add custom field to the edit term form
        add_action('color_group_edit_form_fields', array($this, 'edit_colorpicker_field'));

        // Save custom field data when a term is edited
        add_action('edited_color_group', array($this, 'save_colorpicker_field'));
        // Save custom field data when a term is created
        add_action('create_color_group', array($this, 'save_colorpicker_field'));

        

        // Add meta box to display saved product attribute
        add_action('add_meta_boxes', array($this, 'add_product_attribute_meta_box'));

        // Hook into save_post action
        add_action('save_post', array($this, 'save_product_attribute_meta'));
        add_action('save_post', array($this, 'save_color_group_meta_field'));
        
    }

    public function register_taxonomy()
    {
        $labels = array(
            'name'                       => __('Colors', 'your-plugin-name'),
            'singular_name'              => __('Color', 'your-plugin-name'),
            'menu_name'                  => __('Colors', 'your-plugin-name'),
            'all_items'                  => __('All Colors', 'your-plugin-name'),
            'edit_item'                  => __('Edit Colors', 'your-plugin-name'),
            'view_item'                  => __('View Colors', 'your-plugin-name'),
            'update_item'                => __('Update Colors', 'your-plugin-name'),
            'add_new_item'               => __('Add New Colors', 'your-plugin-name'),
            'new_item_name'              => __('New Colors Name', 'your-plugin-name'),
            'parent_item'                => __('Parent Colors', 'your-plugin-name'),
            'parent_item_colon'          => __('Parent Color:', 'your-plugin-name'),
            'search_items'               => __('Search Color', 'your-plugin-name'),
            'popular_items'              => __('Popular Color', 'your-plugin-name'),
            'separate_items_with_commas' => __('Separate color with commas', 'your-plugin-name'),
            'add_or_remove_items'        => __('Add or remove color', 'your-plugin-name'),
            'choose_from_most_used'      => __('Choose from the most used color', 'your-plugin-name'),
            'not_found'                  => __('No color found.', 'your-plugin-name')
        );

        $args = array(
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'color-group'),
        );

        register_taxonomy('color_group', $this->post_type_colors, $args); 
    }
    public function add_colorpicker_field()
    {
?>
        <div class="form-field term-colorpicker-wrap">
            <label for="term-colorpicker"><?php _e('Color Picker', 'your-plugin-name'); ?></label>
            <input type="text" name="term-colorpicker" id="term-colorpicker" class="colorpicker" value="">
        </div>
    <?php
    }

    public function edit_colorpicker_field($term)
{
    $color = get_term_meta($term->term_id, 'colorpicker', true);
    ?>
    <tr class="form-field term-colorpicker-wrap">
        <th scope="row"><label for="term-colorpicker"><?php _e('Color', 'your-plugin-name'); ?></label></th>
        <td>
            <input type="text" name="term-colorpicker" id="term-colorpicker" class="colorpicker" value="<?php echo esc_attr($color); ?>">
        </td>
    </tr>
    
    <?php
    // Set up your query arguments
    $args = array(
        'post_type'      => 'price_group_cpt', // Replace 'price_group_cpt' with your actual post type slug
        'posts_per_page' => -1, // Retrieve all posts
        'meta_query'     => array(
            array(
                'key'     => 'price_group_meta_field', // Replace 'price_group_meta_field' with your actual meta key
                'compare' => 'EXISTS', // Compare the existence of the meta key
            ),
        ),
    );

    // Create a new WP_Query instance
    $query = new WP_Query($args);

    // Initialize an empty array to store meta values
    $meta_values = array();

    // Loop through the query results
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Get the meta value for each post
            $meta_value = get_post_meta(get_the_ID(), 'price_group_meta_field', true); // Replace 'price_group_meta_field' with your actual meta key
            // Add the meta value to the array if it's not already present
            if (!in_array($meta_value, $meta_values)) {
                $meta_values[] = $meta_value;
            }
        }
        // Reset post data
        wp_reset_postdata();
    }

    // Sort the meta values alphabetically
    sort($meta_values);

    // Output the dropdown options
    ?>
    <tr class="form-field term-price-group-wrap">
        <th scope="row"><label for="term-price-group"><?php _e('Price Group', 'your-plugin-name'); ?></label></th>
        <td>
            <select name="term-price-group" id="term-price-group">
                <?php foreach ($meta_values as $meta_value) : ?>
                    <option value="<?php echo esc_attr($meta_value); ?>" <?php selected(get_term_meta($term->term_id, 'pricegroup', true), $meta_value); ?>><?php echo esc_html($meta_value); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
    $theme = get_term_meta($term->term_id, 'theme', true); // Retrieve the saved theme value
    ?>
    <tr class="form-field term-theme-wrap">
        <th scope="row"><label for="term-theme"><?php _e('Theme', 'your-plugin-name'); ?></label></th>
        <td>
            <select name="term-theme" id="term-theme">
                <option value="light" <?php selected($theme, 'light'); ?>><?php _e('Light', 'your-plugin-name'); ?></option>
                <option value="dark" <?php selected($theme, 'dark'); ?>><?php _e('Dark', 'your-plugin-name'); ?></option>
            </select>
        </td>
    </tr>
    <?php

    // Assuming Color_Group_CPT is included and available
    $colors = $this->get_color_values();
    
    // Retrieve current color value stored as term meta
    $current_color = get_term_meta($term->term_id, 'term_color_filter', true);

    echo '<tr class="form-field">
        <th scope="row" valign="top"><label for="term_color_filter">Select Color</label></th>
        <td>
            <select name="term_color_filter" id="term_color_filter" class="postform">
                <option value="">Select a Color</option>';

    foreach ($colors as $slug => $color_info) {
        $selected = ($current_color === $color_info['value']) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($color_info['value']) . '" ' . $selected . '>' . esc_html($color_info['label']) . '</option>';
    }

    echo '</select>
            <p class="description">Choose a color for this term.</p>
        </td>
    </tr>';
}


    public function save_colorpicker_field($term_id)
    {
        if (isset($_POST['term-colorpicker'])) {
            $color = sanitize_hex_color($_POST['term-colorpicker']);
            update_term_meta($term_id, 'colorpicker', $color);
        }
        // Save the price group value
        if (isset($_POST['term-price-group'])) {
            $price_group_value = sanitize_text_field($_POST['term-price-group']);
            update_term_meta($term_id, 'pricegroup', $price_group_value);
        }
        if (isset($_POST['term-theme']) && in_array($_POST['term-theme'], ['light', 'dark'])) {
            update_term_meta($term_id, 'theme', $_POST['term-theme']);
        }

        if (isset($_POST['term_color_filter']) && '' !== $_POST['term_color_filter']) {
            update_term_meta($term_id, 'term_color_filter', ($_POST['term_color_filter']));
        }
        global $wpdb;

        // Prefix for the transient keys you want to delete
        $transient_prefix = 'price_group_data_';

        // SQL to delete transient data
        $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_$transient_prefix%' OR option_name LIKE '_transient_timeout_$transient_prefix%'";
        $wpdb->query($sql);
    }

  


    // Render content for the color group meta box.
    public function render_color_group_meta_box($post) {
        // Retrieve existing value of the meta field, if any
        $existing_value = get_post_meta($post->ID, 'color_group_meta_field', true);
        ?>
        <p>
            <label for="color_group_meta_field"><?php _e('Color Group Meta ID', 'your-plugin-name'); ?></label><br>
            <input type="text" id="color_group_meta_field" name="color_group_meta_field" value="<?php echo esc_attr($existing_value); ?>" style="width: 100%;">
        </p>
        <?php

        
    }

    // Save meta field value when color_group_cpt post is saved.
    public function save_color_group_meta_field($post_id) {
     
        if (isset($_POST['color_group_meta_field'])) {
            update_post_meta($post_id, 'color_group_meta_field', sanitize_text_field($_POST['color_group_meta_field']));
        }
        if (isset($_POST['price_group_meta_field'])) {
            update_post_meta($post_id, 'price_group_meta_field', sanitize_text_field($_POST['price_group_meta_field']));
        }
    }


   

    public function add_product_attribute_meta_box()
    {
        add_meta_box(
            'product_attribute_meta_box',
            __('Product Attribute', 'your-plugin-name'),
            array($this, 'product_attribute_meta_box_callback'),
            'price_group_cpt',
            'normal'
        );
        add_meta_box(
            'color_group_meta_box',
            __('Color Group Meta', 'your-plugin-name'),
            array($this, 'render_color_group_meta_box'),
            'color_group_cpt',
            'side',
            'high'
        );
        add_meta_box(
            'price_group_meta_box',
            __('Price Group Meta', 'your-plugin-name'),
            array($this, 'render_price_group_meta_box'),
            'price_group_cpt',
            'side',
            'high'
        );
    }

   
    

     // Render content for the color group meta box.
     public function render_price_group_meta_box($post) {
        // Retrieve existing value of the meta field, if any
        $existing_value = get_post_meta($post->ID, 'price_group_meta_field', true);
        ?>
        <p>
            <label for="price_group_meta_field"><?php _e('Price Group Meta ID', 'your-plugin-name'); ?></label><br>
            <input type="text" id="price_group_meta_field" name="price_group_meta_field" value="<?php echo esc_attr($existing_value); ?>" style="width: 100%;">
        </p>
        <?php
    }

    // // Save meta field value when color_group_cpt post is saved.
    // public function save_color_group_meta_field($post_id) {
     
    //     if (isset($_POST['color_group_meta_field'])) {
    //         update_post_meta($post_id, 'color_group_meta_field', sanitize_text_field($_POST['color_group_meta_field']));
    //     }
    // }


    public function product_attribute_meta_box_callback($post)
    {
        $attribute_id = get_option('your_plugin_name_attribute');
        $product_attribute = wc_get_attribute($attribute_id);

        if ($attribute_id) {
            $terms = get_terms(array(
                'taxonomy' => $product_attribute->slug,
                'hide_empty' => false,
            ));
            if ($terms && !is_wp_error($terms)) {
                wp_nonce_field('product_attribute_meta_box', 'product_attribute_meta_box_nonce');
                $meta_key = 'color_term_price_simple';
                $meta_value = get_post_meta($post->ID, $meta_key, true);
                echo '<div class="term-field" style="margin-bottom:10px;">';
                echo '<label style="min-width:120px;display:inline-block" for="' . esc_attr($meta_key) . '">' . esc_html('Simple product: ') . '</label>';
                echo '<input type="text" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" class="term-price" value="' . esc_attr($meta_value) . '">';
                echo '</div>';
                $meta_value = false;
                foreach ($terms as $term) {
                    $meta_key = 'color_term_price_' . esc_attr($term->term_id);
                    $meta_value = get_post_meta($post->ID, $meta_key, true);

                    echo '<div class="term-field" style="margin-bottom:10px;">';
                    echo '<label style="min-width:120px;display:inline-block" for="' . esc_attr($meta_key) . '">' . esc_html($term->name) . '</label>';
                    echo '<input type="text" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" class="term-price" value="' . esc_attr($meta_value) . '">';
                    echo '</div>';
                }
            } else {
                echo '<p>' . __('No terms found for this attribute.', 'your-plugin-name') . '</p>';
            }
        } else {
            echo '<p>' . __('No WooCommerce attribute selected.', 'your-plugin-name') . '</p>';
        }
    }

    public function save_product_attribute_meta($post_id)
    {
     
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if the nonce was set
        if (!isset($_POST['product_attribute_meta_box_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['product_attribute_meta_box_nonce'], 'product_attribute_meta_box')) {
            return;
        }

        // Check if data is submitted and save it
        $attribute_id = get_option('your_plugin_name_attribute');
        $product_attribute = wc_get_attribute($attribute_id);

        if ($attribute_id && $product_attribute) {
            $terms = get_terms(array(
                'taxonomy' => $product_attribute->slug,
                'hide_empty' => false,
            ));

            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {

                    $meta_key = 'color_term_price_' . esc_attr($term->term_id);
                    if (isset($_POST[$meta_key])) {
                        $meta_value = sanitize_text_field($_POST[$meta_key]);
                        update_post_meta($post_id, $meta_key, $meta_value);
                    }
                }
                if (isset($_POST['color_term_price_simple'])) {
                    $meta_value = sanitize_text_field($_POST['color_term_price_simple']);
                    update_post_meta($post_id, 'color_term_price_simple', $meta_value);
                }
            }
        }
       
    }

    public function getColorByCode($code) {
        $i = 1;
        foreach ($this->get_color_values() as $color => $details) {
            if (isset($i) && $i === $code) {
                return $details;
            }
            $i++;
        }
    
        return null; // Return null if no color matches the code
    }
}
