jQuery(document).ready(function($) {
    $('#remove-group-btn').on('click', function() {
        if (confirm('Are you sure you want to remove all groups?')) {
            removeGroups();
        }
    });

    function removeGroups() {
        // Remove all group elements from the DOM
        $('#accordion').empty();
        console.log(ajaxurl);
        // Send AJAX request to remove all groups
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'remove_all_groups'
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
});