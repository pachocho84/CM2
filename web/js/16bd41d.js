function calculateColumns() {
    var pageWidth = $(window).width();

    var columns;
    if (pageWidth > 900) { // three columns
        columns = '<div class="col-xs-4" col="1"></div><div class="col-xs-4" col="2"></div><div class="col-xs-4" col="3"></div>';
    } else if (pageWidth > 600) { // two columns 
        columns = '<div class="col-xs-6" col="1"></div><div class="col-xs-6" col="2"></div>';
    } else { // one column
        columns = '<div class="col-xs-12" col="1"></div>';
    }

    return columns;
}

function wallLoad(data, t, c, reload) {
    var reload = reload || false;

    var columns = [];
    $.each($('#wall > div'), function(i, col) {
        columns[i] = [$(col), $(col).outerHeight()];
    });

    $.each(data, function(i, box) {
        if (i == 'loadMore') return;

        $box = $(box);
        $box.attr('wall-order', wallOrder);
        wallOrder++;

        columns.sort(function(a, b) { return a[1] > b[1]; });

        $box.hide();
        columns[0][0].append($box);

        if (!reload) {
            $box.find('.cycle-slideshowX').cycle({
                loader: true,
                log: false,
                next: '.box-partner-nav-next',
                pauseOnHover: true,
                prev: '.box-partner-nav-prev',
                slides: '> div',
                swipe: true
            });
        }

        $box.fadeIn('fast');

        columns[0][1] += $box.outerHeight();
    });

    $('#wall ~ .load_more').remove();
    $('#wall').after($(data.loadMore));
}

var wallOrder = 0;
var wallTimer;

$(function() {
    $('#wall').append(calculateColumns());

    $.get(document.URL, function(data) {
        wallLoad(data);
    });

    $(window).resize(function(event) {
        var pageWidth = $(window).width();
        var num = $('#wall > div').length;

        if ((pageWidth > 900 && num != 3) || (pageWidth <= 900 && pageWidth > 600 && num != 2) || (pageWidth <= 600 && num != 1)) {
            clearTimeout(wallTimer);
            wallTimer = setTimeout(function() {
                var $loadMore = $('#wall ~ .load_more').detach();            

                var data = {};
                $.each($('#wall > div > *'), function(i, elem) {
                    $(elem).attr('old-order', $(elem).attr('wall-order'));
                    data[$(elem).attr('wall-order')] = $(elem).detach();
                });
                data['loadMore'] = $loadMore;

                $('#wall').empty().append(calculateColumns());
                wallOrder = 0;
                wallLoad(data, null, null, true);
            }, 200);
        }
    });
});