$(function () {
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

    $(document).ajaxError(function () {
        toastr.error('Něco se podělalo... snad to někdo opraví', 'Je to blbý');
    }).ajaxSuccess(function (event, request, settings) {
        if (request.responseJSON && request.responseJSON.snippets) {
            redrawSnippets(request.responseJSON.snippets);
        }
    });
});