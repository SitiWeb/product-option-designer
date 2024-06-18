<?php

Class Color_group{

    private $post_id;
    private $post;
    private $color_group_id;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
        $this->post = false ;
        $this->color_group_id = false;
    }

    public function get_color_group(){
        if (!$this->color_group_id){
            $this->color_group_id = get_post_meta($this->post_id, 'color_group', true);
        }
        
        return $this->color_group_id;
    }

    public function get_colors(){
        $color_group = $this->get_color_group();
        $result = array();
        
        if ($color_group){
            $taxonomy = 'color_group'; // Replace 'your_taxonomy' with your actual taxonomy slug
            $this->color_group_id;
            // Get the terms associated with the post ID and taxonomy
            $terms = wp_get_post_terms($this->color_group_id, $taxonomy);
          
            // Check if any terms are found
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $color = get_term_meta($term->term_id, 'colorpicker', true);
                    $price_group_id = get_term_meta($term->term_id, 'pricegroup', true);
                    $theme = get_term_meta($term->term_id, 'theme', true);
                    $order = get_term_meta($term->term_id, 'custom_order', true);
                    $filter = get_term_meta($term->term_id, 'term_color_filter', true);
                 
                    if (!$theme){
                        $theme = 'light';
                    }
                    

                    if ($price_group_id){
                        $price_group = $this->get_price_group($price_group_id);
                      
                        if ($price_group){
                            $term_data = array(
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'slug' => $term->slug,
                                'pricegroup' => $price_group,
                                'color' => $color,
                                'pricegroupId' => $price_group_id,
                                'theme' => $theme,
                                'order' => $order,
                                'filter' => $filter
                            );
                            $result[] = $term_data;
                        }   
                    }

                }
                
            } else {
                // If no terms found, return an empty array
                $result = array();
            }
        }
   
        // Return the result as JSON
        return ($result);
    }

    public function get_price_data($post){
        // Check if data is submitted and save it
        $attribute_id = get_option('your_plugin_name_attribute');
        $product_attribute = wc_get_attribute($attribute_id);


        if ($attribute_id && $product_attribute) {
            $terms = get_terms(array(
                'taxonomy' => $product_attribute->slug,
                'hide_empty' => false,
            ));

            if ($terms && !is_wp_error($terms)) {
                $new_terms = [];
                foreach ($terms as $term) {
                    $meta_key = 'color_term_price_' . esc_attr($term->term_id);
                    $term_id = ($term->term_id);
                    $price = (get_post_meta($post->ID,$meta_key, true));
                    $new_terms[] = [
                        'term_id' => $term_id,
                        'price' => $price,
                        'slug' => $term->slug,
                    ];
                }
                $meta_key = 'color_term_price_simple';
                $price = (get_post_meta($post->ID,$meta_key, true));
                $new_terms[] = [
                    'term_id' => 0,
                    'price' => $price,
                    'slug' => 'single',
                ];
              
                return $new_terms;
            }
        }
    }
    

    public function get_price_group($index){
        // Construct a unique transient key based on the index
        $transient_key = 'price_group_data_' . $index;
    
        // Try to get data from the transient first
        $cached_data = get_transient($transient_key);
//         if ($cached_data !== false) {
//             return $cached_data; // Return cached data if it exists
//         }
    
        // Define the query arguments
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
        
        // Execute the query
        $query = new WP_Query($args);
        
        // Check if the query has posts
        if ($query->have_posts()) {
            $query->the_post(); // Set up post data
            $price_data = $this->get_price_data($query->post);
            $query->post->price_data = $price_data;
    
            // Save the result in a transient with a long expiration time
            set_transient($transient_key, $query->post->price_data, DAY_IN_SECONDS * 30); // Example: 30 days expiration
            wp_reset_postdata();
            // Return the post object with price data
            return $query->post->price_data;
        } else {
            return false; // Returning false if no matching post is found
        }
    }
    


    public function calculate_custom_color_price(){
   
    }
    
}

