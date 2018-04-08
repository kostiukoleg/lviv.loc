jQuery(document).ready(function ($) {

    $('#do_uploadFile').click(function () {
        $('#uploadFile').click();
    })

    $('#uploadFile').on('change', function () {
        var filename = $('#uploadFile').val().replace(/C:\\fakepath\\/i, '');

        $('input[name="submit-files"]').remove();
        $('span.uploaded-filename').remove();

        $('div.upload-items').append('<input type="submit" name="submit-files" value="Upload"></div>');
        $('#uploadFileList').append('<span class="uploaded-filename">' + filename + '</span>');
    });
});