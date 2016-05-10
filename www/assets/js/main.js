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

var redrawSnippets = function (response) {
    if (response.snippets == undefined) {
        return;
    }
    for (var key in response.snippets) {
        if (response.snippets.hasOwnProperty(key)) {
            $('#' + key).html(response.snippets[key]);
        }
    }
};

var attachCover = function ($el) {
    if ($el.find('.Cover').length == 0) {
        if (!$el.is('body')) {
            $el.css('position', 'relative');
        }
        $('<div class="Cover"><div class="Cover__loader"></div></div>').hide().appendTo($el).fadeIn(50);
    }
};

var detachCover = function ($el) {
    $el.find('.Cover').fadeOut(100, function () {
        $(this).remove();
    });
};

var initMobileNav = function ($nav) {
    $nav.find('select').on('change', function (e) {
        window.location = $(e.target).val();
    })
};

var initEasterEgg = function ($el, pattern) {
    var doBarrelRoll = function () {
        $el.find('body').toggleClass('barrel-roll');
    };
    var typed = "";
    $el.keyup(function (e) {
        if (e.key.length !== 1) return;
        typed += e.key;
        if (typed.length > pattern.length) {
            typed = typed.slice(1);
        }
        if (typed.toLocaleLowerCase() === pattern) {
            doBarrelRoll();
        }
    });
};

var initLinkLogging = function () {
    $(document).on('click', 'a.log', function (e) {
        var $link = $(this);
        var category = 'link',
            action = 'click',
            value = $(this).attr('data-value') || $(this).attr('href');
        try {
            ga("send", "event", category, action, value);
        } catch (e) {
        }
    });
};

var renderCategoryHistory = function (data, teamsCount) {
    var chart = new Chartist.Line('.ct-chart', data, {
            high: teamsCount - 1,
            chartPadding: {top: 5, right: 25, bottom: 5, left: 5},
            low: -1,
            showArea: false,
            showLine: true,
            showPoint: true,
            lineSmooth: Chartist.Interpolation.none({
                divisor: 2
            }),
            axisY: {
                scaleMinSpace: 15,
                labelInterpolationFnc: function (value) {
                    return (teamsCount - parseInt(value)) + ". ";
                },
                labelOffset: {
                    x: 0,
                    y: 0
                }
            },
            axisX: {
                labelInterpolationFnc: function (value) {
                    return value;
                }
            }
        }
    );

    var $chart = $('.ct-chart');

    var $toolTip = $chart
        .append('<div class="tooltip"></div>')
        .find('.tooltip')
        .hide();

    $chart.on('mouseenter', '.ct-point', function () {
        var $point = $(this),
            value = $point.attr('ct:value'),
            seriesName = $point.parent().attr('ct:series-name');
        $toolTip.html(seriesName + '<br>' + (teamsCount - parseInt(value)) + ". ").show();
    });

    $chart.on('mouseenter', '.ct-series', function () {
        var $series = $(this);
        $series.insertAfter($series.parent().find('.ct-series:last'));
    });

    $chart.on('mouseleave', '.ct-point', function () {
        $toolTip.hide();
    });

    $chart.on('mousemove', function (event) {
        $toolTip.css({
            left: (event.offsetX || event.originalEvent.layerX) - $toolTip.width() / 2 - 10,
            top: (event.offsetY || event.originalEvent.layerY) - $toolTip.height() - 40
        });
    });

    chart.on('draw', function (data) {
        if (data.type === 'line' || data.type === 'area') {
            data.element.animate({
                d: {
                    begin: 200 * data.index,
                    dur: 200,
                    from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                    to: data.path.clone().stringify(),
                    easing: Chartist.Svg.Easing.easeOutQuint
                }
            });
        } else if (data.type === 'point') {
            console.log(data);
            data.element.animate({
                y1: {
                    begin: 200,
                    dur: 200 * data.index,
                    from: 0,
                    to: data.y,
                    easing: 'easeOutQuart'
                },
                y2: {
                    begin: 200,
                    dur: 200 * data.index,
                    from: 0,
                    to: data.y,
                    easing: 'easeOutQuart'
                },
                opacity: {
                    begin: 200,
                    dur: 200 * data.index,
                    from: 0,
                    to: 1,
                    easing: 'easeOutQuart'
                }
            });
        }
    });
}

jQuery(function ($) {
    $.nette.init();
    $.nette.ext({
        success: function () {
            $.nette.load();
        }
    });

    initMobileNav($('#nav-mobile'));
    initEasterEgg($(document), 'do a barrel roll');
    initLinkLogging();
});