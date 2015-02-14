var initSelect2 = function ($el, dataFn) {
    $el.select2({
        tags: true,
        tokenSeparators: [',', ' '],
        minimumInputLength: 0,
        ajax: {
            dataType: 'json',
            data: dataFn
        }
    });
};
