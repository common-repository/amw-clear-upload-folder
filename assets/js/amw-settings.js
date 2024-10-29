jQuery(document).ready(function($) {
    console.log('AMW Clear Upload Folder plugin settings js initialized!');
    var siteUrl = window.location.origin;
    var ajaxUrl = siteUrl + '/wp-admin/admin-ajax.php';

    // Remove or not thumbs settings button
    $('.amv-update-thumbs').click(function(e) {
        e.preventDefault();
        input = $(this).find('input');
        isChecked = input.is(':checked');
        span = input.next('span');

        if(isChecked) {
            data = {
                'action': 'update_thumbs',
                'value': false
            }
        } else {
            data = {
                'action': 'update_thumbs',
                'value': true
            }
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: data,
            success: function (data) {
                jsonData = $.parseJSON(data);
                if(jsonData.result){
                    if(isChecked) {
                        span.text('No');
                        input.attr('checked', false);
                    } else {
                        span.text('Yes');
                        input.attr('checked', true);
                    }
                } else {
                    swal("Hmm!", "It seems that somethins went wrong! Please try one more time.", "warning");
                }
            }
        });
    });

    // Extension textarea
    $('.extensions-checkbox').change(function (e) {
        that = $(this);
        value = that.find('input').val();

        if(!value) {
            swal("Hmm!", "Please enter any extension type in the textarea.", "warning");
        } else {
            data = {
                'action': 'update_extensions',
                'value': value
            }

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: data,
                success: function (data) {
                    jsonData = $.parseJSON(data);
                    if(jsonData.result){
                        swal("Hooray!", "Extension "+value+" was updated.", "success");
                    } else {
                        swal("Hmm!", "It seems that somethins went wrong! Please try one more time.", "warning");
                    }
                }
            });
        }
    });
});