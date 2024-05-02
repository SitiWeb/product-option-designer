class POD_js_file {
    static POD_page() {
        jQuery('#add-group-btn').on('click', function() {
            var nextGroupNumber = jQuery('.group-item').length + 1;
            var uniqueId = Date.now(); // Unique timestamp identifier
            var groupItem = '<div class="group-item">' +
                '<input type="text" name="group[id][' + uniqueId + ']" value="' + nextGroupNumber + '-' + uniqueId + '" placeholder="ID">' +
                '<input type="text" name="group[name][' + nextGroupNumber + ']" placeholder="Name">' +
                '<input type="text" name="group[price][' + nextGroupNumber + ']" placeholder="Price">' +
                '<button type="button" class="remove-group-btn">Remove Group</button>' +
                '</div>';
            jQuery('#group-container').append(groupItem);
        });

        jQuery('#group-container').on('click', '.remove-group-btn', function() {
            jQuery(this).closest('.group-item').remove();
            // Update group numbers after removal
            jQuery('.group-item').each(function(index) {
                var uniqueId = jQuery(this).find('input[name="group[id][]"]').val().split('-')[1]; // Extract unique identifier
                jQuery(this).find('input[name="group[id][]"]').val((index + 1) + '-' + uniqueId);
            });
        });
    }
}

jQuery(document).ready(function() {
    POD_js_file.POD_page();
});