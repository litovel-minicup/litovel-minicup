var redrawSnippets = function (snippets) {
    for (var key in snippets) {
        if (snippets.hasOwnProperty(key)) {
            $('#' + key).html(snippets[key]);
        }
    }
};
$(function ($) {
    $.nette.init();
    $.nette.ext({
        success: function () {
            $.nette.load();
        }
    });
});