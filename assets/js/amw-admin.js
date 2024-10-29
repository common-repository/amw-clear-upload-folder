jQuery(document).ready(function($) {
    console.log('AMW Clear Upload Folder plugin initialized!');
    var siteUrl = window.location.origin;
    var ajaxUrl = siteUrl + '/wp-admin/admin-ajax.php';

    // Tabs on admin page
    $('#amw-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        tabID = $(this).attr('href').replace('#', '');
    });

    // Folder click script
    $('.amw-folder-button').click(function(e) {
        e.preventDefault();
        input = $(this).find('input');
        isChecked = input.is(":checked");
        folderFullPath = input.val();
        folderName = $('label[for='+input.attr("name")+']').text();

        if(!isChecked) {
            data = {
                'action': 'add_ignored_folder',
                'folderFullPath': folderFullPath,
                'folderName': folderName
            }

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: data,
                success: function (data) {
                    jsonData = $.parseJSON(data);
                    if(jsonData.result){
                        swal("Hooray!", jsonData.result, "success");
                        input.attr('checked', true);
                    } else {
                        swal("Hmm!", "It seems something went wrong!", "warning");
                    }
                }
            });
        } else {
            data = {
                'action': 'remove_ignored_folder',
                'folderFullPath': folderFullPath,
                'folderName': folderName
            }

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: data,
                success: function (data) {
                    jsonData = $.parseJSON(data);
                    if(jsonData.result){
                        swal("Hooray!", jsonData.result, "success");
                        input.attr('checked', false);
                    } else {
                        swal("Hmm!", "It seems something went wrong!", "warning");
                    }
                }
            });
        }
    });

    // Scan and rescan files button
    $('.amw-scan-images-button, .amw-rescan-images-button').click(function() {
        $('#amw-server-files-accordion').html('');

        dataAction = $(this).attr('data-action');

        // Add process indicator
        $('<div class="preloader-wrapper"><img src="'+siteUrl+'/wp-content/plugins/amw-clear-upload-folder/assets/img/preloader.gif"></div>').insertBefore('#amw-server-files-accordion');

        if(dataAction == 'rescan') {
            data = {
                'action': 'render_images_markup',
                'type': dataAction
            }
        } else {
            data = {
                'action': 'render_images_markup',
                'type': dataAction
            }
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: data,
            success: function (data) {
                jsonData = $.parseJSON(data);
                if(jsonData.result){
                    $('.preloader-wrapper').remove();
                    if(dataAction == 'scan') {
                        $('.refresh-img-text').hide();
                        $('<div class="btn amw-run-cleaner-button" data-action="clean">Run cleaner</div>').prependTo('#amw-main');
                    }
                    $(jsonData.result).appendTo('#amw-server-files-accordion');
                } else {
                    $('.preloader-wrapper').remove();
                    swal("Hmm!", "It seems that list of files is empty! Try to remove few folder from the 'IGNORED' list.", "warning");
                }
            }
        });
    });

    // Run cleaner
    $('#amw-main').on('click', '.amw-run-cleaner-button', function() {
        $('#amw-removed-files-accordion').html('');
        $('.amw-run-cleaner-button').hide();
        $('<div class="preloader-wrapper"><img src="'+siteUrl+'/wp-content/plugins/amw-clear-upload-folder/assets/img/preloader.gif"></div>').insertBefore('.preloader-wrapper-box');
        data = {
            'action': 'run_cleaner'
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: data,
            success: function (data) {
                jsonData = $.parseJSON(data);
                if(jsonData.result.length > 0){
                    removedFilesMarkup = '<div class="accordion-group">' +
                        '<div class="accordion-heading">' +
                        '<a class="accordion-toggle" data-toggle="collapse" data-parent="#amw-server-files-accordion" href="#collapse-removed-files">Removed file(s)</a>' +
                        '</div>' +
                        '<div id="collapse-removed-files" class="accordion-body collapse"><div class="accordion-inner"><ol>';
                    $.each(jsonData.result, function(key, value) {
                        removedFilesMarkup += '<li>'+value+'</li>';
                    });
                    removedFilesMarkup +=  +'</ol></div></div></div>';
                    $(removedFilesMarkup).appendTo('#amw-removed-files-accordion');
                    swal("Hooray!", jsonData.result.length + " file(s) were removed!", "success");
                } else if(jsonData.result.length == 0) {
                    swal("Hooray!", "You do not have files to remove!", "success");
                } else {
                    swal("Hmm!", "It seems something went wrong!", "warning");
                }
                $('.preloader-wrapper').remove();
                $('.amw-run-cleaner-button').show();
            }
        });
    });
});