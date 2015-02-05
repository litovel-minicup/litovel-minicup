$(function ($) {
    $.nette.init();
    $.nette.ext({
        success: function () {
            $.nette.load();
        }
    });

    // TODO: move to another droppler init file
    var $uploadDropper = $('#upload-dropper');
    var initDropper = function ($el) {
        $el.dropper({
            action: $uploadDropper.data('upload-link'),
            maxSize: 52428800,
            postData: {uploadId: $uploadDropper.data('upload-id')}
        });
    };
    initDropper($uploadDropper);
    $uploadDropper.on('fileComplete.dropper', function (e, file, response) {
        for (var key in response.snippets) {
            if (response.snippets.hasOwnProperty(key)) {
                $('#' + key).html(response.snippets[key]);
            }
        }
    });
    $uploadDropper.on('fileProgress.dropper', function (e, file, percent) {
        console.log(percent);
        console.log(file);
    });
    $uploadDropper.on('fileError.dropper', function (e, file, error) {
        console.log(error);
    });
    $uploadDropper.on('fileStart.dropper', function(e, file) {
        e.data = {};
        // TODO: fixed adding info about uploaded files
        e.data.postData = {key:5};
        console.log(e);
    });
});