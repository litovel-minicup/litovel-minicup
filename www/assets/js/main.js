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
    if (response.snippets === undefined) {
        return;
    }
    for (var key in response.snippets) {
        if (response.snippets.hasOwnProperty(key)) {
            $('#' + key).html(response.snippets[key]);
        }
    }
};

var attachCover = function ($el) {
    if ($el.find('.Cover').length === 0) {
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
    new Chartist.Line(selector, data, {
            high: teamsCount,
            low: 1,
            showArea: false,
            showLine: true,
            showPoint: true,
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
            },
            plugins: [
                Chartist.plugins.legend({}),
                Chartist.plugins.tooltip({})
            ]
        }
    );
};

var renderScoreChart = function (data, selector) {
    new Chartist.Bar(selector, data, {
            seriesBarDistance: 10,
            chartPadding: {
                left: -5,
                right: 15
            },
            axisY: {
                showLabel: false,
                offset: 0
            },
            axisX: {
                offset: 50,
                scaleMinSpace: 40
            },
            plugins: [
                Chartist.plugins.ctBarLabels({
                    thresholdPercentage: 5,
                    labelPositionFnc: function (data) {
                        return {
                            labelOffset: {
                                x: 7.5,
                                y: 15
                            },
                            textAnchor: 'start'
                        }
                    }
                })
            ]
        }
    );
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

var initTabs = function () {
    if ($('.Box__head__toggle .active').length === 0) {
        $('.Box__head__toggle a:first-child').addClass('active');
    }
    if ($('.tab-content .active').length === 0) {
        $('.tab-content .tab-pane:first-child').addClass('active');
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var $tab = $(e.target);
        $tab.parent().find('.active').removeClass('active');
        $tab.addClass('active'); // newly activated tab
    })
};

jQuery(function ($) {
    if ($.nette) {
        $.nette.init();
        $.nette.ext({
            success: function () {
                $.nette.load();
            }
        });
    }

    // initMobileNav($('#nav-mobile'));
    initEasterEgg($(document), 'do a barrel roll');
    initLinkLogging();
    initTabs();
});