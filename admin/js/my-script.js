jQuery(document).ready(function($) {
    // Access the localized data
    var ajaxurl = my_script_vars.ajax_url;
    var ajax_nonce = my_script_vars.nonce;
    var confirmationMessage = my_script_vars.jsDeleteConfirmation;
    // AJAX function to get suggested contributor names
    $('#contributor-input').on('input', function() {
        var inputVal = $(this).val();
        // Check if inputVal is empty
        if (!inputVal) {
            $('#suggested-names').html('');
            return; // Exit the function
        }
        // Check if inputVal has at least 3 characters
        if (inputVal.length < 3) {
            $('#suggested-names').html('<div class="notice notice-info cursor-style-error">Please enter at least 3 characters...</div>');
            return; // Exit the function
        }
        // Show the loader
        $("#loading").show();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ajax_function_get_contributor_names',
                nonce:   ajax_nonce,
                inputVal: inputVal
            },
            success: function(response) {
                // alert(response);
                if (response) {
                    $('#suggested-names').html(response);
                } else {
                    $('#suggested-names').html('<div class="notice notice-error cursor-style-error">User not found.</div>');
                }
                // Hide the loader
                $("#loading").hide();
            }
        });
    });

    // Add click event listener to suggested names
    $(document).on('click', '.suggested-name', function() {
        var selectedName = $(this).text();
        var selectedNames = $('#contributor-names').val().split(',');
        
        // check if selectedName is not empty or just whitespace
        if (selectedName.trim() !== '') { 
            if ($.inArray(selectedName, selectedNames) == -1) {
                selectedNames.push(selectedName);
                $('#selected-names ul').append('<li class="liStyle">' + selectedName + ' <i class="fas fa-times show-before"></i></li>');
                $('#contributor-names').val(selectedNames.join(','));
            }
        }
    });

    // Remove click event listener to selected names
    $(document).on('click', '#selected-names li i', function() {
        if(confirm(confirmationMessage)){
            var removedName = $(this).parent().text().trim(); // get the text of the parent li element
            var selectedNames = $('#contributor-names').val().split(',');
            var index = selectedNames.indexOf(removedName);
            if (index !== -1) {
                selectedNames.splice(index, 1);
                $(this).parent().remove(); // Remove the parent li element
                $('#contributor-names').val(selectedNames.join(','));
            }
        }
    });

});



