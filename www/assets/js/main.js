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

var renderCategoryHistory = function (data, teamsCount, selector) {
    var chart = new Chartist.Line(selector,
        data, {
            high: teamsCount,
            low: 1,
            showArea: false,
            showLine: true,
            showPoint: false,
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 0.5
            }),
            chartPadding: {
                right: -50,
                top: 10,
                left: 10,
                bottom: 10
            },
            axisY: {
                showLabel: false,
                offset: 0,
                showGrid: false
            },
            axisX: {
                showLabel: false,
                offset: 0
            }
        }
    );

    var $chart = $(selector);

    var $toolTip = $chart
        .append('<div class="tooltip"></div>')
        .find('.tooltip')
        .hide();

    $chart.on('mouseenter', '.ct-series', function () {
        var $series = $(this);
        var name = $series.attr('ct:series-name');
        $toolTip.text(name).show();

        $series.insertAfter($series.parent().find('.ct-series:last'));
    });

    $chart.on('mouseleave', '.ct-series', function () {
        $toolTip.hide();
    });

    $chart.on('mousemove', function (event) {
        $toolTip.css({
            left: (event.offsetX || event.originalEvent.layerX) - $toolTip.width() / 2,
            top: (event.offsetY || event.originalEvent.layerY) - $toolTip.height() / 2
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
};

var renderScoreChart = function (data, selector) {
    new Chartist.Bar(selector, data, {
        seriesBarDistance: 10,
        chartPadding: {
            left: 0,
            right: 0
        },
        axisY: {
            showLabel: true,
            offset: 15,
            onlyIntegers: true,
            labelOffset: {
                x: 18,
                y: 7
            }
        },
        axisX: {
            offset: 50,
            scaleMinSpace: 40
        }
    });
};

var renderSingleTeamHistoryChart = function (selector, data, teamsCount) {
    var chart = new Chartist.Line(selector, data, {
            high: teamsCount,
            low: 1,
            showArea: true,
            showLine: true,
            showPoint: false,
            areaBase: -5,
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 1
            }),
            chartPadding: {
                top: 10,
                left: 10,
                right: 10,
                bottom: 25
            },
            axisY: {
                showLabel: false,
                offset: 0,
                showGrid: false
            },
            axisX: {
                labelInterpolationFnc: function (value) {
                    return value;
                }
            }
        }
    );
    /*chart.on('draw', function (data) {
     if (data.type === 'line' || data.type === 'area') {
     data.element.animate({
     d: {
     begin: 2000 * data.index,
     dur: 2000,
     from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
     to: data.path.clone().stringify(),
     easing: Chartist.Svg.Easing.easeOutQuint
     }
     });
     }
     });*/
};

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