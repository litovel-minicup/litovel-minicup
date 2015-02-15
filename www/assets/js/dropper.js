var initDropper = function ($el) {
    $el.dropper({
        action: $el.data('upload-link'),
        maxSize: 10000000, // 10 MB
        maxQueue: 2,
        label: 'Natáhni soubory přímo nebo si to proklikni..',
        postData: {uploadId: $el.data('upload-id')}
    });
    $el.on('fileComplete.dropper', function (e, file, response) {
        redrawSnippets(response.snippets);
    });
    $el.on('fileProgress.dropper', function (e, file, percent) {
        console.log(percent);
        console.log(file);
    });
    var onFinally = function(event, file, e) {
        //TODO detach cover
        //TODO ajax for slug multiselect
    };
    $el.on('fileComplete.dropper', onFinally);
    $el.on('fileError.dropper', onFinally);
    $el.on('fileStart.dropper', function(e, file) {
        console.log(file);

    });
};