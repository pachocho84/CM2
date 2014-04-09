function newFromPrototype($target) {
    // get the new index
    var index = $target.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = $target.data('prototype').replace(/__name__label__/g, index).replace(/__name__/g, index);

    // increase the index with one for the next item
    $target.data('index', index + 1);

    return $(newForm);
}

function addFormDeleteLink($target, text) {
    var $removeFormA = $('<a class="btn btn-default" href="#"><span class="glyphicon glyphicon-minus"></span> ' + text + '</a>');
    if ($target.find('.panel-footer') < 1) {
        $target.append('<div class="panel-footer"><div class="btn-group"></div></div>');
    }
    $target.find('.btn-group').append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $target.remove();
    });
}

function uploadCollection($target, trigger, text) {
    // add the "add a tag" anchor and li to the tags ul
    $target.append($('<div></div>'));
    $target.children('div.panel-body').slice(1).each(function() {
        addFormDeleteLink($(this), text);
    });

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $target.data('index', $target.find(':input').length);

    $(document).on('click', trigger, function(e) {   
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        var $newForm = newFromPrototype($target);

        // Display the form in the page in an li, before the "Add a tag" link li
        $target.find('.panel:last').after($newForm);

        addFormDeleteLink($newForm, text);

        $(e.currentTarget).trigger('collection-added', $newForm);
    });
}

$(function() {
    uploadCollection($('#cm_cmbundle_event_eventDates'), '.add_date_link', $('.add_date_link').attr('delete_date-text'));
    uploadCollection($('#cm_cmbundle_disc_discTracks'), '.add_track_link', $('.add_track_link').attr('delete_track-text'));

    // $('body').on('click', '.copy_date_link', function(event) {
    //     event.preventDefault();

    //     var $newForm = newFromPrototype($($(event.currentTarget).attr('href')));
        
    //     // make a copy of each input
    //     $(event.currentTarget).closest('.panel-body').find('input, textarea, select').each(function(i, elem) {
    //         var copy = $newForm.find('input, textarea, select').get(i);
    //         var id = $(copy).attr('id');
    //         var name = $(copy).attr('name');
    //         $(copy).replaceWith($(elem).clone(true, true));
    //         $(copy).attr('id', id);
    //         $(copy).attr('name', name);
    //     });
        
    //     $(event.currentTarget).closest('.panel').after($newForm);
    // });
});