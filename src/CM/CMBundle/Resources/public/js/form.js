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



    /* DATETIME PICKER & INPUT MASK */
    // datetime
    initDatetimepicker = function(elem) {
        $(elem).datetimepicker({
            language: 'it',
            format: $(elem).attr('datetimepicker-format'),
            autoclose: true,
            todayBtn: true,
            todayHighlight: true,
            pickerPosition: "bottom-left",
            linkField: $(elem).siblings('input[type="hidden"]').attr('id'),
            linkFormat: "yyyy-mm-dd hh:ii"
        });
    }
    $('[datetimepicker-container]').each(function(i, elem) {
        initDatetimepicker(elem);
    });
    $('body').on('collection-added', '.add_date_link', function(event, elem) {
        $(elem).find('[datetimepicker-container]').each(function(i, elem) {
            initDatetimepicker(elem);
        });
    });
    // date
    initDatepicker = function(elem) {
        $(elem).datetimepicker({
            pickTime: false,
            language: 'it',
            format: $(elem).attr('datepicker-format'),
            autoclose: true,
            todayBtn: true,
            todayHighlight: true,
            pickerPosition: "bottom-left",
            linkField: $(elem).siblings('input[type="hidden"]').attr('id'),
            linkFormat: "yyyy-mm-dd"
        });
    }
    $('[datepicker-container]').each(function(i, elem) {
        initDatepicker(elem);
    });
    $('body').on('collection-added', '.add_date_link', function(event, elem) {
        $(elem).find('[datepicker-container]').each(function(i, elem) {
            initDatepicker(elem);
        });
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
            $(canvas).parent().parent().parent().find('[address-coordinates]').val(place.geometry.location.lat() + ',' + place.geometry.location.lng());
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
        engine: Handlebars,
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
    function initTags($protagonist) {
        $protagonist.find('input[tags]').each(function(i, elem) {
            var source = $.map($(elem).closest('.row').find('select[tags] option'), function(e) { return {label: $(e).html(), value: $(e).attr('value')}; } );
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

            $(elem).closest('.row').find('select[tags] option[selected]').each(function(i, e) {
                $(elem).tokenfield('createToken', {label: $(e).html(), value: $(e).attr('value')});
            }).filter('placeholder').remove();
        });
    }
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
    function imagePosition($img, $target) {
        var $box = $img.closest('.image_box');
        $box.css('cursor', 'move');

        $img.draggable({
            scroll: false,
            drag: function(event, ui) {
                if (ui.position.top > 0) {
                    ui.position.top = 0;
                } else if (ui.position.top < $box.height() - $img.outerHeight()) {
                    ui.position.top = $box.height() - $img.outerHeight();
                }
                if (ui.position.left > 0) {
                    ui.position.left = 0;
                } else if (ui.position.left < $box.width() - $img.outerWidth()) {
                    ui.position.left = $box.width() - $img.outerWidth();
                } 
            },
            stop: function(event, ui) {
                offsetX = Math.abs(100 * $img.position().left / $img.outerWidth()).toFixed(2);
                offsetY = Math.abs(100 * $img.position().top / $img.outerHeight()).toFixed(2);
                $target.val(Math.max(offsetX, offsetY));
            }
        });
    }
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