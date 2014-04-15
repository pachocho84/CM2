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

function wallKeysToAttrs(keys) {
    if (!$.isArray(keys) || keys.length != 3) return '';
    return {'wall-order': '', 'wall-order-1': keys[0], 'wall-order-2': keys[1], 'wall-order-3': keys[2]};
}

function wallAttrsToKeys(elem) {
    if (typeof $(elem).attr('wall-order') === 'undefined') return -1;
    return [$(elem).attr('wall-order-1'), $(elem).attr('wall-order-2'), $(elem).attr('wall-order-3')].join(',');
}

function getFields(obj) {
   var keys = [];
   for(var key in obj){
      keys.push(key);
   }
   return keys;
}

function recalculateWall() {
    var $loadMore = $('#wall ~ .load_more').detach();

    var dataPos = {};
    var dataOrder = {};
    var colIndex = $('#wall > div').length;
    $.each($('#wall > div > [wall-pos]'), function(i, elem) {
        dataPos['pos-' + i + ';' + $(elem).attr('wall-pos')] = $(elem).detach();
    });
    $.each($('#wall > div > [wall-order]'), function(i, elem) {
        dataOrder[$(elem).attr('wall-order')] = $(elem).detach();
    });
    // dataOrder = dataOrder.sort();
    var data = dataPos.concat(dataOrder);

    data['loadMore'] = $loadMore;

console.log(data);

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

    var colIndex = $('#wall > div').length - 1;
    var fields = getFields(data);

    fields = fields.sort(function(a, b) {
        var aP = (a.split(';')[1] || '').split(',')[colIndex];
        var bP = (b.split(';')[1] || '').split(',')[colIndex];
        if (typeof bP === 'undefined') return -1;
        else if (typeof aP === 'undefined') return 1;
        else return parseInt(aP) - parseInt(bP);
    });

    $.each(fields, function(i, field) {
        if (field == 'loadMore') return;

        var $box = $(data[field]);

        if ($box.attr('post-id') != '' && $('.post[post-id="' + $box.attr('post-id') + '"]').length != 0) return;

        var colIndex = $('#wall > div').length;

        var positions = field.split(';')[1];
        var position = (positions || '').split(',')[colIndex - 1];

        var column;
        if (typeof position === 'undefined') {
            columns.sort(function(a, b) { return a.outerHeight() > b.outerHeight(); });
            column = columns[0];
        } else if (position % colIndex == 1 && colIndex == 3) {
            column = $('#wall > div:nth-child(2)');
        } else if ((position % colIndex == 2 && colIndex == 3) || (position % colIndex == 1 && colIndex == 2)) {
            column = $('#wall > div:last');
        } else {
            column = $('#wall > div:first');
        }

        if (typeof positions !== 'undefined') {
            $box.attr('wall-pos', positions);
        } else {
            $box.attr('wall-order', i);
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