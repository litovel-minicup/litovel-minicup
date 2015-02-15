var initDropper = function ($dropper, $dropperList, itemTemplate) {
    $dropper.dropper();
    $dropper.on('fileStart.dropper', function (e, file) {
        var $tmpl = $(itemTemplate);
        $dropperList.append($tmpl);
        $tmpl.attr('data-name', file.name).find('p').text(file.name);
    });
    $dropper.on('fileProgress.dropper', function (e, file, percent) {
        $dropperList.find('.upload[data-name="' + file.name + '"] .progress-bar').width(percent + '%');
    });

    $dropper.on('fileComplete.dropper', function (e, file, response) {
        $dropperList.find('.upload[data-name="' + file.name + '"]').fadeOut(500, function () {
            $(this).remove();
            toastr.success('Tak tohle se Ti povedlo, ' + file.name + ' byl úspěšně nahrán!', 'Dobře ty!');
        });
    });

    $dropper.on('fileError.dropper', function (e, file, msg) {
        toastr.error('Ajaj, se souborem ' + file.name + ' je něco špatně: ' + msg, 'Je to špatný');
    });
};