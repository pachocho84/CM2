$(function() {
    /* PROTAGONIST */

    // var form = $('form');
    // var element = $('select.typeahead');
    // var select = $('<div />').append(element.clone()).html();
    // var hashOptions = new Array;
    // var options = new Array;
    // $($(select).html()).each(function () {
    //     hashOptions[$(this).val()] = $(this).html();
    //     options.push($(this).html());
    // });
    // // substitute multiselect with text field
    // $('select.typeahead').replaceWith('<input type="text" class="typeahead-select_sub ' + $(select).attr('class') + '"" ></input>');

    // $('input.typeahead-select_sub').typeahead({
    //     name: 'tags',
    //     local: options,
    //     updater: function(item) {
    //         console.log('ok');
    //         return '';
    //     }
    // });
    // form.submit(function () {

    // });

    var collection = $('.protagonist_typeahead .collection-items');
    collection.on('typeahead:autocompleted typeahead:selected', function (event) {
        console.log('ok');
        console.log(event.data);
    });
    collection.append('<div class="form-group"><label class="control-label">Add new protagonists</label><input type="text" id="protagonists_finder" class="form-control" /></div>');
    $('#protagonists_finder').typeahead({
        name: 'protagonists',
        remote: typeaheadHintRoute+'?query=%QUERY',
        filter: function (response) {
            console.log(response);
            return response;
        }
    });

// 	$('.protagonists_user:last').attr('protagonist_new_id') != undefined ? protagonist_new_id = $('.protagonists_user:last').attr('protagonist_new_id') : protagonist_new_id = 1;
	
// 	// Add
// 	$('#protagonists_finder').typeahead({
// 		minLength: 		2,
//    	source: 			function (query, process) {
//       return $.get(script + '/utenti/autocomplete_users', { query: query, exclusion: $('.protagonists_user, .protagonists_group_user').map(function() { return $(this).attr('user_id'); }).get().join(',') }, function (data) {
// 				users = data;
//         return process(
// 					$.map(data, function(user) {
// 						item = new String(user.FirstName + ' ' + user.LastName);
// 						item.data = user;
// 						return item;
// 					})
// 				);
//       });
//    	},
// 		highlighter: function(item) {
//       return '<img src="' + item.data.Img + '" class="pull-left" /> ' + item;
//    	},
// 		item: 				'<li class="media"><a href="' + script + '"></a></li>',
// 		items: 				8,
// 		updater: 			function(item) {
// 			$.get(script + '/protagonist/add', { user_id: users[item].Id, protagonist_new_id: parseInt(protagonist_new_id) + 1, entity_type: $('#protagonists').attr('object') }, function(data) {
// 				$('#protagonists_users').append(data);
// 				protagonist_new_id ++;
// 			});
// 			return '';
// 		}
// 	});
	
// 	// Group
// 	$('body').on('change', 'select[name*="group_id"]', function(event) {
// 		// Mark all protagonists from previous selected group for delete
// 		$('.protagonists_user[group_id]').each(function (index, element) {
// 			$('input[name$="[Protagonist][' + $(element).attr('protagonist_key') + '][delete]"]').attr('value', 1);
// 		});
// 		if ($(event.target).val() != '') {
// 			// Remove all protagonists from previous selected group
// 			$('.protagonists_user[group_id]').remove();
// 			// Fetch all protagonists from current selected group
// 			$.getJSON(script + '/protagonist/addGroup', { group_id: $(event.target).val(), protagonist_new_id: parseInt(protagonist_new_id) + 1, entity_type: $('#protagonists').attr('object'), exclusion: $('.protagonists_user, .protagonists_group_user').map(function() { return $(this).attr('user_id'); }).get().join(',') }, function(data) {
// 				$('#protagonists_users').append(data.protagonists);
// 				// Update all protagonists that are already added but also part of the current selected group with the group_id
// 				for (userId in data.usersExcluded) {
// 					$('.protagonists_user[user_id=' + data.usersExcluded[userId] + ']').attr('group_id', $(event.target).val());
// 				}
// 				protagonist_new_id ++;
// 				fix_new_protagonists_ids();
// 			}).error(function(jqXHR, textStatus, errorThrown) { console.log(textStatus + ': ' + errorThrown); });
// 		} else {
// 			$('.protagonists_user[group_id]').fadeOut('normal', function() { 
// 				$(this).remove(); 
// 				fix_new_protagonists_ids();
// 			});	
// 		}
// 	});
	
// 	// Delete
// 	$('body').on('click', '.protagonists_user .close', function(event) {
// 		event.preventDefault();	
// 		protagonist_key = $(this).parents('.protagonists_user').attr('protagonist_key');
// 		if ($(this).parent().attr('protagonist_new_id') == 0) {	
// 			$(this).popover({ title: $(this).attr('confirmation-title'), content: $(this).attr('confirmation-body'), placement: 'left', html: true }).popover('show');
// 			$(this).next('.popover').find('.btn-primary').bind('click', function(event) { 					
// 				event.preventDefault();
// 				console.log(protagonist_key);
// 				protagonist_delete(event, protagonist_key);	
// 			});
// 			$(this).next('.popover').find('.btn-close').bind('click', function(event) { 
// 				event.preventDefault(); 
// 				$(this).parents('.popover').prev().popover('destroy'); 
// 			});
// 		} else {
// 			$(this).parent().fadeOut('normal', function() { 
// 				$('input[name*="' + $(this).attr('protagonist_key') + '"]').remove();
// 				$(this).remove(); 
// 				fix_new_protagonists_ids();
// 			});
// 		}
// 	});
	
// 	function protagonist_delete(event, protagonist_key) {
// 		$.get(event.target.href, function(data) {	
// 			$('.protagonists_user[protagonist_key="' + protagonist_key + '"]').fadeOut('normal', function() { $(this).remove(); });
// 			$('input[name*="[Protagonist][' + protagonist_key + ']"]').remove();
// 			$('.protagonists_user').each(function(index,element) {
// 				if (parseInt($(element).attr('protagonist_key')) > protagonist_key) {
// 					$(element).attr('protagonist_key', $(element).attr('protagonist_key') - 1);
// 				}
// 			});
// 			$('*[name*="Protagonist"]:not([name*="newProtagonist"])').each(function(index, element) {
// 				$(element).attr('name', $(element).attr('name').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
// 				$(element).attr('id', $(element).attr('id').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
// 			});
// 			$('label[for*="Protagonist"]:not([for*="newProtagonist"])').each(function(index, element) {
// 				$(element).attr('for', $(element).attr('for').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
// 			});
// 		}).error(function(jqXHR, textStatus, errorThrown) { 
// 			$(event.target).closest('.btn').addClass('btn-danger').text(textStatus + ': ' + errorThrown ); 
// 			console.log(textStatus + ': ' + errorThrown); 
// 		});	
// 	}	
	
// 	function fix_new_protagonists_ids() {
// 		$('form > input[name*="newProtagonist"]').remove();
// 		$('.protagonists_user[protagonist_new_id!=0]').each(function(index, element) {
// 			$(element).attr('protagonist_key', $(element).attr('protagonist_key').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
// 			$(element).find('*[name*="newProtagonist"]').each(function(sub_index, element) {
// 				$(element).attr('name', $(element).attr('name').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
// 				$(element).attr('id', $(element).attr('id').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
// 			});
// 			$(element).attr('protagonist_new_id', index + 1);
// 			protagonist_new_id = index + 1;
// 		});
// 	}
	
	
	
// 	/* DISCS */
// 	// Add track
// 	new_track_id = 1;
// 	$('form').on('click', '.track-add', function(event) {
// 		event.preventDefault();
// 		$.get(event.currentTarget.href, { new_track_id: new_track_id }, function(data) {
// 			$(event.target).closest('table').append(data);
// 			new_track_id++;
// 		});
// 	});
// 	// Delete track
// 	$('#tracks').on('click', '.close', function(event) {
// /* 		console.log($('#tracks tr').index($(event.target).closest('tr'))); */
// 		event.preventDefault();
// 		$('input[name="entity[DiscTrack][' + $('#tracks tr').index($(event.target).closest('tr')) + '][delete]"]').val('on');
// 		$(event.target).closest('tr').fadeOut();
// 	});
	
	
	
// 	/* AUTOCOMPLETE */
	
// 	// City autocomplete
// 	$('form').on('click', '.autocomplete-city', function() {
// 		$(this).typeahead({
// 			minLength: 		3,
// 	   	source: 			function (query, process) {
// 	      return $.getJSON('http://ws.geonames.org/searchJSON', { featureClass: 'P', style: 'full', username: 'circuitomusica', maxRows: 8, lang: culture, name_startsWith: query, type: 'json' }, function (data) {
// 					cities = new Array(); // CREDO NON SERVA PIU'
// 					return process(
// 						$.map(data.geonames, function(city) {
// 							return city.name + (city.adminName1 ? ", " + city.adminName1 : "") + ", " + city.countryName;
// 						})
// 					);
// 	      });
// 	   	}
// 		});
// 	});
	
	
// 	var GooglePlacesService = new google.maps.places.AutocompleteService();
	
//   var burnsvilleMN = new google.maps.LatLng(44.797916,-93.278046);
//   // Creating a map
//   var map = new google.maps.Map($('#map')[0], {
//     zoom: 15,
// /*     center: burnsvilleMN, */
// /*     disableDefaultUI: true, */
//     mapTypeId: google.maps.MapTypeId.ROADMAP
//   });
  
  
//   navigator.geolocation.getCurrentPosition(function(position) {
        
// 	    var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
	    
// /*
// 	    var infowindow = new google.maps.InfoWindow({
// 	        map: map,
// 	        position: geolocate,
// 	        content: 'Location pinned from HTML5 Geolocation!'
// 	    });
// */
	    
// 	    map.setCenter(geolocate);
	    
// 	});
    
    
    
	
// 	// Places autocomplete
// 	$('form ul').on('focus', 'input.places-autocomplete', function() {
// 		input = this;
// 		found = false;
    
// 		$(input).typeahead({
// 		  source: function(query, process) {
// 		    GooglePlacesService.getPlacePredictions({ input: query, types: ['establishment'] }, function(predictions, status) {
// 		      if (status == google.maps.places.PlacesServiceStatus.OK) {
// 		      	found = true;
// 		        process($.map(predictions, function(prediction) {
// 		          return prediction.description;
// 		        }));
// 		      } else {
// 		      	found = false;
// 		      	process(new Array());
// 		      }
// 		    });
// 		  },
// 		  matcher: function(item) {
// 			  return true;
// 			},
// 		  updater: function (item) {
//       	geocodeInputPlace(item, input);
// 		  }
// 		});
// 	});
	
// 	function geocodeInputPlace(item, input) {
// 		GooglePlacesService.getPlacePredictions({ input: item, types: ['establishment'] }, function(predictions, status) {
// 			$(input).val(predictions[0].terms[0].value);
// 		});
// 		var geocoder = new google.maps.Geocoder();
//     geocoder.geocode({ address: item }, function(results, status) {
//     	if (status == google.maps.GeocoderStatus.OK) {
//         $(input).closest('.object').find('input.address-autocomplete').val(results[0].formatted_address);
//         $(input).closest('.object').find('input.geocoordinates').val(results[0].geometry.location.jb + ',' + results[0].geometry.location.kb);
// 		    map.setCenter(results[0].geometry.location);
// 		    map.setZoom(18);
// 		    var marker = new google.maps.Marker({
// 			    map: map,
// 			    position: results[0].geometry.location
// 			  });
//       }
//     });
// 	}
	 
// 	// Address autocomplete
// 	$('form ul').on('focus', 'input.address-autocomplete', function() {
// 		input = this;
// 		found = false;
		
// 		$(input).keypress(function(event){
//     	if (found == false) {
//         if (event.keyCode === 13){ 
//         	geocodeInput($(input).val(), input);
//         	return false; 
//         }
//       }
//     });
    
//     $(input).on('blur', function() {
//     	if (found == false) {
//         geocodeInput($(input).val(), input);
//       }
//     });
    
// 		$(input).typeahead({
// 		  source: function(query, process) {
// 		    GooglePlacesService.getPlacePredictions({ input: query }, function(predictions, status) {
// 		      if (status == google.maps.places.PlacesServiceStatus.OK) {
// 		      	found = true;
// 		        process($.map(predictions, function(prediction) {
// 		          return prediction.description;
// 		        }));
// 		      } else {
// 		      	found = false;
// 		      	process(new Array());
// 		      }
// 		    });
// 		  },
// 		  matcher: function(item) {
// 			  return true;
// 			},
// 		  updater: function (item) {
//       	geocodeInput(item, input);
// 		  }
// 		});
// 	});
	
// 	function geocodeInput(item, input) {
// 		var geocoder = new google.maps.Geocoder();
//     geocoder.geocode({ address: item }, function(results, status) {
//     	if (status == google.maps.GeocoderStatus.OK) {
//         $(input).val(results[0].formatted_address);
//         $(input).closest('.object').find('input.geocoordinates').val(results[0].geometry.location.jb + ',' + results[0].geometry.location.kb);
// 		    map.setCenter(results[0].geometry.location);
// 		    map.setZoom(18);
// 		    var marker = new google.maps.Marker({
// 			    map: map,
// 			    position: results[0].geometry.location
// 			  });
//       }
//     });
// 	}
	
	
	
// 	/* EMBED RELATION */
// 	// Add
// 	$('form .item-add').on('click', function(event) {
// 		event.preventDefault();
// 		console.log($('input').filter(function() { return this.id.match(new RegExp($(event.target).attr('id') + '\\d{1,2}_id')); }));
// 		$.get(event.currentTarget.href, { new_id: $('input').filter(function() { return this.id.match(new RegExp($(event.target).attr('id') + '\\d{1,2}_id')); }).size() + 1 }, function(data) {
// 			$(event.target).closest('.objects').find('ul').append(data);
// 		});
// 	});
// 	// Remove
// 	$('form').on('click', '.item-remove', function(event) {
// 		event.preventDefault();
// 		$('input[name="' + $(event.currentTarget).attr('rel') + '"]').val('on');
// 		$(event.target).closest('li.object').fadeOut();
// 	});
	 
});