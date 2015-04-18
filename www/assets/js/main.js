var initTagsSelect2 = function ($el) {
    $el.select2({
        tags: true,
        tokenSeparators: [',', ' '],
        minimumInputLength: 0,
        ajax: {
            dataType: 'json',
            data: function (term, page) {
                return {
                    term: term['term']
                };
            }
        }
    });
    return $el;
};

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

var redrawSnippets = function (snippets) {
    for (var key in snippets) {
        if (snippets.hasOwnProperty(key)) {
            $('#' + key).html(snippets[key]);
        }
    }
};

var attachCover = function ($el) {
    if ($el.find('.Cover').length == 0) {
        if (!$el.is('body')) {
            $el.css('position', 'relative');
        }
        $('<div class="Cover"><div class="Cover__loader"></div></div>').hide().appendTo($el).fadeIn(200);
    }
};

var detachCover = function ($el) {
    $el.find('.Cover').fadeOut(250, function () {
        $(this).remove();
    });
};

$(function ($) {
    $.nette.init();
    $.nette.ext({
        success: function () {
            $.nette.load();
        }
    });
});