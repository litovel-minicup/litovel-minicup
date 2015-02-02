$(function($) {
    $.nette.init();
    $.nette.ext({
        success: function () {
            $.nette.load();
        }});
});