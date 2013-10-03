/* event date */
// Get the ul that holds the collection of tags
var datesCollectionHolder = $('#cm_cmbundle_event_event_dates');

// setup an "add a tag" link
var $addDateLink = $('<a href="#" class="add_date_link">Add a date</a>');
var $newLinkForDate = $('<div></div>').append($addDateLink);

function addEventDateForm(datesCollectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = collectionHolder.data('prototype');

    // get the new index
    var index = datesCollectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    datesCollectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newForm = $('<div></div>').append(newForm);
    $newLinkForDate.before($newForm);
}

/* image */
// Get the ul that holds the collection of tags
var imageCollectionHolder = $('#cm_cmbundle_image_images');

// setup an "add a tag" link
var $addImageLink = $('<a href="#" class="add_date_link">Add an image</a>');
var $newLinkForImage = $('<div></div>').append($addImageLink);

function addImageForm(imageCollectionHolder, $newLinkForImage) {
    // Get the data-prototype explained earlier
    var prototype = imageCollectionHolder.data('prototype');

    // get the new index
    var index = imageCollectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    imageCollectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newForm = $('<div></div>').append(newForm);
    $newLinkForImage.before($newForm);
}

jQuery(document).ready(function() {
    // add the "add a tag" anchor and li to the tags ul
    datesCollectionHolder.append($newLinkForDate);
    imageCollectionHolder.append($newLinkForImage);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    datesCollectionHolder.data('index', datesCollectionHolder.find(':input').length);
    imageCollectionHolder.data('index', imageCollectionHolder.find(':input').length);

    $addDateLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addEventDateForm(datesCollectionHolder, $newLinkForDate);
    });
    $addImageLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addImageForm(imageCollectionHolder, $newLinkForImage);
    });
});