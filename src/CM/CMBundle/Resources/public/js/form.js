function initCollectionForm(type) {
    $(document).on('keydown, change', 'form .' + type + '-form:last', function(event) {
        var $form = $(event.currentTarget);
        var $collection = $form.closest('[data-prototype]');
        var index = $collection.find('.' + type + '-form :input').length;
        var $newForm = $($collection.data('prototype').replace(/__name__label__/g, index).replace(/__name__/g, index));

        $form.find('[remove-link]').parent().removeClass('hidden');
        $form.after($newForm);

        $(event.currentTarget).trigger('collection-added', $newForm);
    });
    $(document).on('click', '[remove-link]', function(event) {
        event.preventDefault();

        $(event.currentTarget).closest('.' + type + '-form').remove();
    });
    $(document).on('submit', 'form', function(event) {
        if ($('.' + type + '-form').length > 1) {
            $(event.currentTarget).find('.' + type + '-form:last [name]').attr('name', '');
        }
    });
}

initDatetimepicker = function(elem) {
    $(elem).datetimepicker({
        language: culture,
        format: $(elem).attr('datetimepicker-format'),
        autoclose: true,
        todayBtn: false,
        todayHighlight: true,
        pickerPosition: "bottom-left",
        linkField: $(elem).siblings('input[type="hidden"]').attr('id'),
        linkFormat: "yyyy-mm-dd hh:ii"
    });
}

initDatepicker = function(elem) {
    $(elem).datetimepicker({
        viewSelect: 'month',
        language: culture,
        format: $(elem).attr('datepicker-format'),
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        pickerPosition: "bottom-left",
        linkField: $(elem).siblings('input[type="hidden"]').attr('id'),
        linkFormat: "yyyy-mm-dd"
    });
}

function initializePlaces(index) {
    canvas = $('[gmap-canvas]').get(index);
    input = $(canvas).closest('.date-form').find('[places-autocomplete]').get(0);

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

    $(input).keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
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
        $(map.getDiv()).closest('.date-form').find('[places-autocomplete]').val(place.name);
        var $address = $(map.getDiv()).closest('.date-form').find('[address-autocomplete]');
        if ($address.val() == '' || $address.attr('address-autocomplete') != 'set') {
            $address.val(address);
            $(map.getDiv()).closest('.date-form').find('[address-latitude]').val(place.geometry.location.lat());
            $(map.getDiv()).closest('.date-form').find('[address-longitude]').val(place.geometry.location.lng());
        }
        infowindow.open(map, marker);
    });
}

function initAddress(container) {
    $.each($(container).find('[address-autocomplete]'), function(i, elem) {
        $(elem).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
            }
        });
        $(elem).autocomplete({
            minLength: 1,
            source: function(request, response) {
                $.ajax('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&culture=' + culture + '&address=' + request.term, {
                    success: function(data) {
                        response($.map(data.results, function(address) {
                            return {label: address.formatted_address, value: address.formatted_address, coords: address.geometry.location}
                        }));
                    }
                });
            },
            focus: function() {
              // prevent value inserted on focus
              return false;
            },
            select: function(event, ui) {
                console.log(ui.item);
                $(elem).attr('address-autocomplete', 'set');
                $(elem).closest('.date-form').find('[address-latitude]').val(ui.item.coords.lat);
                $(elem).closest('.date-form').find('[address-longitude]').val(ui.item.coords.lng);
            }
        });
        $(elem).on('change', function(event) {
            if ($(elem).val() == '') {
                $(elem).attr('address-autocomplete', '');
                $(elem).closest('.date-form').find('[address-latitude]').val('');
                $(elem).closest('.date-form').find('[address-longitude]').val('');
            }
        });
        // $(elem).typeahead({
        //     name: 'address',
        //     // minLength: 3,
        //     valueKey: 'val',
        //     template: '<div>{{ val }}</div>',
        //     engine: Handlebars,
        //     remote: {
        //         url: 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&culture=' + culture + '&address=%QUERY',
        //         replace: function (url, uriEncodedQuery) {
        //             return url.replace('%QUERY', uriEncodedQuery);
        //         },
        //         filter: function(data) {
        //             if (data.status == 'OK') {
        //                 return $.map(data.results, function(address) {
        //                     value = new Object();
        //                     value.val = address.formatted_address;
        //                     value.coords = address.geometry.location.lat + ',' + address.geometry.location.lng;
        //                     return value;
        //                 });
        //             }
        //         }
        //     }
        // });
    });
}

function initTags($protagonist) {
    $protagonist.find('input[tags]').each(function(i, elem) {
        var source = $.map($(elem).closest('.row').find('select[tags] option:not([disabled])'), function(e) { return {label: $(e).html(), value: $(e).attr('value')}; } );
        $(elem).tokenfield({
            autocomplete: {
                source: source,
                focus: function() {
                  // prevent value inserted on focus
                  return false;
                },
                select: function(event, ui) {
                    event.preventDefault();

                    $('.ui-autocomplete-input').autocomplete('close');
                }
            },
            showAutocompleteOnFocus: true
        });

        var $input = $(elem).siblings('.token-input.ui-autocomplete-input');
        $(document).on('tokenfield:createtoken', function(event) {
            $input.attr('placeholder', '');
        }).on('tokenfield:removetoken', function(event) {
            if ($(elem).tokenfield('getTokens', 'active').length == 0 && $input.attr('placeholder') == '') {
                $input.attr('placeholder', $(elem).attr('placeholder'));
            }
        });

        $(elem).closest('.row').find('select[tags] option[selected]').each(function(i, e) {
            $(elem).tokenfield('createToken', {label: $(e).html(), value: $(e).attr('value')});
        }).filter('placeholder').remove();
    });
}

function imagePosition($img, $target) {
    var $box = $img.closest('.image_box');
    $box.css('cursor', 'move');

    $img.draggable({
        scroll: false,
        drag: function(event, ui) {
            if (ui.position.top > 0) {
                ui.position.top = 0;
            } else if (ui.position.top < $box.outerHeight() - $img.outerHeight()) {
                ui.position.top = $box.outerHeight() - $img.outerHeight();
            }
            if (ui.position.left > 0) {
                ui.position.left = 0;
            } else if (ui.position.left < $box.width() - $img.outerWidth()) {
                ui.position.left = $box.width() - $img.outerWidth();
            } 
        },
        stop: function(event, ui) {
            offsetX = - $img.position().left;
            offsetY = - $img.position().top;
/*             console.log(offsetX, offsetY, $box.width(), Math.abs(100 * Math.max(offsetX, offsetY) / $box.width()).toFixed(2)); */
            $target.val(Math.abs(100 * Math.max(offsetX, offsetY) / $box.width()).toFixed(2));
        }
    });
}

$(function() {
    /* PROTAGONIST */
    if($('#protagonists_finder').length > 0) {
        var protagonist_new_id = parseInt(1 + $('.protagonists_user:last').attr('protagonist_new_id')) + 5;
        var collection = $('.protagonist_typeahead').children('.collection-items');
        $('#protagonists_finder').on('keydown', function(event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(event.currentTarget).data('ui-autocomplete').menu.active) {
                event.preventDefault();
            }
        }).autocomplete({
            minLength: 1,
            source: function(request, response) {
                var url = typeaheadHintRoute + '?query=' + request.term + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user'); }).get().join(',');
                $.ajax(url, {
                    success: function(data) {
                        console.log(data);
                        response(data);
                    }
                });
            },
            focus: function() {
              // prevent value inserted on focus
              return false;
            },
            select: function(event, ui) {
                event.preventDefault();

                $('.ui-autocomplete-input').autocomplete('close');

                $('#message_recipients').val($('#message_recipients').val() + ',' + ui.item.value);
            }
        }).data('ui-autocomplete')._renderItem = function(ul, item) {
            return $('<li><a>' + item.view + '</a></li>').appendTo(ul);
        };
    }
    // $('#protagonists_finder').typeahead({
    //     name: 'protagonists',
    //     valueKey: 'fullname',
    //     template: '{{{ view }}}',
    //     engine: Handlebars,
    //     remote:  {
    //         url: typeaheadHintRoute + '?query=%QUERY',
    //         replace: function (url, uriEncodedQuery) {
    //             return url.replace('%QUERY', uriEncodedQuery) + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user_id'); }).get().join(',');
    //         },
    //         cache: false
    //     },
    // });
    $(document).on('autocompleteselect', '.protagonist_typeahead', function (event, ui) {
        protagonist_new_id += 1;
        var callback = null;
        if ($(event.currentTarget).is('[typeahead-callback]')) {
            callback = $(event.currentTarget).attr('typeahead-callback');
        } else {
            callback = $(event.currentTarget).find('li[typeahead-callback]').attr('typeahead-callback');
        }
        if (typeof callback === 'undefined') {
            // do nothing
        } else if (callback.substring(0, 1) == '$') {
            callback = callback.substring(1);
            window[callback.split('(')[0]](event.currentTarget, ui.item, callback.split('(').slice(1).join('(').slice(0, -1));
        } else {
            var url = callback.replace(/USER_ID/, ui.item.id).replace(/NEW_ID/, protagonist_new_id).replace(/ENTITY_TYPE/, $('#protagonists').attr('object'));

            $.get(url, function (data) {
                $('.protagonists_user:last').after(data);
                $('.protagonists_user:last').trigger('protagonist-added');
            });
        }
    });

    $(document).on('click', '.protagonists_remove', function (event) {
        event.preventDefault();
        var removeId = $(event.target).attr('id');
        if (parseInt(removeId.substring(removeId.lastIndexOf('_') + 1)) != 0) {
            $(this).closest('.protagonists_user').remove();
        }
    });
    // page
    $(document).on('change', '.protagonists_page', function (event) {
        event.preventDefault();
        var page = $(this).children('option:selected').attr('value');
        $('.protagonists_user[page_id]').each(function () {
            $(this).remove();
        });
        if (page != '') {
            $.get(script + '/protagonist/addPage?page_id=' + page + '&exclude=' + $('.protagonists_user').map(function() { return $(this).attr('user_id'); }).get().join(',') + '&protagonist_new_id=' + (parseInt($('.protagonists_user:last').attr('protagonist_new_id')) + 1), function (data) {
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



    /* COLLECTIONS */
    initCollectionForm('work');
    initCollectionForm('education');
    initCollectionForm('date');
    initCollectionForm('track');



    /* DATETIME PICKER & INPUT MASK */
    // datetime
    $('[datetimepicker-container]').each(function(i, elem) {
        initDatetimepicker(elem);
    });
    // date
    $('[datepicker-container]').each(function(i, elem) {
        initDatepicker(elem);
    });
    $(document).on('collection-added', '.work-form, .education-form, .date-form', function(event, elem) {
        $(elem).find('[datetimepicker-container]').each(function(i, elem) {
            initDatetimepicker(elem);
        });
        $(elem).find('[datepicker-container]').each(function(i, elem) {
            initDatepicker(elem);
        });
    });
    // current
    $(document).on('change', '.current-input', function(event) {
        var datetimepicker = $(event.currentTarget).closest('.' + type + '-form').find('.current-input-target [datepicker-container]').data('datetimepicker');
        var $button = $(event.currentTarget).closest('.' + type + '-form').find('.current-input-target [datepicker-container] .btn');
        if ($(event.currentTarget).is(':checked')) {
            $button.attr('disabled', 'disabled');
            var date = new Date();
            date = new Date(Date.UTC.apply(Date, [date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds(), 0]));
            datetimepicker.viewMode = datetimepicker.startViewMode;
            datetimepicker.showMode(0);
            datetimepicker._setDate(date);
            datetimepicker.fill();
        } else {
            datetimepicker.reset();
            $button.removettr('disabled');
        }
    });
    
    
    
    /* AUTOCOMPLETE */
    // Places autocomplete
    $('[gmap-canvas]').each(function(i) {
        google.maps.event.addDomListener(window, 'load', initializePlaces(i));
    });
    $(document).on('collection-added','.date-form', function(event) {
        google.maps.event.addDomListener(window, 'load', initializePlaces(-1));
    });
    $(document).on('keydown', '[gmap-canvas]', function(event) {
        if (event.which == 13) {
            event.preventDefault();
        }
    });

    // Address autocomplete
    initAddress($(document));
    $(document).on('collection-added','.date-form', function(event) {
        initAddress($(event.currentTarget));
    });
    $(document).on('typeahead:autocompleted typeahead:selected', '.event_date', function (event, datum) {
        $(event.currentTarget).find('[address-coordinates]').val(datum.coords);
    });

    // City autocomplete
    $('[autocomplete-city]').typeahead({
        name: 'cities',
        minLength: 3,
        template: '<div>{{ value }}</div>',
        engine: Handlebars,
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
    initTags($(document));
    $(document).on('protagonist-added', '.protagonists_user', function(event) {
        initTags($(event.currentTarget));
    });

    $(document).on('tokenfield:preparetoken', 'input[tags]', function(event) {
        $(event.currentTarget).closest('.row').find('select[tags] option[value="' + event.token.value + '"]').attr('selected', 'selected');

        if ($(event.currentTarget).tokenfield('getTokens').length == 0) {
            $(event.currentTarget).closest('.row').find('input.ui-autocomplete-input').attr('placeholder', '');
        }
    }).on('tokenfield:removetoken', 'input[tags]', function(event) {
        $(event.currentTarget).closest('.row').find('select[tags] option[value="' + event.token.value + '"]').removeAttr('selected');

        if ($(event.currentTarget).tokenfield('getTokens').length == 0) {
            $(event.currentTarget).closest('.row').find('input.ui-autocomplete-input').attr('placeholder', $(event.currentTarget).attr('placeholder'));
        }
    });

    $('.protagonists_user').each(function(i, elem) {
        initTags($(elem));
    });



    /* IMAGE POSITIONING */
    if ($('#profile-pic').length == 1) {
        imagePosition($('#profile-pic').find('img'), $('[img-offset-field]'));
    }
    if ($('#cover-pic').length == 1) {
        imagePosition($('#cover-pic').find('img'), $('[cover-img-offset-field]'));
    }



    /* IMAGE BOX */
    $.each($('[img-offset-field]'), function(i, elem) {
        imagePosition($(elem).closest('.form-group').find('.fileinput-new img'), $(elem));
    });
    $(document).on('change.bs.fileinput', '.fileinput', function(event) {
        if (event.namespace != 'bs.fileinput') return;

        var $image =  $(event.currentTarget).find('.fileinput-exists img');
        var $box = $image.parent();

        var width  = $box.width();
        var height = $box.height();

        // Ratio
        var imageRatio = $image.width() / $image.height();
        var boxRatio = width / height;
        var ratio = imageRatio / boxRatio;

        // Offset
        var imgStyle = [];
        if (ratio == 1) {
            $image.height('100%');
        } else if (ratio > 1) { // landscape
            $image.height('100%');
            $image.css('left', (-Math.abs((imageRatio / boxRatio - 1) / 2 * 100)) + '%');
        } else if (ratio < 1) { // portrait
            $image.width('100%');
            $image.css('top', (-Math.ceil(Math.min(width / imageRatio - height, width / imageRatio / 10))) + 'px');
        }

        imagePosition($image, $image.closest('.form-group').find('[img-offset-field]'));
    });
});