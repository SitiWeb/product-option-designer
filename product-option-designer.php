<?php
/* 
Plugin Name: POD by SitiWeb
Description: Product Option Designer
Version: 1.0
Author: SitiWeb
*/
class POD_Settings {
    
    // Constructor
    public function __construct() {
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_scripts'));
    }
    
    public function enqueue_custom_scripts() {
        // Enqueue jQuery UI library
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-accordion');
    
        // Enqueue Spectrum color picker library
        wp_enqueue_script('spectrum', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js', array('jquery'), '1.8.0', true);
        wp_enqueue_style('spectrum', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css', array(), '1.8.0');
    }
    
    
    
    // Plugin settings page
    public function settings_page() {
        ?>
        <div class="wrap">
            <h2>POD plugin Settings</h2>
            <form method="post" action="options.php">
                <?php
                // Output security fields
                settings_fields('pod_settings_group');
                // Output settings sections
                do_settings_sections('pod_settings_page');
                // Submit button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }
    
    // Register settings page
    public function register_settings_page() {
        add_options_page('POD Plugin Settings', 'POD Plugin', 'manage_options', 'pod_settings_page', array($this, 'settings_page'));
    }
    
    // Register plugin settings
    public function register_settings() {
        // Add section
        add_settings_section('pod_general_section', 'General Settings', array($this, 'general_section_callback'), 'pod_settings_page');
        // Add fields
        add_settings_field('pod_repeater_field', 'Repeater Field', array($this, 'repeater_field_callback'), 'pod_settings_page', 'pod_general_section');
        // Register settings
        register_setting('pod_settings_group', 'pod_repeater_field', array($this, 'sanitize_repeater_field'));
    }
    
    // Section callback
    public function general_section_callback() {
        echo '<p>General settings for POD plugin</p>';
    }
    
   // Repeater field callback
// Repeater field callback
public function repeater_field_callback() {
    $repeater_data = get_option('pod_repeater_field');
    echo "<pre>";
    //var_dump($repeater_data);
    echo "</pre>";
    ?>
    <div id="repeater-container">
    <?php if ($repeater_data && is_array($repeater_data)) :
    foreach ($repeater_data as $index => $values) : ?>
        
        <h3 id="groupowner<?php echo $index; ?>">Group <?php echo !empty($values['name']) ? esc_attr($values['name']) : 'unnamed';  ?></h3>
        <div class="repeater-row" >
            <label for="pod_repeater_field_<?php echo $index; ?>_name">Name:</label>
            <input id="pod_repeater_field_<?php echo $index; ?>_name" type="text" name="pod_repeater_field[<?php echo $index; ?>][name]" value="<?php echo isset($values['name']) ? esc_attr($values['name']) : ''; ?>" placeholder="Name" />
            <label for="pod_repeater_field_<?php echo $index; ?>_price">Price:</label>
            <input id="pod_repeater_field_<?php echo $index; ?>_price" type="text" name="pod_repeater_field[<?php echo $index; ?>][price]" value="<?php echo isset($values['price']) ? esc_attr($values['price']) : ''; ?>" placeholder="Price" />
            <div class="subrows-container">
                <?php if (isset($values['subrows']) && is_array($values['subrows'])) :
                    foreach ($values['subrows'] as $subindex => $subvalue) : ?>
                        <div class="subrow">
                            <label for="pod_repeater_field_<?php echo $index; ?>_sub_<?php echo $subindex; ?>_colorname">Color Name:</label>
                            <input id="pod_repeater_field_<?php echo $index; ?>_sub_<?php echo $subindex; ?>_colorname" type="text" name="pod_repeater_field[<?php echo $index; ?>][subrows][<?php echo $subindex; ?>][colorname]" value="<?php echo isset($subvalue['colorname']) ? esc_attr($subvalue['colorname']) : ''; ?>" placeholder="Color Name" />
                            <label for="pod_repeater_field_<?php echo $index; ?>_sub_<?php echo $subindex; ?>_colorhex">Color HEX:</label>
                            <input id="pod_repeater_field_<?php echo $index; ?>_sub_<?php echo $subindex; ?>_colorhex" type="text" name="pod_repeater_field[<?php echo $index; ?>][subrows][<?php echo $subindex; ?>][colorhex]" class="colorpicker" value="<?php echo isset($subvalue['colorhex']) ? esc_attr($subvalue['colorhex']) : ''; ?>" placeholder="Color HEX" />
                            <button class="remove-subrow">Remove</button>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>
            <button class="add-subrow" id="repeater-row_<?php echo $index; ?>">Add Subrow</button>
            <button class="remove-row">Remove</button>
        </div>
    <?php endforeach;?>
<?php endif; ?>

    </div>
    <div style="margin-top: 10px;">
        <button id="add-row">Add Row</button>
    </div>
    <script>
    jQuery(document).ready(function() {
        jQuery('.colorpicker').spectrum({
            preferredFormat: "hex",
            showInput: true,
            showAlpha: false,
            allowEmpty: true
        });
    });
    jQuery(document).ready(function($) {
    // Initialize accordion
    $('#repeater-container').accordion({
        collapsible: true, // Allow all panels to be collapsed
        heightStyle: 'content' // Set height based on content
    });

    $('#add-row').click(function() {
        var index = $('#repeater-container .repeater-row').length;
        $('<h3>Row ' + index + '</h3><div class="repeater-row"><label for="pod_repeater_field_' + index + '_name">Name:</label><input id="pod_repeater_field_' + index + '_name" type="text" name="pod_repeater_field[' + index + '][name]" placeholder="Name" /><label for="pod_repeater_field_' + index + '_price">Price:</label><input id="pod_repeater_field_' + index + '_price" type="text" name="pod_repeater_field[' + index + '][price]" placeholder="Price" /><div class="subrows-container"></div><button class="add-subrow">Add Subrow</button><button class="remove-row">Remove</button></div>').appendTo('#repeater-container').accordion('refresh');
        return false;
    });

    $(document).on('click', '.add-subrow', function() {
        
        console.log(this);
        var container = $(this).prev('.subrows-container');
        var index = container.find('.subrow').length;
        $('<div class="subrow"><label for="pod_repeater_field_' + index + '_sub_' + index + '_colorname">Color Name:</label><input id="pod_repeater_field_' + index + '_sub_' + index + '_colorname" type="text" name="pod_repeater_field[' + container.closest('.repeater-row').index() + '][subrows][' + index + '][colorname]" placeholder="Color Name" /><label for="pod_repeater_field_' + index + '_sub_' + index + '_colorhex">Color HEX:</label><input id="pod_repeater_field_' + index + '_sub_' + index + '_colorhex" type="text" name="pod_repeater_field[' + container.closest('.repeater-row').index() + '][subrows][' + index + '][colorhex]" placeholder="Color HEX" /><button class="remove-subrow">Remove</button></div>').appendTo(container);
        return false;
    });

    $(document).on('click', '.remove-subrow', function() {
        $(this).closest('.subrow').remove();
        return false;
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('.repeater-row').prev('h3').remove(); // Remove corresponding accordion header
        $(this).closest('.repeater-row').remove();
        $('#repeater-container').accordion('refresh');
        return false;
    });
});



    </script>
    <?php
}


    
    // Sanitize repeater field
public function sanitize_repeater_field($input) {
    $sanitized_input = array();
    if (is_array($input)) {
        foreach ($input as $values) {
            $sanitized_values = array(
                'name' => sanitize_text_field($values['name']),
                'price' => sanitize_text_field($values['price']),
                'subrows' => array()
            );
            if (isset($values['subrows']) && is_array($values['subrows'])) {
                foreach ($values['subrows'] as $subrow) {
                    $sanitized_subrow = array(
                        'colorname' => sanitize_text_field($subrow['colorname']),
                        'colorhex' => sanitize_text_field($subrow['colorhex'])
                    );
                    $sanitized_values['subrows'][] = $sanitized_subrow;
                }
            }
            $sanitized_input[] = $sanitized_values;
        }
    }
    return $sanitized_input;
}

}

// Instantiate the class
$pod_settings = new POD_Settings();


?>