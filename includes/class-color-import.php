<?php

class Color_Group_Import
{
    private $file_path;
    private $taxonamy_array;
    private $price_group_array;

    public function __construct($file_path)
    {
        $this->taxonamy_array = [];
        $this->price_group_array = [];
        $this->file_path = $file_path;
    }

    public function import_data()
    {
        
        if (($handle = fopen($this->file_path, 'r')) !== false) {
            // Loop through each line of the CSV file
          
            $i = 0;
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                $i++;
                if ($i === 1){
                    continue;
                }
               
                // Process each row of data
                $this->process_row($data);
                
            }

            fclose($handle);
        }

        $this->create_color_groups();
        $this->create_price_group();
       
        global $wpdb;

        // Prefix for the transient keys you want to delete
        $transient_prefix = 'price_group_data_';

        // SQL to delete transient data
        $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_$transient_prefix%' OR option_name LIKE '_transient_timeout_$transient_prefix%'";
        $wpdb->query($sql);
    }

    public function process_row($data)
    {
        // Extract data from the CSV row
        $number_id = isset($data[0]) ? intval($data[0]) : 0;
        $term_name = isset($data[1]) ? sanitize_text_field($data[1]) : '';
        $term_price_group = isset($data[2]) ? intval($data[2]) : 0; 
        $kleurfilter = isset($data[3]) ? intval($data[3]) : false;
        $term_color = isset($data[4]) ? sanitize_hex_color($data[4]) : '';
        $term_color_group = isset($data[5]) ? intval($data[5]) : 0;
        $colors = new Color_Group_CPT();
        if ($kleurfilter){
            $code_group = $colors->getColorByCode($kleurfilter);
        }
        else{
            $code_group = false;
        }
        
        $term_order = 0;

        if (!$term_color || !$term_name || !$number_id){
            return;
        }

        
        
        

        // Check if the term meta exists
        $existing_term_id = $this->get_term_id_by_meta('color_group_number', $number_id);
        
        // Prepare term data
        $term_data = array(
            'description' => '',
            'slug' => sanitize_title($term_name),
        );

        // Check if the term already exists
        if ($existing_term_id) {
            $term_id = $existing_term_id;
            // Update existing term
            $term_id = wp_update_term($existing_term_id, 'color_group', array(
                'name' => $term_name,
                'slug' => $term_data['slug'],
                'term_order' => $term_order
            ));
            if (is_wp_error($term_id)) {
                // Handle error
                return;
            }
            $term_id = $term_id['term_id'];
            update_term_meta($term_id, 'custom_order', $term_order);
            update_term_meta($term_id, 'colorpicker', $term_color);
            update_term_meta($term_id, 'pricegroup', $term_price_group);
            if ($code_group){
                $result = update_term_meta($term_id, 'term_color_filter', $code_group['label']);
            }
            
            // var_dump( $existing_term_id );
            // var_dump( $code_group );
            // var_dump( $code_group['label'] );
            // var_dump( $result );
            // var_dump( $term_id );
            // wp_die();
            
        } else {
            // Insert new term
            $term_id = wp_insert_term($term_name, 'color_group', $term_data);

            // Check if the result is an error
            if (is_wp_error($term_id)) {
                // Check for 'duplicate_term_slug' error
                if ($term_id->get_error_code() === 'term_exists') {
                    // Modify the slug to make it unique and attempt to insert again
                    $unique_suffix = wp_generate_uuid4(); // Generating a unique ID
                    $term_data['slug'] = isset($term_data['slug']) ? $term_data['slug'] . '-' . $unique_suffix : sanitize_title($term_name) . '-' . $unique_suffix;

                    // Attempt to insert the term again with the updated slug
                    $term_id = wp_insert_term($term_name, 'color_group', $term_data);
                    
                    // Check again if there is an error
                    if (is_wp_error($term_id)) {
                        // Handle persistent error
                        return;
                    }
                } else {
                    // Handle other types of errors
                    return;
                }
            }
            $term_id = $term_id['term_id'];
            update_term_meta($term_id, 'custom_order', $term_order);
            update_term_meta($term_id, 'colorpicker', $term_color);
            update_term_meta($term_id, 'pricegroup', $term_price_group);
            if ($code_group){
                $result = update_term_meta($term_id, 'term_color_filter', $code_group['label']);
            }
            
            
        }

        // Check if the term meta exists
        if ($existing_term_id) {
            // Update existing term meta
            update_term_meta($term_id, 'color_group_number', $number_id);
        } else {
            // Insert new term meta
            add_term_meta($term_id, 'color_group_number', $number_id, true);
        }

        $this->taxonamy_array[$term_color_group][] = $term_id;
        
        $this->price_group_array[$term_price_group][] = $term_id;
        


    }

    public function get_term_id_by_meta($meta_key, $meta_value)
    {
        global $wpdb;
        $term_id = $wpdb->get_var($wpdb->prepare(
            "SELECT term_id FROM $wpdb->termmeta WHERE meta_key = %s AND meta_value = %s",
            $meta_key,
            $meta_value
        ));
        return $term_id;
    }

    public function create_color_groups(){
        
        foreach($this->taxonamy_array as $index => $post){
            
            $args = array(
                'post_type' => 'color_group_cpt',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'color_group_meta_field',
                        'value' => $index,
                    ),
                ),
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
               
                while ($query->have_posts()) {
                    $query->the_post();
                    // Get the ID of the post
                    $post_id = get_the_ID();
                    // Add terms to the post's taxonomy
                    $result = wp_set_post_terms($post_id, $post, 'color_group');
                }
                wp_reset_postdata(); // Restore global post data
            } else {
                // Create a new post
                $post_args = array(
                    'post_title' => 'Color group '. $index, // Set the title
                    'post_type' => 'color_group_cpt',
                    'post_status' => 'publish',
                );
    
                $new_post_id = wp_insert_post($post_args);
    
                if (!is_wp_error($new_post_id)) {
                    // Add terms to the new post's taxonomy
                    wp_set_post_terms($new_post_id, $post, 'color_group');
                    // Set meta data for the new post
                    update_post_meta($new_post_id, 'color_group_meta_field', $index);
                } else {
                    // Handle error
                }
            }
        }
    }

    public function create_price_group(){
        
        foreach($this->price_group_array as $index => $post){
            $args = array(
                'post_type' => 'price_group_cpt',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'price_group_meta_field',
                        'value' => $index,
                    ),
                ),
            );
            
            $query = new WP_Query($args);
            
            if (!$query->have_posts()) {
                // Create a new post
                $post_args = array(
                    'post_title' => 'Price group '. $index, // Set the title
                    'post_type' => 'price_group_cpt',
                    'post_status' => 'publish',
                );
    
                $new_post_id = wp_insert_post($post_args);
    
                if (!is_wp_error($new_post_id)) {
                    // Set meta data for the new post
                    update_post_meta($new_post_id, 'price_group_meta_field', $index);
                } else {
                    // Handle error
                }
            }
        }
    }
}
