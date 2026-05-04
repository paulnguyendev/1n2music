$('input[name="is_agree"]').change(function() {
    // If checkbox is checked
    const btn = $(this).parent().next();
    if ($(this).is(':checked')) {
        // Enable the button
        btn.prop('disabled', false);
    } else {
        // Disable the button
        btn.prop('disabled', true);
    }
});