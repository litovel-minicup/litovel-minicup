$(function () {
    toastr.options = {
        "closeButton": false,
        "newestOnTop": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "fadeOut"
    };
    $(document).ajaxError(function () {
        toastr.error('Něco se podělalo... snad to někdo opraví', 'Je to na nic');
    }).ajaxSuccess(function (event, request, settings) {
        if (request.responseJSON && request.responseJSON.snippets) {
            redrawSnippets(request.responseJSON.snippets);
        }
    });
});