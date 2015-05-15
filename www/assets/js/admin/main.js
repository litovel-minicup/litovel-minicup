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

jQuery(function ($) {
    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "fadeOut"
    };

    $(document).ajaxError(function (e, r) {
        if (r.status === 403) {
            window.location.reload(true);
        } else {
            toastr.error('Něco se podělalo... snad to někdo opraví', 'Je to blbý');
        }
    }).ajaxSuccess(function (event, request, settings) {
        if (request.responseJSON && request.responseJSON.snippets) {
            redrawSnippets(request.responseJSON.snippets);
        }
        $('.grido').grido();
    });
    $('.grido').grido();
});