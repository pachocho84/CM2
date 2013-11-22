/**
* @preserve Copyright 2012 Twitter, Inc.
* @license http://www.apache.org/licenses/LICENSE-2.0.txt
*/
var Hogan={};(function(a,b){function i(a){return String(a===null||a===undefined?"":a)}function j(a){return a=i(a),h.test(a)?a.replace(c,"&amp;").replace(d,"&lt;").replace(e,"&gt;").replace(f,"&#39;").replace(g,"&quot;"):a}a.Template=function(a,c,d,e){this.r=a||this.r,this.c=d,this.options=e,this.text=c||"",this.buf=b?[]:""},a.Template.prototype={r:function(a,b,c){return""},v:j,t:i,render:function(b,c,d){return this.ri([b],c||{},d)},ri:function(a,b,c){return this.r(a,b,c)},rp:function(a,b,c,d){var e=c[a];return e?(this.c&&typeof e=="string"&&(e=this.c.compile(e,this.options)),e.ri(b,c,d)):""},rs:function(a,b,c){var d=a[a.length-1];if(!k(d)){c(a,b,this);return}for(var e=0;e<d.length;e++)a.push(d[e]),c(a,b,this),a.pop()},s:function(a,b,c,d,e,f,g){var h;return k(a)&&a.length===0?!1:(typeof a=="function"&&(a=this.ls(a,b,c,d,e,f,g)),h=a===""||!!a,!d&&h&&b&&b.push(typeof a=="object"?a:b[b.length-1]),h)},d:function(a,b,c,d){var e=a.split("."),f=this.f(e[0],b,c,d),g=null;if(a==="."&&k(b[b.length-2]))return b[b.length-1];for(var h=1;h<e.length;h++)f&&typeof f=="object"&&e[h]in f?(g=f,f=f[e[h]]):f="";return d&&!f?!1:(!d&&typeof f=="function"&&(b.push(g),f=this.lv(f,b,c),b.pop()),f)},f:function(a,b,c,d){var e=!1,f=null,g=!1;for(var h=b.length-1;h>=0;h--){f=b[h];if(f&&typeof f=="object"&&a in f){e=f[a],g=!0;break}}return g?(!d&&typeof e=="function"&&(e=this.lv(e,b,c)),e):d?!1:""},ho:function(a,b,c,d,e){var f=this.c,g=this.options;g.delimiters=e;var d=a.call(b,d);return d=d==null?String(d):d.toString(),this.b(f.compile(d,g).render(b,c)),!1},b:b?function(a){this.buf.push(a)}:function(a){this.buf+=a},fl:b?function(){var a=this.buf.join("");return this.buf=[],a}:function(){var a=this.buf;return this.buf="",a},ls:function(a,b,c,d,e,f,g){var h=b[b.length-1],i=null;if(!d&&this.c&&a.length>0)return this.ho(a,h,c,this.text.substring(e,f),g);i=a.call(h);if(typeof i=="function"){if(d)return!0;if(this.c)return this.ho(i,h,c,this.text.substring(e,f),g)}return i},lv:function(a,b,c){var d=b[b.length-1],e=a.call(d);if(typeof e=="function"){e=i(e.call(d));if(this.c&&~e.indexOf("{{"))return this.c.compile(e,this.options).render(d,c)}return i(e)}};var c=/&/g,d=/</g,e=/>/g,f=/\'/g,g=/\"/g,h=/[&<>\"\']/,k=Array.isArray||function(a){return Object.prototype.toString.call(a)==="[object Array]"}})(typeof exports!="undefined"?exports:Hogan),function(a){function h(a){a.n.substr(a.n.length-1)==="}"&&(a.n=a.n.substring(0,a.n.length-1))}function i(a){return a.trim?a.trim():a.replace(/^\s*|\s*$/g,"")}function j(a,b,c){if(b.charAt(c)!=a.charAt(0))return!1;for(var d=1,e=a.length;d<e;d++)if(b.charAt(c+d)!=a.charAt(d))return!1;return!0}function k(a,b,c,d){var e=[],f=null,g=null;while(a.length>0){g=a.shift();if(g.tag=="#"||g.tag=="^"||l(g,d))c.push(g),g.nodes=k(a,g.tag,c,d),e.push(g);else{if(g.tag=="/"){if(c.length===0)throw new Error("Closing tag without opener: /"+g.n);f=c.pop();if(g.n!=f.n&&!m(g.n,f.n,d))throw new Error("Nesting error: "+f.n+" vs. "+g.n);return f.end=g.i,e}e.push(g)}}if(c.length>0)throw new Error("missing closing tag: "+c.pop().n);return e}function l(a,b){for(var c=0,d=b.length;c<d;c++)if(b[c].o==a.n)return a.tag="#",!0}function m(a,b,c){for(var d=0,e=c.length;d<e;d++)if(c[d].c==a&&c[d].o==b)return!0}function n(a){return a.replace(f,"\\\\").replace(c,'\\"').replace(d,"\\n").replace(e,"\\r")}function o(a){return~a.indexOf(".")?"d":"f"}function p(a){var b="";for(var c=0,d=a.length;c<d;c++){var e=a[c].tag;e=="#"?b+=q(a[c].nodes,a[c].n,o(a[c].n),a[c].i,a[c].end,a[c].otag+" "+a[c].ctag):e=="^"?b+=r(a[c].nodes,a[c].n,o(a[c].n)):e=="<"||e==">"?b+=s(a[c]):e=="{"||e=="&"?b+=t(a[c].n,o(a[c].n)):e=="\n"?b+=v('"\\n"'+(a.length-1==c?"":" + i")):e=="_v"?b+=u(a[c].n,o(a[c].n)):e===undefined&&(b+=v('"'+n(a[c])+'"'))}return b}function q(a,b,c,d,e,f){return"if(_.s(_."+c+'("'+n(b)+'",c,p,1),'+"c,p,0,"+d+","+e+',"'+f+'")){'+"_.rs(c,p,"+"function(c,p,_){"+p(a)+"});c.pop();}"}function r(a,b,c){return"if(!_.s(_."+c+'("'+n(b)+'",c,p,1),c,p,1,0,0,"")){'+p(a)+"};"}function s(a){return'_.b(_.rp("'+n(a.n)+'",c,p,"'+(a.indent||"")+'"));'}function t(a,b){return"_.b(_.t(_."+b+'("'+n(a)+'",c,p,0)));'}function u(a,b){return"_.b(_.v(_."+b+'("'+n(a)+'",c,p,0)));'}function v(a){return"_.b("+a+");"}var b=/\S/,c=/\"/g,d=/\n/g,e=/\r/g,f=/\\/g,g={"#":1,"^":2,"/":3,"!":4,">":5,"<":6,"=":7,_v:8,"{":9,"&":10};a.scan=function(c,d){function w(){p.length>0&&(q.push(new String(p)),p="")}function x(){var a=!0;for(var c=t;c<q.length;c++){a=q[c].tag&&g[q[c].tag]<g._v||!q[c].tag&&q[c].match(b)===null;if(!a)return!1}return a}function y(a,b){w();if(a&&x())for(var c=t,d;c<q.length;c++)q[c].tag||((d=q[c+1])&&d.tag==">"&&(d.indent=q[c].toString()),q.splice(c,1));else b||q.push({tag:"\n"});r=!1,t=q.length}function z(a,b){var c="="+v,d=a.indexOf(c,b),e=i(a.substring(a.indexOf("=",b)+1,d)).split(" ");return u=e[0],v=e[1],d+c.length-1}var e=c.length,f=0,k=1,l=2,m=f,n=null,o=null,p="",q=[],r=!1,s=0,t=0,u="{{",v="}}";d&&(d=d.split(" "),u=d[0],v=d[1]);for(s=0;s<e;s++)m==f?j(u,c,s)?(--s,w(),m=k):c.charAt(s)=="\n"?y(r):p+=c.charAt(s):m==k?(s+=u.length-1,o=g[c.charAt(s+1)],n=o?c.charAt(s+1):"_v",n=="="?(s=z(c,s),m=f):(o&&s++,m=l),r=s):j(v,c,s)?(q.push({tag:n,n:i(p),otag:u,ctag:v,i:n=="/"?r-v.length:s+u.length}),p="",s+=v.length-1,m=f,n=="{"&&(v=="}}"?s++:h(q[q.length-1]))):p+=c.charAt(s);return y(r,!0),q},a.generate=function(b,c,d){var e='var _=this;_.b(i=i||"");'+p(b)+"return _.fl();";return d.asString?"function(c,p,i){"+e+";}":new a.Template(new Function("c","p","i",e),c,a,d)},a.parse=function(a,b,c){return c=c||{},k(a,"",[],c.sectionTags||[])},a.cache={},a.compile=function(a,b){b=b||{};var c=a+"||"+!!b.asString,d=this.cache[c];return d?d:(d=this.generate(this.parse(this.scan(a,b.delimiters),a,b),a,b),this.cache[c]=d)}}(typeof exports!="undefined"?exports:Hogan)
/* event date */
// Get the ul that holds the collection of tags
var datesCollectionHolder = $('#cm_cmbundle_event_eventDates');

// setup an "add a tag" link
// var $addDateLink = $('<a href="#" class="add_date_link">Add a date</a>');
var $newLinkForDate = $('<div></div>'); //.append($addDateLink);

function addEventDateFormDeleteLink($tagFormLi) {
    var $removeFormA = $('<a class="btn btn-default" href="#"><i class="glyphicon glyphicon-minus"></i> delete this date</a>');
    $tagFormLi.find('.well').append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $tagFormLi.remove();
    });
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
    datesCollectionHolder.children().children('div.form-group').each(function() {
        addEventDateFormDeleteLink($(this));
    });
    imageCollectionHolder.append($newLinkForImage);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    datesCollectionHolder.data('index', datesCollectionHolder.find(':input').length);
    imageCollectionHolder.data('index', imageCollectionHolder.find(':input').length);

    $(document).on('click', '.add_date_link', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // Get the data-prototype explained earlier
        var prototype = $('#cm_cmbundle_event_eventDates').data('prototype');
    
        // get the new index
        var index = $('#cm_cmbundle_event_eventDates').data('index');
    
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__label__/g, index).replace(/__name__/g, index);
    
        // increase the index with one for the next item
        $('#cm_cmbundle_event_eventDates').data('index', index + 1);
    
        // Display the form in the page in an li, before the "Add a tag" link li
        console.log($('#cm_cmbundle_event_eventDates').find('.well:last'));
        var $newForm = $(newForm);
        $('#cm_cmbundle_event_eventDates').find('.well:last').after($newForm);
        // $newLinkForDate.before($newForm);
        addEventDateFormDeleteLink($newForm);
    });
    $addImageLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addImageForm(imageCollectionHolder, $newLinkForImage);
    });
});
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
            }
        },
    });
    collection.on('typeahead:autocompleted typeahead:selected', function (event, datum) {
        protagonist_new_id += 1;
        $.get(script + '/protagonist/add?user_id=' + datum.id + '&protagonist_new_id=' + protagonist_new_id + '&entity_type=' + $('#protagonists').attr('object'), function (data) {
            $('.protagonists_user:last').after(data);
            // $('#protagonists_finder').val('');
        });
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

//  $('.protagonists_user:last').attr('protagonist_new_id') != undefined ? protagonist_new_id = $('.protagonists_user:last').attr('protagonist_new_id') : protagonist_new_id = 1;

    // // Add
    // $('#protagonists_finder').typeahead({
    //     minLength: 2,
    //     source: function (query, process) {
    //         return $.get(script + '/utenti/autocomplete_users', { query: query, exclusion: $('.protagonists_user, .protagonists_group_user').map(function() { return $(this).attr('user_id'); }).get().join(',') }, function (data) {
    //             users = data;
    //             return process(
    //                 $.map(data, function(user) {
    //                     item = new String(user.FirstName + ' ' + user.LastName);
    //                     item.data = user;
    //                     return item;
    //                 })
    //             );
    //         });
    //     },
    //     highlighter: function(item) {
    //         return '<img src="' + item.data.Img + '" class="pull-left" /> ' + item;
    //     },
    //     item: '<li class="media"><a href="' + script + '"></a></li>',
    //     items: 8,
    //     updater: function(item) {
    //         $.get(script + '/protagonist/add', { user_id: users[item].Id, protagonist_new_id: parseInt(protagonist_new_id) + 1, entity_type: $('#protagonists').attr('object') }, function(data) {
    //             $('#protagonists_users').append(data);
    //             protagonist_new_id ++;
    //         });
    //         return '';
    //     }
    // });

//  // Group
//  $('body').on('change', 'select[name*="group_id"]', function(event) {
//      // Mark all protagonists from previous selected group for delete
//      $('.protagonists_user[group_id]').each(function (index, element) {
//          $('input[name$="[Protagonist][' + $(element).attr('protagonist_key') + '][delete]"]').attr('value', 1);
//      });
//      if ($(event.target).val() != '') {
//          // Remove all protagonists from previous selected group
//          $('.protagonists_user[group_id]').remove();
//          // Fetch all protagonists from current selected group
//          $.getJSON(script + '/protagonist/addGroup', { group_id: $(event.target).val(), protagonist_new_id: parseInt(protagonist_new_id) + 1, entity_type: $('#protagonists').attr('object'), exclusion: $('.protagonists_user, .protagonists_group_user').map(function() { return $(this).attr('user_id'); }).get().join(',') }, function(data) {
//              $('#protagonists_users').append(data.protagonists);
//              // Update all protagonists that are already added but also part of the current selected group with the group_id
//              for (userId in data.usersExcluded) {
//                  $('.protagonists_user[user_id=' + data.usersExcluded[userId] + ']').attr('group_id', $(event.target).val());
//              }
//              protagonist_new_id ++;
//              fix_new_protagonists_ids();
//          }).error(function(jqXHR, textStatus, errorThrown) { console.log(textStatus + ': ' + errorThrown); });
//      } else {
//          $('.protagonists_user[group_id]').fadeOut('normal', function() { 
//              $(this).remove(); 
//              fix_new_protagonists_ids();
//          }); 
//      }
//  });
    
//  // Delete
//  $('body').on('click', '.protagonists_user .close', function(event) {
//      event.preventDefault(); 
//      protagonist_key = $(this).parents('.protagonists_user').attr('protagonist_key');
//      if ($(this).parent().attr('protagonist_new_id') == 0) { 
//          $(this).popover({ title: $(this).attr('confirmation-title'), content: $(this).attr('confirmation-body'), placement: 'left', html: true }).popover('show');
//          $(this).next('.popover').find('.btn-primary').bind('click', function(event) {                   
//              event.preventDefault();
//              console.log(protagonist_key);
//              protagonist_delete(event, protagonist_key); 
//          });
//          $(this).next('.popover').find('.btn-close').bind('click', function(event) { 
//              event.preventDefault(); 
//              $(this).parents('.popover').prev().popover('destroy'); 
//          });
//      } else {
//          $(this).parent().fadeOut('normal', function() { 
//              $('input[name*="' + $(this).attr('protagonist_key') + '"]').remove();
//              $(this).remove(); 
//              fix_new_protagonists_ids();
//          });
//      }
//  });
    
//  function protagonist_delete(event, protagonist_key) {
//      $.get(event.target.href, function(data) {   
//          $('.protagonists_user[protagonist_key="' + protagonist_key + '"]').fadeOut('normal', function() { $(this).remove(); });
//          $('input[name*="[Protagonist][' + protagonist_key + ']"]').remove();
//          $('.protagonists_user').each(function(index,element) {
//              if (parseInt($(element).attr('protagonist_key')) > protagonist_key) {
//                  $(element).attr('protagonist_key', $(element).attr('protagonist_key') - 1);
//              }
//          });
//          $('*[name*="Protagonist"]:not([name*="newProtagonist"])').each(function(index, element) {
//              $(element).attr('name', $(element).attr('name').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
//              $(element).attr('id', $(element).attr('id').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
//          });
//          $('label[for*="Protagonist"]:not([for*="newProtagonist"])').each(function(index, element) {
//              $(element).attr('for', $(element).attr('for').replace(/\d{1,}/, function(value) { return parseInt(value) > protagonist_key ? parseInt(value) - 1 : value; }));
//          });
//      }).error(function(jqXHR, textStatus, errorThrown) { 
//          $(event.target).closest('.btn').addClass('btn-danger').text(textStatus + ': ' + errorThrown ); 
//          console.log(textStatus + ': ' + errorThrown); 
//      }); 
//  }   
    
//  function fix_new_protagonists_ids() {
//      $('form > input[name*="newProtagonist"]').remove();
//      $('.protagonists_user[protagonist_new_id!=0]').each(function(index, element) {
//          $(element).attr('protagonist_key', $(element).attr('protagonist_key').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
//          $(element).find('*[name*="newProtagonist"]').each(function(sub_index, element) {
//              $(element).attr('name', $(element).attr('name').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
//              $(element).attr('id', $(element).attr('id').replace(/newProtagonist\d/, 'newProtagonist' + (index + 1)));
//          });
//          $(element).attr('protagonist_new_id', index + 1);
//          protagonist_new_id = index + 1;
//      });
//  }
    
    
    
//  /* DISCS */
//  // Add track
//  new_track_id = 1;
//  $('form').on('click', '.track-add', function(event) {
//      event.preventDefault();
//      $.get(event.currentTarget.href, { new_track_id: new_track_id }, function(data) {
//          $(event.target).closest('table').append(data);
//          new_track_id++;
//      });
//  });
//  // Delete track
//  $('#tracks').on('click', '.close', function(event) {
// /*       console.log($('#tracks tr').index($(event.target).closest('tr'))); */
//      event.preventDefault();
//      $('input[name="entity[DiscTrack][' + $('#tracks tr').index($(event.target).closest('tr')) + '][delete]"]').val('on');
//      $(event.target).closest('tr').fadeOut();
//  });
    
    
    
    /* AUTOCOMPLETE */

    // City autocomplete

    $('[autocomplete-city]').typeahead({
        name: 'cities',
        minLength: 3,
        template: '<div>{{ value }}</div>',
        engine: Hogan,
        remote: {
            url: 'http://ws.geonames.org/searchJSON?featureClass=P&style=full&username=circuitomusica&maxRows=8&lang=en&name_startsWith=%QUERY&type=json',
            filter: function(data) {
                return $.map(data.geonames, function(city) {
                    return city.name + (city.adminName1 ? ", " + city.adminName1 : "") + ", " + city.countryName;
                });
            }
        }
    });

    // $('form').on('click', '[autocomplete-city]', function() {
    //     console.log(666);
    //     $(this).typeahead({
    //         minLength:      3,
    //     source:             function (query, process) {
    //       return $.getJSON('http://ws.geonames.org/searchJSON', { featureClass: 'P', style: 'full', username: 'circuitomusica', maxRows: 8, lang: culture, name_startsWith: query, type: 'json' }, function (data) {
    //                 cities = new Array(); // CREDO NON SERVA PIU'
    //                 return process(
    //                     $.map(data.geonames, function(city) {
    //                         return city.name + (city.adminName1 ? ", " + city.adminName1 : "") + ", " + city.countryName;
    //                     })
    //                 );
    //       });
    //     }
    //     });
    // });
    
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
     
//     // Address autocomplete
    $('[address-autocomplete]').typeahead({
        name: 'address',
        // minLength: 3,
        template: '<div>{{ value }}</div>',
        engine: Hogan,
        remote: {
            url: 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&culture=' + culture + '&address=%QUERY',
            replace: function (url, uriEncodedQuery) {
                return url.replace('%QUERY', uriEncodedQuery);
            },
            filter: function(data) {
                if (data.status == 'OK') {
                    return $.map(data.results, function(address) {
                        return address.formatted_address;
                    });
                }
            }
        }
    });
    $('[address-autocomplete]').on('typeahead:autocompleted typeahead:selected', function (event, datum) {
        
    });
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
    
    
    
//  /* EMBED RELATION */
//  // Add
//  $('form .item-add').on('click', function(event) {
//      event.preventDefault();
//      console.log($('input').filter(function() { return this.id.match(new RegExp($(event.target).attr('id') + '\\d{1,2}_id')); }));
//      $.get(event.currentTarget.href, { new_id: $('input').filter(function() { return this.id.match(new RegExp($(event.target).attr('id') + '\\d{1,2}_id')); }).size() + 1 }, function(data) {
//          $(event.target).closest('.objects').find('ul').append(data);
//      });
//  });
//  // Remove
//  $('form').on('click', '.item-remove', function(event) {
//      event.preventDefault();
//      $('input[name="' + $(event.currentTarget).attr('rel') + '"]').val('on');
//      $(event.target).closest('li.object').fadeOut();
//  });     
});