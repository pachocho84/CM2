var wallColW3 = 1200;
var wallColW2 = 750;
var wallOrder = 0;
var wallTimer;

function calculateColumns() {
    var pageWidth = $(window).width();

    var columns;
    if (pageWidth > wallColW3) { // three columns
        columns = '<div class="col-xs-4" col="1"></div><div class="col-xs-4" col="2"></div><div class="col-xs-4" col="3"></div>';
    } else if (pageWidth > wallColW2) { // two columns 
        columns = '<div class="col-xs-6" col="1"></div><div class="col-xs-6" col="2"></div>';
    } else { // one column
        columns = '<div class="col-xs-12" col="1"></div>';
    }

    return columns;
}

function recalculateWall() {
    var $loadMore = $('#wall ~ .load_more').detach();

    var data = {};
    $.each($('#wall > div > [wall-col="left"'), function(i, elem) {
        data[$(elem).attr('wall-order') + ';left'] = $(elem).detach();
    });
    $.each($('#wall > div > [wall-col="center,left"'), function(i, elem) {
        data[$(elem).attr('wall-order') + ';center,left'] = $(elem).detach();
    });
    $.each($('#wall > div > [wall-col="center,right"'), function(i, elem) {
        data[$(elem).attr('wall-order') + ';center,right'] = $(elem).detach();
    });
    $.each($('#wall > div > [wall-col="right"'), function(i, elem) {
        data[$(elem).attr('wall-order') + ';right'] = $(elem).detach();
    });
    $.each($('#wall > div > *:not([wall-col])'), function(i, elem) {
        data['order' + $(elem).attr('wall-order')] = $(elem).detach();
    });
    data['loadMore'] = $loadMore;

    $('#wall').empty().append(calculateColumns());
    wallOrder = 0;
    wallLoad(data, null, null, true);
}

function wallLoad(data, t, c, reload) {
    var reload = reload || false;

    var columns = [];
    $.each($('#wall > div'), function(i, col) {
        columns[i] = $(col);
    });

    $('#wall ~ .load_more').remove();

    $.each(data, function(i, box) {
        if (i == 'loadMore') return;

        var position = i.split(';')[1];

        var $box = $(box);
        $box.attr('wall-order', wallOrder);
        wallOrder++;
        
        var column;
        if (position == 'left') {
            $box.attr('wall-col', 'left');
            column = $('#wall > div:first');
        } else if (position == 'right') {
            $box.attr('wall-col', 'right');
            column = $('#wall > div:last');
        } else if (position == 'center,left') {
            $box.attr('wall-col', 'center,left');
            if ($('#wall > div').length == 3) {
                column = $('#wall > div:nth-child(2)');
            } else {
                column = $('#wall > div:first');
            }
        } else if (position == 'center,right') {
            $box.attr('wall-col', 'center,right');
            if ($('#wall > div').length == 3) {
                column = $('#wall > div:nth-child(2)');
            } else {
                column = $('#wall > div:last');
            }
        } else {
            columns.sort(function(a, b) { return a.outerHeight() > b.outerHeight(); });
            column = columns[0];
        }

        // $box.hide();
        column.append($box);

        if (!reload) {
            initSlideshow($box.find('.cycle-slideshow'));
        }

        // $box.find('img').each(function() {
        //     if (!this.src.match(/\/(banner|medium|full)\//)) return;
        //     $.ajax(this.src, {async: false});
        // });

        $box.show();
    });

    $('#wall').after($(data.loadMore));

    $('#wall').trigger('boxLoaded');
}

$(function() {
    $('#wall').append(calculateColumns());

    $.get(document.URL, function(data) {
        wallLoad(data);
    });

    $(window).resize(function(event) {
        var pageWidth = $(window).width();
        var num = $('#wall > div').length;

        if ((pageWidth > wallColW3 && num != 3) || (pageWidth <= wallColW3 && pageWidth > wallColW2 && num != 2) || (pageWidth <= wallColW2 && num != 1)) {
            clearTimeout(wallTimer);
            wallTimer = setTimeout(function() {
                recalculateWall();
            }, 200);
        }
    });



    // NAV TABS
    $('body').on('click', '.box .nav.nav-tabs a', function (event) {
        event.preventDefault();
        $(this).tab('show');
    })
});