jQuery(document).ready(function($) {
    $('#bulk-order').on('submit', function(e) {
        e.preventDefault(); 

        var formData = $(this).serialize(); 
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'wbo_save_bulk_order_settings', 
                form_data: formData
            },
            success: function(response) {
                if (response.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Failed to save settings: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while saving the settings.');
            }
        });
    });
});
