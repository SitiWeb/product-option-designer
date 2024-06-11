<?php
class Product_option_designer_settings {
    public function register() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'register_upload_settings'));
        add_action('admin_post_save_color_options', array($this, 'save_color_options'));
        add_action('admin_post_handle_file_upload', array($this, 'handle_form_submission'));
    }

    public function add_plugin_page() {
        // Add submenu pages under the custom post type menu
        add_submenu_page(
            'edit.php?post_type=color_group_cpt',
            __('Product Option Designer', 'your-plugin-name'),
            __('Product Option Designer', 'your-plugin-name'),
            'manage_options',
            'product-option-designer',
            array($this, 'create_admin_page')
        );

        add_submenu_page(
            'edit.php?post_type=color_group_cpt',
            __('Color Settings', 'your-plugin-name'),
            __('Color Settings', 'your-plugin-name'),
            'manage_options',
            'color-settings',
            array($this, 'create_color_settings_page')
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Product Option Designer Settings', 'your-plugin-name'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('your-plugin-name-settings-group'); ?>
                <?php do_settings_sections('your-plugin-name-settings'); ?>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2><?php _e('Upload Settings', 'your-plugin-name'); ?></h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="handle_file_upload">
                <?php wp_nonce_field('handle_file_upload_nonce'); ?>
                <?php do_settings_sections('your-plugin-name-upload-settings'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function create_color_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Color Settings', 'your-plugin-name'); ?></h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="save_color_options">
                <?php wp_nonce_field('save_color_options_nonce'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Colors', 'your-plugin-name'); ?></th>
                        <td>
                            <table id="color-options-table">
                                <?php
                                $colors = get_option('color_values', []);
                                foreach ($colors as $key => $color) {
                                    echo '<tr>';
                                    echo '<td><input type="text" name="color_values[' . esc_attr($key) . '][label]" value="' . esc_attr($color['label']) . '" placeholder="Label"></td>';
                                    echo '<td><input type="text" name="color_values[' . esc_attr($key) . '][value]" value="' . esc_attr($color['value']) . '" placeholder="Value"></td>';
                                    echo '<td><input type="text" name="color_values[' . esc_attr($key) . '][hex]" value="' . esc_attr($color['hex']) . '" placeholder="Hex"></td>';
                                    echo '<td><button class="button remove-color">Remove</button></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </table>
                            <button class="button" id="add-color"><?php _e('Add Color', 'your-plugin-name'); ?></button>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#add-color').click(function(e) {
                e.preventDefault();
                var row = '<tr>';
                row += '<td><input type="text" name="color_values[new_' + Date.now() + '][label]" placeholder="Label"></td>';
                row += '<td><input type="text" name="color_values[new_' + Date.now() + '][value]" placeholder="Value"></td>';
                row += '<td><input type="text" name="color_values[new_' + Date.now() + '][hex]" placeholder="Hex"></td>';
                row += '<td><button class="button remove-color">Remove</button></td>';
                row += '</tr>';
                $('#color-options-table').append(row);
            });

            $(document).on('click', '.remove-color', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        });
        </script>
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
            'your-plugin-name-upload-settings-group',
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
        ?>
        <input type="file" id="your_plugin_name_upload" name="your_plugin_name_upload">
        <p class="description"><?php _e('Start import by uploading file.', 'your-plugin-name'); ?></p>
        <?php
    }

    public function settings_section_callback() {
        echo '<p>' . __('Select the WooCommerce attribute you want to use:', 'your-plugin-name').'</p>';
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

    public function save_color_options() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'save_color_options_nonce')) {
            wp_die(__('Invalid nonce specified', 'your-plugin-name'), __('Error', 'your-plugin-name'), array(
                'response' => 403,
                'back_link' => 'admin.php?page=color-settings'
            ));
        }

        if (isset($_POST['color_values'])) {
            update_option('color_values', $_POST['color_values']);
        }

        wp_redirect(admin_url('admin.php?page=color-settings&status=1'));
        exit;
    }

    public function handle_form_submission() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'handle_file_upload_nonce')) {
            wp_die(__('Invalid nonce specified', 'your-plugin-name'), __('Error', 'your-plugin-name'), array(
                'response' => 403,
                'back_link' => 'admin.php?page=product-option-designer'
            ));
        }

        if (isset($_FILES['your_plugin_name_upload'])) {
            $file = $_FILES['your_plugin_name_upload'];

            // Check for errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                wp_die(__('File upload error.', 'your-plugin-name'), __('Error', 'your-plugin-name'), array('response' => 400));
            }

            // Move uploaded file to permanent location
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'] . '/colors-import/';
            $upload_file = $upload_path . basename($file['name']);

            if (!file_exists($upload_path)) {
                wp_mkdir_p($upload_path);
            }

            if (!move_uploaded_file($file['tmp_name'], $upload_file)) {
                wp_die(__('Error moving uploaded file.', 'your-plugin-name'), __('Error', 'your-plugin-name'), array('response' => 500));
            }

            // Now parse the uploaded file and import data
            $this->parse_and_import_data($upload_file);
        }

        wp_redirect(admin_url('admin.php?page=product-option-designer&status=1'));
        exit;
    }

    public function parse_and_import_data($file_path) {
        $import = new Color_Group_Import($file_path);
        $import->import_data();
        // Implement your logic to parse and import data from the uploaded file
        // For example, if it's a CSV file, you can use PHP functions like fgetcsv()
        // to read the file line by line and process each row
        // After parsing, you can insert the data into your database or perform any other actions
    }
}

