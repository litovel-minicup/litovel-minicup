var initDropper = function ($dropperEl, $dropperList, itemTemplate, authorInputName) {
    var dropper = $dropperEl.dropper();
    $dropperEl.on('start.dropper', function (e, files, data) {
        var $authorInput = $('input[name=' + authorInputName + ']:checked');
        data['postData']['author'] = $authorInput.val();
    });
    $dropperEl.on('fileStart.dropper', function (e, file) {
        var $template = $(itemTemplate);
        $dropperList.append($template);
        $template.attr('data-name', file.name).find('p').text(file.name);
    });
    $dropperEl.on('fileProgress.dropper', function (e, file, percent) {
        $dropperList.find('.upload[data-name="' + file.name + '"] .progress-bar').width(percent + '%');
    });

    $dropperEl.on('fileComplete.dropper', function (e, file, response) {
        $dropperList.find('.upload[data-name="' + file.name + '"]').fadeOut(500, function () {
            $(this).remove();
            toastr.success('Tak tohle se Ti povedlo, ' + file.name + ' byl úspěšně nahrán!', 'Dobře ty!');
        });
    });

    $dropperEl.on('fileError.dropper', function (e, file, msg) {
        toastr.error('Ajaj, se souborem ' + file.name + ' je něco špatně: ' + msg, 'Je to špatný');
    });
};

jQuery(function ($) {
    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
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
            // redrawSnippets(request.responseJSON.snippets);
        }
        $('.grido').grido();
    });
    $('.grido').grido();
});