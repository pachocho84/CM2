/**
* @preserve Copyright 2012 Twitter, Inc.
* @license http://www.apache.org/licenses/LICENSE-2.0.txt
*/
var Hogan={};(function(a,b){function i(a){return String(a===null||a===undefined?"":a)}function j(a){return a=i(a),h.test(a)?a.replace(c,"&amp;").replace(d,"&lt;").replace(e,"&gt;").replace(f,"&#39;").replace(g,"&quot;"):a}a.Template=function(a,c,d,e){this.r=a||this.r,this.c=d,this.options=e,this.text=c||"",this.buf=b?[]:""},a.Template.prototype={r:function(a,b,c){return""},v:j,t:i,render:function(b,c,d){return this.ri([b],c||{},d)},ri:function(a,b,c){return this.r(a,b,c)},rp:function(a,b,c,d){var e=c[a];return e?(this.c&&typeof e=="string"&&(e=this.c.compile(e,this.options)),e.ri(b,c,d)):""},rs:function(a,b,c){var d=a[a.length-1];if(!k(d)){c(a,b,this);return}for(var e=0;e<d.length;e++)a.push(d[e]),c(a,b,this),a.pop()},s:function(a,b,c,d,e,f,g){var h;return k(a)&&a.length===0?!1:(typeof a=="function"&&(a=this.ls(a,b,c,d,e,f,g)),h=a===""||!!a,!d&&h&&b&&b.push(typeof a=="object"?a:b[b.length-1]),h)},d:function(a,b,c,d){var e=a.split("."),f=this.f(e[0],b,c,d),g=null;if(a==="."&&k(b[b.length-2]))return b[b.length-1];for(var h=1;h<e.length;h++)f&&typeof f=="object"&&e[h]in f?(g=f,f=f[e[h]]):f="";return d&&!f?!1:(!d&&typeof f=="function"&&(b.push(g),f=this.lv(f,b,c),b.pop()),f)},f:function(a,b,c,d){var e=!1,f=null,g=!1;for(var h=b.length-1;h>=0;h--){f=b[h];if(f&&typeof f=="object"&&a in f){e=f[a],g=!0;break}}return g?(!d&&typeof e=="function"&&(e=this.lv(e,b,c)),e):d?!1:""},ho:function(a,b,c,d,e){var f=this.c,g=this.options;g.delimiters=e;var d=a.call(b,d);return d=d==null?String(d):d.toString(),this.b(f.compile(d,g).render(b,c)),!1},b:b?function(a){this.buf.push(a)}:function(a){this.buf+=a},fl:b?function(){var a=this.buf.join("");return this.buf=[],a}:function(){var a=this.buf;return this.buf="",a},ls:function(a,b,c,d,e,f,g){var h=b[b.length-1],i=null;if(!d&&this.c&&a.length>0)return this.ho(a,h,c,this.text.substring(e,f),g);i=a.call(h);if(typeof i=="function"){if(d)return!0;if(this.c)return this.ho(i,h,c,this.text.substring(e,f),g)}return i},lv:function(a,b,c){var d=b[b.length-1],e=a.call(d);if(typeof e=="function"){e=i(e.call(d));if(this.c&&~e.indexOf("{{"))return this.c.compile(e,this.options).render(d,c)}return i(e)}};var c=/&/g,d=/</g,e=/>/g,f=/\'/g,g=/\"/g,h=/[&<>\"\']/,k=Array.isArray||function(a){return Object.prototype.toString.call(a)==="[object Array]"}})(typeof exports!="undefined"?exports:Hogan),function(a){function h(a){a.n.substr(a.n.length-1)==="}"&&(a.n=a.n.substring(0,a.n.length-1))}function i(a){return a.trim?a.trim():a.replace(/^\s*|\s*$/g,"")}function j(a,b,c){if(b.charAt(c)!=a.charAt(0))return!1;for(var d=1,e=a.length;d<e;d++)if(b.charAt(c+d)!=a.charAt(d))return!1;return!0}function k(a,b,c,d){var e=[],f=null,g=null;while(a.length>0){g=a.shift();if(g.tag=="#"||g.tag=="^"||l(g,d))c.push(g),g.nodes=k(a,g.tag,c,d),e.push(g);else{if(g.tag=="/"){if(c.length===0)throw new Error("Closing tag without opener: /"+g.n);f=c.pop();if(g.n!=f.n&&!m(g.n,f.n,d))throw new Error("Nesting error: "+f.n+" vs. "+g.n);return f.end=g.i,e}e.push(g)}}if(c.length>0)throw new Error("missing closing tag: "+c.pop().n);return e}function l(a,b){for(var c=0,d=b.length;c<d;c++)if(b[c].o==a.n)return a.tag="#",!0}function m(a,b,c){for(var d=0,e=c.length;d<e;d++)if(c[d].c==a&&c[d].o==b)return!0}function n(a){return a.replace(f,"\\\\").replace(c,'\\"').replace(d,"\\n").replace(e,"\\r")}function o(a){return~a.indexOf(".")?"d":"f"}function p(a){var b="";for(var c=0,d=a.length;c<d;c++){var e=a[c].tag;e=="#"?b+=q(a[c].nodes,a[c].n,o(a[c].n),a[c].i,a[c].end,a[c].otag+" "+a[c].ctag):e=="^"?b+=r(a[c].nodes,a[c].n,o(a[c].n)):e=="<"||e==">"?b+=s(a[c]):e=="{"||e=="&"?b+=t(a[c].n,o(a[c].n)):e=="\n"?b+=v('"\\n"'+(a.length-1==c?"":" + i")):e=="_v"?b+=u(a[c].n,o(a[c].n)):e===undefined&&(b+=v('"'+n(a[c])+'"'))}return b}function q(a,b,c,d,e,f){return"if(_.s(_."+c+'("'+n(b)+'",c,p,1),'+"c,p,0,"+d+","+e+',"'+f+'")){'+"_.rs(c,p,"+"function(c,p,_){"+p(a)+"});c.pop();}"}function r(a,b,c){return"if(!_.s(_."+c+'("'+n(b)+'",c,p,1),c,p,1,0,0,"")){'+p(a)+"};"}function s(a){return'_.b(_.rp("'+n(a.n)+'",c,p,"'+(a.indent||"")+'"));'}function t(a,b){return"_.b(_.t(_."+b+'("'+n(a)+'",c,p,0)));'}function u(a,b){return"_.b(_.v(_."+b+'("'+n(a)+'",c,p,0)));'}function v(a){return"_.b("+a+");"}var b=/\S/,c=/\"/g,d=/\n/g,e=/\r/g,f=/\\/g,g={"#":1,"^":2,"/":3,"!":4,">":5,"<":6,"=":7,_v:8,"{":9,"&":10};a.scan=function(c,d){function w(){p.length>0&&(q.push(new String(p)),p="")}function x(){var a=!0;for(var c=t;c<q.length;c++){a=q[c].tag&&g[q[c].tag]<g._v||!q[c].tag&&q[c].match(b)===null;if(!a)return!1}return a}function y(a,b){w();if(a&&x())for(var c=t,d;c<q.length;c++)q[c].tag||((d=q[c+1])&&d.tag==">"&&(d.indent=q[c].toString()),q.splice(c,1));else b||q.push({tag:"\n"});r=!1,t=q.length}function z(a,b){var c="="+v,d=a.indexOf(c,b),e=i(a.substring(a.indexOf("=",b)+1,d)).split(" ");return u=e[0],v=e[1],d+c.length-1}var e=c.length,f=0,k=1,l=2,m=f,n=null,o=null,p="",q=[],r=!1,s=0,t=0,u="{{",v="}}";d&&(d=d.split(" "),u=d[0],v=d[1]);for(s=0;s<e;s++)m==f?j(u,c,s)?(--s,w(),m=k):c.charAt(s)=="\n"?y(r):p+=c.charAt(s):m==k?(s+=u.length-1,o=g[c.charAt(s+1)],n=o?c.charAt(s+1):"_v",n=="="?(s=z(c,s),m=f):(o&&s++,m=l),r=s):j(v,c,s)?(q.push({tag:n,n:i(p),otag:u,ctag:v,i:n=="/"?r-v.length:s+u.length}),p="",s+=v.length-1,m=f,n=="{"&&(v=="}}"?s++:h(q[q.length-1]))):p+=c.charAt(s);return y(r,!0),q},a.generate=function(b,c,d){var e='var _=this;_.b(i=i||"");'+p(b)+"return _.fl();";return d.asString?"function(c,p,i){"+e+";}":new a.Template(new Function("c","p","i",e),c,a,d)},a.parse=function(a,b,c){return c=c||{},k(a,"",[],c.sectionTags||[])},a.cache={},a.compile=function(a,b){b=b||{};var c=a+"||"+!!b.asString,d=this.cache[c];return d?d:(d=this.generate(this.parse(this.scan(a,b.delimiters),a,b),a,b),this.cache[c]=d)}}(typeof exports!="undefined"?exports:Hogan)
function addFormDeleteLink($target, text) {
    var $removeFormA = $('<div class="panel-footer"><a class="btn btn-default" href="#"><i class="glyphicon glyphicon-minus"></i> ' + text + '</a></div>');
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
    $target.children('div.panel-body').slice(1).each(function() {
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
        $target.find('.panel:last').after($newForm);

        addFormDeleteLink($newForm, text);

        $(e.currentTarget).trigger("collection-added");
    });
}

jQuery(document).ready(function() {
    uploadCollection($('#cm_cmbundle_event_eventDates'), '.add_date_link', $('.add_date_link').attr('delete_date-text'));
    uploadCollection($('#cm_cmbundle_disc_discTracks'), '.add_track_link', $('.add_track_link').attr('delete_track-text'));
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
    function initialize(index) {
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
            marker.setIcon(/** @type {google.maps.Icon} */({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
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
            infowindow.open(map, marker);
        });
    }
    
    $('[gmap-canvas]').each(function(i) {
        google.maps.event.addDomListener(window, 'load', initialize(i));
    });
    $(document).on('collection-added', function(event) {
        google.maps.event.addDomListener(window, 'load', initialize(-1));
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
});