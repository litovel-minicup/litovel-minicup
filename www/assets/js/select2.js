var initTagsSelect2 = function ($el) {
    $el.select2({
        tags: true,
        tokenSeparators: [',', ' '],
        minimumInputLength: 0,
        ajax: {
            dataType: 'json',
            data: function(term, page) {
                return {
                    term: term['term']
                };
            }
        }
    });
    return $el;
};
