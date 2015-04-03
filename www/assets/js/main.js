var redrawSnippets = function (snippets) {
    for (var key in snippets) {
        if (snippets.hasOwnProperty(key)) {
            $('#' + key).html(snippets[key]);
        }
    }
};
var attachCover = function($el) {
    if ($el.find('.Cover').length == 0) {
        if (!$el.is('body')) {
            $el.css('position', 'relative');
        }
        $('<div class="Cover"><div class="Cover__loader"></div></div>').hide().appendTo($el).fadeIn(200);
    }
};
var detachCover = function($el) {
    $el.find('.Cover').fadeOut(250, function() {
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