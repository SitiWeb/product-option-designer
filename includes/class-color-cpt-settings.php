<?php

class Product_option_designer_settings {
    public function register(){
        // Add settings page
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'register_upload_settings'));
        add_action('admin_init', array($this, 'handle_form_submission'));
    }

    public function add_plugin_page() {
        add_options_page(
            __('Color Settings', 'your-plugin-name'),
            __('Color settings', 'your-plugin-name'),
            'manage_options',
            'custom-color-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Color settings', 'your-plugin-name'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('your-plugin-name-settings-group'); ?>
                <?php do_settings_sections('your-plugin-name-settings'); ?>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2><?php _e('Upload Settings', 'your-plugin-name'); ?></h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php settings_fields('your-plugin-name-upload-settings-group'); ?>
                <?php do_settings_sections('your-plugin-name-upload-settings'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php 
    }


    public function register_settings() {
        register_setting(
            'your-plugin-name-settings-group',
            'your_plugin_name_attribute',
            array($this, 'sanitize_attribute')
        );

        add_settings_section(
            'your-plugin-name-settings-section',
            __('Select WooCommerce Attribute', 'your-plugin-name'),
            array($this, 'settings_section_callback'),
            'your-plugin-name-settings'
        );

        add_settings_field(
            'your_plugin_name_attribute',
            __('WooCommerce Attribute', 'your-plugin-name'),
            array($this, 'attribute_dropdown_callback'),
            'your-plugin-name-settings',
            'your-plugin-name-settings-section'
        );
    }

    public function register_upload_settings() {
        register_setting(
            'your-plugin-name-upload-settings-group', // Use a different settings group
            'your_plugin_name_upload',
            array($this, 'sanitize_upload')
        );

        add_settings_section(
            'your-plugin-name-upload-section',
            __('Upload File', 'your-plugin-name'),
            array($this, 'upload_section_callback'),
            'your-plugin-name-upload-settings'
        );

        add_settings_field(
            'your_plugin_name_upload_field',
            __('Upload File', 'your-plugin-name'),
            array($this, 'upload_field_callback'),
            'your-plugin-name-upload-settings',
            'your-plugin-name-upload-section'
        );
    }

    public function upload_section_callback() {
        echo '<p>' . __('Upload a CSV file to start import.', 'your-plugin-name') . '</p>';
    }


    public function sanitize_upload($input) {
        // Sanitize upload field if necessary
        return $input;
    }

    public function upload_field_callback() {
        $upload_value = False;
        ?>

        <input type="file" id="your_plugin_name_upload" name="your_plugin_name_upload" value="<?php echo esc_attr($upload_value); ?>">
        <p class="description"><?php _e('Start import by uploading file.', 'your-plugin-name'); ?></p>
        <?php
    }

    public function settings_section_callback() {
        echo '<p>' . __('Select the WooCommerce attribute you want to use:', 'your-plugin-name') . '</p>';
    }

    public function attribute_dropdown_callback() {
      
        ?>
        <select name="your_plugin_name_attribute">
            <?php foreach (wc_get_attribute_taxonomies() as $attribute) : ?>
                <option value="<?php echo esc_attr($attribute->attribute_id); ?>" <?php selected(get_option('your_plugin_name_attribute'), esc_attr($attribute->attribute_id)); ?>>
                    <?php echo esc_html($attribute->attribute_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function sanitize_attribute($input) {
        return sanitize_text_field($input);
    }

    public function handle_form_submission()
    {
        if (isset($_POST['submit'])) {
            // Check nonce, validate fields, etc.
            //check_admin_referer('your-plugin-name-upload-settings-group');
        
            // Handle file upload
            $this->handle_file_upload();
        }
    }

    public function handle_file_upload()
    {
        if (isset($_FILES['your_plugin_name_upload'])) {
            $file = $_FILES['your_plugin_name_upload'];
            
            // Check for errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                // Handle upload error
                return;
            }

            // Move uploaded file to permanent location
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'] . '/colors-import/';
            $upload_file = $upload_path . basename($file['name']);

            if (!file_exists($upload_path)) {
                wp_mkdir_p($upload_path);
            }

            if (!move_uploaded_file($file['tmp_name'], $upload_file)) {
                // Handle file move error
                return;
            }

            // Now parse the uploaded file and import data
            $this->parse_and_import_data($upload_file);
        }
    }
    public function parse_and_import_data($file_path)
    {
        $import = new Color_Group_Import($file_path);
        $import->import_data();
        // Implement your logic to parse and import data from the uploaded file
        // For example, if it's a CSV file, you can use PHP functions like fgetcsv()
        // to read the file line by line and process each row
        // After parsing, you can insert the data into your database or perform any other actions
    }
        
}


