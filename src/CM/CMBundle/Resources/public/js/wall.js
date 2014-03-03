function calculateColumns() {
    var pageWidth = $(window).width();

    if (pageWidth > 900) { // three columns
        $('#wall').append('<div class="col-xs-4"></div><div class="col-xs-4"></div><div class="col-xs-4"></div>');
    } else if (pageWidth > 600) { // two columns 
        $('#wall').append('<div class="col-xs-6"></div><div class="col-xs-6"></div>');
    } else { // one column
        $('#wall').append('<div class="col-xs-12"></div>');
    }
}

$(function() {
    calculateColumns();

    $.get(document.URL, function(data) {
        var columns = [];
        $.each($('#wall > div'), function(i, col) {
            columns[i] = [$(col), $(col).outerHeight()];
        });

        $.each(data, function(i, box) {
            $box = $(box);

            columns.sort(function(a, b) { return a[1] > b[1]; });

            $box.hide();
            columns[0][0].append($box);

            $box.fadeIn('fast');

            columns[0][1] += $box.outerHeight();
        });

        
    });
});