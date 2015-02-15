$(function () {
    toastr.options = {
        "closeButton": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
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
    $(document).ajaxError(function() {
        toastr.error('Něco se podělalo... snad to někdo opraví', 'Je to na nic');
    });
});