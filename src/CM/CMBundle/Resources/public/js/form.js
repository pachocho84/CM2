function addRecipient(c, d, a)
{
    recipients = $(c).find('#recipients');
    messageRecipients = $(c).find('#message_recipients');
    r = recipients.attr('prototype').replace("__id__", d['id']).replace("__username__", d['username']).replace("__fullname__", d['fullname']);
    recipients.html(recipients.html() + r);
    messageRecipients.val(messageRecipients.val() + (messageRecipients.val() ? ',' : '') + d['username']);
}

$(function() {
    /* PROTAGONIST */

    var protagonist_new_id = parseInt(1 + $('.protagonists_user:last').attr('protagonist_new_id')) + 5;
    var collection = $('.protagonist_typeahead').children('.collection-items');
    $('#protagonists_finder').typeahead({
        name: 'protagonists',
        valueKey: 'fullname',
        template: '{{{ view }}}',
        engine: Hogan,
        remote:  {
            url: typeaheadHintRoute + '?query=%QUERY',
            replace: function (url, uriEncodedQuery) {
                return url.replace('%QUERY', uriEncodedQuery) + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user_id'); }).get().join(',');
            },
            cache: false
        },
    });
    $(document).on('typeahead:autocompleted typeahead:selected', '.protagonist_typeahead', function (event, datum) {
        protagonist_new_id += 1;
        if ($(event.currentTarget).is('[typeahead-callback]')) {
            callback = $(event.currentTarget).attr('typeahead-callback');
        } else {
            callback = $(event.currentTarget).find('li[typeahead-callback]').attr('typeahead-callback');
        }
        if (callback.substring(0, 1) == '$') {
            callback = callback.substring(1);
            func = callback.split('(')[0];
            args = callback.split('(').slice(1).join('(').slice(0, -1);
            window[func](event.currentTarget, datum, args);
        } else {
            target = callback.replace(/USER_ID/, datum.id).replace(/NEW_ID/, protagonist_new_id).replace(/ENTITY_TYPE/, $('#protagonists').attr('object'));
            $.get(target, function (data) {
                $('.protagonists_user:last').after(data);
                $('.protagonists_user:last').trigger('protagonist-added');
            });
        }
        $('#protagonists_finder').typeahead('setQuery', '');
    });

    $(document).on('click', '.protagonists_remove', function (event) {
        event.preventDefault();
        var removeId = $(event.target).attr('id');
        if (parseInt(removeId.substring(removeId.lastIndexOf('_') + 1)) != 0) {
            $(this).closest('.protagonists_user').remove();
        }
    });
    // group
    $(document).on('change', '.protagonists_group', function (event) {
        event.preventDefault();
        var group = $(this).children('option:selected').attr('value');
        $('.protagonists_user[group_id]').each(function () {
            $(this).remove();
        });
        if (group != '') {
            $.get(script + '/protagonist/addGroup?group_id=' + group + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user_id'); }).get().join(',') + '&protagonist_new_id=' + (parseInt($('.protagonists_user:last').attr('protagonist_new_id')) + 1), function (data) {
                $('.protagonists_user:last').after(data);
            });
        }
    });
    // page
    $(document).on('change', '.protagonists_page', function (event) {
        event.preventDefault();
        var group = $(this).children('option:selected').attr('value');
        $('.protagonists_user[page_id]').each(function () {
            $(this).remove();
        });
        if (group != '') {
            $.get(script + '/protagonist/addPage?page_id=' + group + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user_id'); }).get().join(',') + '&protagonist_new_id=' + (parseInt($('.protagonists_user:last').attr('protagonist_new_id')) + 1), function (data) {
                $('.protagonists_user:last').after(data);
            });
        }
    });
    // relation
    $(document).on('click', '#protagonists_finder_container .dropdown-menu li', function(event) {
        $('#protagonists_finder_container .dropdown-menu li').each(function(i, li) {
            $(li).removeAttr('active');
        });
        $(event.currentTarget).attr('active', 'active');
        $(event.currentTarget).parent().siblings('button').html($(event.currentTarget).attr('name') + ' <span class="caret"></span>');
    });
    // recipient
    $(document).on('click', '.recipient .close', function(event) {
        val = $(event.currentTarget).closest('.protagonist_typeahead').find('#message_recipients').val().split(',');
        i = val.indexOf($(event.currentTarget).parent().attr('recipient'));
        if (i >= 0) {
            val.splice(i, 1);
        }
        $(event.currentTarget).closest('.protagonist_typeahead').find('#message_recipients').val(val.join(','));
        console.log($(event.currentTarget).closest('.protagonist_typeahead').find('#message_recipients').val());
        $(event.currentTarget).parent().remove();
    });
    
    
    
    /* AUTOCOMPLETE */

    // Places autocomplete
    function initializePlaces(index) {
        canvas = $('[gmap-canvas]').get(index);
        input = $(canvas).parent().parent().parent().find('[places-autocomplete]').get(0);

        var mapOptions = {
            center: new google.maps.LatLng(45.4654542, 9.186515999999999),
            zoom: 14
        };
        var map = new google.maps.Map(canvas,
            mapOptions);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map
        });

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
            }
  /*
          marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
*/
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            $(canvas).parent().parent().parent().find('[address-autocomplete]').val(address);
            $(canvas).parent().parent().parent().find('[places-autocomplete]').val(place.name);
            $(canvas).parent().parent().parent().find('[address-coordinates]').val(place.geometry.location);
            infowindow.open(map, marker);
        });
    }
    
    $('[gmap-canvas]').each(function(i) {
        google.maps.event.addDomListener(window, 'load', initializePlaces(i));
    });
    $(document).on('collection-added', function(event) {
        google.maps.event.addDomListener(window, 'load', initializePlaces(-1));
    });

    $(document).on('keydown', '[gmap-canvas]', function(event) {
        if (event.which == 13) {
            event.preventDefault();
        }
    });

    // Address autocomplete
    $('[address-autocomplete]').typeahead({
        name: 'address',
        // minLength: 3,
        valueKey: 'val',
        template: '<div>{{ val }}</div>',
        engine: Hogan,
        remote: {
            url: 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&culture=' + culture + '&address=%QUERY',
            replace: function (url, uriEncodedQuery) {
                return url.replace('%QUERY', uriEncodedQuery);
            },
            filter: function(data) {
                if (data.status == 'OK') {
                    return $.map(data.results, function(address) {
                        value = new Object();
                        value.val = address.formatted_address;
                        value.coords = address.geometry.location.lat + ',' + address.geometry.location.lng;
                        return value;
                    });
                }
            }
        }
    });
    $(document).on('typeahead:autocompleted typeahead:selected', '.event_date', function (event, datum) {
        $(event.currentTarget).find('[address-coordinates]').val(datum.coords);
    });

    // City autocomplete
    $('[autocomplete-city]').typeahead({
        name: 'cities',
        minLength: 3,
        template: '<div>{{ value }}</div>',
        engine: Hogan,
        remote: {
            url: 'http://api.geonames.org/searchJSON?formatted=true&style=full&username=circuitomusica&maxRows=8&lang=' + culture + '&q=%QUERY&type=json',
            filter: function(data) {
                data = $.map(data.geonames, function(city) {
                    return city.name + (city.adminName1 ? ", " + city.adminName1 : "") + ", " + city.countryName;
                });
                return data;
            }
        }
    });
    
//     var GooglePlacesService = new google.maps.places.AutocompleteService();
    
//   var burnsvilleMN = new google.maps.LatLng(44.797916,-93.278046);
//   // Creating a map
//   var map = new google.maps.Map($('#map')[0], {
//     zoom: 15,
// /*     center: burnsvilleMN, */
// /*     disableDefaultUI: true, */
//     mapTypeId: google.maps.MapTypeId.ROADMAP
//   });
  
  
//   navigator.geolocation.getCurrentPosition(function(position) {
        
//         var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        
// /*
//         var infowindow = new google.maps.InfoWindow({
//             map: map,
//             position: geolocate,
//             content: 'Location pinned from HTML5 Geolocation!'
//         });
// */
        
//         map.setCenter(geolocate);
        
//     });
    
    
    
    
    // Places autocomplete
    // $('form ul').on('focus', '[places-autocomplete]', function() {
    //     input = this;
    //     found = false;
    
    //     $(input).typeahead({
    //         source: function(query, process) {
    //             GooglePlacesService.getPlacePredictions({input: query, types: ['establishment'] }, function(predictions, status) {
    //             if (status == google.maps.places.PlacesServiceStatus.OK) {
    //                 found = true;
    //                 process($.map(predictions, function(prediction) {
    //                 return prediction.description;
    //               }));
    //             } else {
    //                 found = false;
    //                 process(new Array());
    //             }
    //         });
    //         },
    //         matcher: function(item) {
    //             return true;
    //         },
    //         updater: function (item) {
    //             geocodeInputPlace(item, input);
    //         }
    //     });
    // });
    
    // function geocodeInputPlace(item, input) {
    //     GooglePlacesService.getPlacePredictions({ input: item, types: ['establishment'] }, function(predictions, status) {
    //         $(input).val(predictions[0].terms[0].value);
    //     });
    //     var geocoder = new google.maps.Geocoder();
    //     geocoder.geocode({ address: item }, function(results, status) {
    //         if (status == google.maps.GeocoderStatus.OK) {
    //         $(input).closest('.object').find('input.address-autocomplete').val(results[0].formatted_address);
    //         $(input).closest('.object').find('input.geocoordinates').val(results[0].geometry.location.jb + ',' + results[0].geometry.location.kb);
    //             map.setCenter(results[0].geometry.location);
    //             map.setZoom(18);
    //             var marker = new google.maps.Marker({
    //                 map: map,
    //                 position: results[0].geometry.location
    //             });
    //         }
    //     });
    // }
     
//     $('form ul').on('focus', 'input[address-autocomplete]', function() {
//         input = this;
//         found = false;
        
//         $(input).keypress(function(event){
//         if (found == false) {
//         if (event.keyCode === 13){ 
//             geocodeInput($(input).val(), input);
//             return false; 
//         }
//       }
//     });
    
//     $(input).on('blur', function() {
//         if (found == false) {
//         geocodeInput($(input).val(), input);
//       }
//     });
    
//         $(input).typeahead({
//           source: function(query, process) {
//             GooglePlacesService.getPlacePredictions({ input: query }, function(predictions, status) {
//               if (status == google.maps.places.PlacesServiceStatus.OK) {
//                 found = true;
//                 process($.map(predictions, function(prediction) {
//                   return prediction.description;
//                 }));
//               } else {
//                 found = false;
//                 process(new Array());
//               }
//             });
//           },
//           matcher: function(item) {
//               return true;
//             },
//           updater: function (item) {
//         geocodeInput(item, input);
//           }
//         });
//     });
    
    // function geocodeInput(item, input) {
    //     var geocoder = new google.maps.Geocoder();
    //     geocoder.geocode({ address: item }, function(results, status) {
    //         if (status == google.maps.GeocoderStatus.OK) {
    //         $(input).val(results[0].formatted_address);
    //         $(input).closest('.object').find('input.geocoordinates').val(results[0].geometry.location.jb + ',' + results[0].geometry.location.kb);
    //             map.setCenter(results[0].geometry.location);
    //             map.setZoom(18);
    //             var marker = new google.maps.Marker({
    //                 map: map,
    //                 position: results[0].geometry.location
    //             });
    //         }
    //     });
    // }



    /* TINY-MCE */

    if (typeof tinymce == undefined) {
        tinymce.baseURL = '/lib/tinymce';
        tinymce.suffix = '.min';
        tinymce.init({
            selector: 'textarea.tinymce',
            language: culture,
            plugins: [
                "paste"
            ],
            menubar: false,
            toolbar: "undo redo | bold italic",
            statusbar: false,
            height: 150
        });
        tinymce.init({
            selector: 'textarea.tinymce-advanced',
            language: culture,
            plugins: [
                "paste"
            ],
            menubar: false,
            height: 300
        });
    }



    /* TAGS */

    $('[select2]').select2();
    $('body').on('protagonist-added', '.protagonists_user', function(event) {
        $(event.currentTarget).find('[select2]').select2();
    });
});