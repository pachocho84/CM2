function addFormDeleteLink($target, text) {
    var $removeFormA = $('<a class="btn btn-default" href="#"><i class="glyphicon glyphicon-minus"></i> ' + text + '</a>');
    $target.append($removeFormA);

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
    $target.children('div.well').slice(1).each(function() {
        addFormDeleteLink($(this), text);
    });

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $target.data('index', $target.find(':input').length);

    $(document).on('click', trigger, function(e) {   
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // get the new index
        var index = $target.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = $target.data('prototype').replace(/__name__label__/g, index).replace(/__name__/g, index);

        // increase the index with one for the next item
        $target.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newForm = $(newForm);
        $target.find('.well:last').after($newForm);

        addFormDeleteLink($newForm, text);

        $(e.currentTarget).trigger("collection-added");
    });
}

jQuery(document).ready(function() {
    uploadCollection($('#cm_cmbundle_event_eventDates'), '.add_date_link', 'delete this date');
    uploadCollection($('#cm_cmbundle_disc_discTracks'), '.add_track_link', 'delete this track');
});


// /* event date */
// // Get the ul that holds the collection of tags
// var datesCollectionHolder = $('#cm_cmbundle_event_eventDates');

// // setup an "add a tag" link
// // var $addDateLink = $('<a href="#" class="add_date_link">Add a date</a>');
// var $newLinkForDate = $('<div></div>'); //.append($addDateLink);

// function addEventDateFormDeleteLink($tagFormLi) {
//     var $removeFormA = $('<a class="btn btn-default" href="#"><i class="glyphicon glyphicon-minus"></i> delete this date</a>');
//     $tagFormLi.find('.well').append($removeFormA);

//     $removeFormA.on('click', function(e) {
//         // prevent the link from creating a "#" on the URL
//         e.preventDefault();

//         // remove the li for the tag form
//         $tagFormLi.remove();
//     });
// }

// /* image */
// // Get the ul that holds the collection of tags
// var imageCollectionHolder = $('#cm_cmbundle_image_images');

// // setup an "add a tag" link
// var $addImageLink = $('<a href="#" class="add_date_link">Add an image</a>');
// var $newLinkForImage = $('<div></div>').append($addImageLink);

// function addImageForm(imageCollectionHolder, $newLinkForImage) {
//     // Get the data-prototype explained earlier
//     var prototype = imageCollectionHolder.data('prototype');

//     // get the new index
//     var index = imageCollectionHolder.data('index');

//     // Replace '__name__' in the prototype's HTML to
//     // instead be a number based on how many items we have
//     var newForm = prototype.replace(/__name__/g, index);

//     // increase the index with one for the next item
//     imageCollectionHolder.data('index', index + 1);

//     // Display the form in the page in an li, before the "Add a tag" link li
//     var $newForm = $('<div></div>').append(newForm);
//     $newLinkForImage.before($newForm);
// }

// jQuery(document).ready(function() {

//     // add the "add a tag" anchor and li to the tags ul
//     datesCollectionHolder.append($newLinkForDate);
//     datesCollectionHolder.children().children('div.form-group').each(function() {
//         addEventDateFormDeleteLink($(this));
//     });
//     imageCollectionHolder.append($newLinkForImage);

//     // count the current form inputs we have (e.g. 2), use that as the new
//     // index when inserting a new item (e.g. 2)
//     datesCollectionHolder.data('index', datesCollectionHolder.find(':input').length);
//     imageCollectionHolder.data('index', imageCollectionHolder.find(':input').length);

//     $(document).on('click', '.add_date_link', function(e) {
//         // prevent the link from creating a "#" on the URL
//         e.preventDefault();

//         // Get the data-prototype explained earlier
//         var prototype = $('#cm_cmbundle_event_eventDates').data('prototype');
    
//         // get the new index
//         var index = $('#cm_cmbundle_event_eventDates').data('index');
    
//         // Replace '__name__' in the prototype's HTML to
//         // instead be a number based on how many items we have
//         var newForm = prototype.replace(/__name__label__/g, index).replace(/__name__/g, index);
    
//         // increase the index with one for the next item
//         $('#cm_cmbundle_event_eventDates').data('index', index + 1);
    
//         // Display the form in the page in an li, before the "Add a tag" link li
//         console.log($('#cm_cmbundle_event_eventDates').find('.well:last'));
//         var $newForm = $(newForm);
//         $('#cm_cmbundle_event_eventDates').find('.well:last').after($newForm);
//         // $newLinkForDate.before($newForm);
//         addEventDateFormDeleteLink($newForm);
//     });
//     $addImageLink.on('click', function(e) {
//         // prevent the link from creating a "#" on the URL
//         e.preventDefault();

//         // add a new tag form (see next code block)
//         addImageForm(imageCollectionHolder, $newLinkForImage);
//     });
// });