function initRecipients() {
    $('#recipients_finder').on('keydown', function(event) {
        if (event.keyCode === $.ui.keyCode.TAB && $(event.currentTarget).data('ui-autocomplete').menu.active) {
            event.preventDefault();
        }
    }).tokenfield({
        autocomplete: {
            minLength: 1,
            source: function(request, response) {
                var url = typeaheadHintRoute + '?query=' + request.term + '&exclude=' + $.map($('#recipients_finder').tokenfield('getTokens'), function(elem) { return elem.value; }).join(',');
                $.ajax(url, {
                    success: function(data) {
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
        },
        showAutocompleteOnFocus: true
    });
    $('.ui-autocomplete-input').data('ui-autocomplete')._renderItem = function(ul, item) {
        return $('<li><a href="#">' + item.view + '</a></li>').appendTo(ul);
    };

    var placeholder = $('#recipients_finder_container input.ui-autocomplete-input').attr('placeholder');

    $(document).on('tokenfield:preparetoken', '#recipients_finder', function(event) {
        if ($('#recipients_finder').tokenfield('getTokens').length == 0) {
            $('#recipients_finder_container input.ui-autocomplete-input').attr('placeholder', '');
        }
    }).on('tokenfield:removetoken', '#recipients_finder', function(event) {
        var values = $('#message_recipients').val().split(',');
        $('#message_recipients').val($('#message_recipients').val().replace(new RegExp(',?' + event.token.value), ''));

        if ($('#recipients_finder').tokenfield('getTokens').length == 0) {
            $('#recipients_finder_container input.ui-autocomplete-input').attr('placeholder', placeholder);
        }
    });

    $('[data-recipient]').each(function(i, elem) {
        $('#recipients_finder').tokenfield('createToken', {label: $(elem).attr('data-recipient'), value: $(elem).attr('data-recipient-username')});

        $('#message_recipients').val($('#message_recipients').val() + (i == 0 ? '' : ',') + $(elem).attr('data-recipient-username'));
    }).remove();
}

$(function() {
    /* MENU */
    $('#menu ul.pull-left li.dropdown').hover(function(event) {
        timeout = setTimeout(function(){
            $(event.currentTarget).addClass('open'); 
        }, 150); 
    }, function(event) { 
        clearTimeout(timeout);
        $(event.currentTarget).removeClass('open'); 
    });
    $('#menu ul.pull-right li.menu-tab.menu-tab-ajax a').on('click', function(event) {
        if ($(document).width() > 767) {
            if ($(this).parent('li.dropdown.menu-tab').hasClass('open')) {
                return true;
            }
    
            $.get(event.currentTarget.href, function(data) {
                $(event.target).closest('li.menu-tab').find('.dropdown-menu-body .dropdown-menu-loader').hide();
                $(event.target).closest('li.menu-tab').find('.dropdown-menu-body .media-list').html(data);
                $(event.target).closest('li.menu-tab').find('.dropdown-menu-body .media-list .unread').removeClass('unread', 2000);
                $(event.target).closest('li.menu-tab').find('.countNew').empty();
            });
        } else {
            event.stopPropagation();
            location.href = event.currentTarget.href;
        }
    });
    $('#menu ul.pull-right').on('click', '.dropdown-menu', function(event) {
        if (!$(event.target).hasClass('ajax-link')) {
            event.stopPropagation();
        }
    });
    $('#menu ul.pull-right .dropdown-menu-body').on('mousewheel', function(event) {
        totalHeight = 0;
        $(this).children().each(function(){
            totalHeight = totalHeight + $(this).outerHeight();
        });

        // TODO: 8-damned-px
        if (($(this).scrollTop() <= 0 && event.originalEvent.deltaY < 0) || ($(this).scrollTop() - 8 >= totalHeight - $(this).outerHeight() && event.originalEvent.deltaY > 0)) {
            event.preventDefault();
        }
    });
    
    

    /* SCROLL FIXED */
    var initialTop = $('#menu').position().top;
    var oldTop = $(window).scrollTop();

    if (oldTop > initialTop) {
        $('#menu').addClass('fixed');
    }

    $(window).on('mousewheel scroll mouseup DOMMouseScroll', function(event) {
        if ($('#body').hasClass('fixing')) return;

        var currentTop = $(window).scrollTop();

        if (oldTop < initialTop && currentTop > initialTop) {
            $('#menu').addClass('fixed');
        }
        if (oldTop > initialTop && currentTop <= initialTop) {
            $('#menu').removeClass('fixed');
        }

        oldTop = currentTop;
    });
    
    
    
    // search bar
    $('#search-bar').on('click', function(event) {
        event.preventDefault();
    }).autocomplete({
        minLength: 1,
        source: function(request, response) {
            var url = $('#search-bar').data('url') + '?q=' + request.term;

            $('#search-bar').siblings('span[href]').attr('href', url);

            $.ajax(url, {
                success: function(data) {
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

            window.location = ui.item.url;
        }
    });
    $('#search-bar').on('keyup', function() {
        if ($('#search-bar').val() == '') {
            $('#search-bar').siblings('span[href]').attr('href', '#');
        }
    });
    $('#search-bar').siblings('span[href]').on('click', function() {
        if ($('#search-bar').siblings('span[href]').attr('href') == '#') {
            $('#search-bar').focus();
        } else {
            window.location = $('#search-bar').siblings('span[href]').attr('href');
        }
    });
    
    $('.ui-autocomplete-input').data('ui-autocomplete')._renderItem = function(ul, item) {
        var url = item.url || '#';
        var view = item.view || item.label;
        return $('<li><a href="' + url + '">' + view + '</a></li>').appendTo(ul);
    };
    
    
    // /* MODAL */
    // $('body').on('click', '.modal-trigger', function(event) {
    //     event.preventDefault();
    //     $.get(event.currentTarget.href, function(data) {
    //         $('body').append(data);
    //         $('#modal').modal().on('hidden', function () {
    //             $(this).remove();
    //         });
    //     });
    // });



    // YouTube preview
    $(document).on('click', '[youtube-video-source]', function(event) {
        var height = $(event.currentTarget).height();
        var videoId = $(event.currentTarget).attr('youtube-video-source');
        $(event.currentTarget).replaceWith('<iframe width="100%" height="' + height + '" src="//www.youtube.com/embed/' + videoId + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');
    });
    $(document).on('click', '[vimeo-video-source]', function(event) {
        var height = $(event.currentTarget).height();
        var videoId = $(event.currentTarget).attr('vimeo-video-source');
        $(event.currentTarget).replaceWith('<iframe width="100%" height="' + height + '" src="//player.vimeo.com/video/' + videoId + '?color=040505" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
    });



    // Relations
    // $('body').on('click', '.relations-menu .dropdown-menu', function(event) {
    //     console.log($(this), event, $(event.currentTarget));
    //     event.stopPropagation();
    // });

    // $('body').on('submit', '.relations-menu form', function(event) {
    //     event.preventDefault();
    //     $(event.currentTarget).ajaxSubmit({
    //         dataType: 'json',
    //         success: function(data, statusText, xhr, form) {
    //             console.log(data);
    //         }
    //     });
    // });



    /* AJAX LINK */
    $(document).on('click', '.ajax-link', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if ($(event.currentTarget).is('a')) {
            url = event.currentTarget.href;
        }
        if ($(event.currentTarget).is('input:submit') || $(event.currentTarget).is('button')) {
            $(this).closest('*[rel="tooltip"]').tooltip('destroy');
            url = $(event.currentTarget).closest('form').attr('action');
        }

        if ($(this).attr('data-loading-text')) {
            $(this).html('<img src="/images/loader.gif" /> ' + $(this).attr('data-loading-text'));
        }
        $.get(event.currentTarget.href, function(data, status, xhr) {
            var callback = $(event.currentTarget).attr('ajax-link-callback');
            if (callback) {
                callback = callback.substring(1);
                func = callback.split('(')[0];
                args = callback.split('(').slice(1).join('(').slice(0, -1);
                window[func](event.currentTarget, data, args);
            } else if (xhr.getResponseHeader('Content-Type') == 'application/json') {
                $(event.target).closest('.ajax-link-target').replaceWith(data.main);
                $.each(data, function(i, e) {
                    if (i != 'main') {
                        $('.' + i).replaceWith(e);
                    }
                });
            } else {
                $(event.target).closest('.ajax-link-target').replaceWith(data);
            }
        });
    });
    $(document).on('click', '.ajax-form', function(event) {
        event.preventDefault();
        event.stopPropagation();

        $(this).closest('*[rel="tooltip"]').tooltip('destroy');

        if ($(this).attr('data-loading-text')) {
            $(this).html('<img src="/images/loader.gif" /> ' + $(this).attr('data-loading-text'));
        }
        $.post($(event.currentTarget).closest('form').attr('action'), $(event.currentTarget).closest('form').serialize(), function(data, status, xhr) {
            if (xhr.getResponseHeader('Content-Type') == 'application/json') {
                $(event.target).closest('.ajax-form-target').replaceWith(data.main);
                $.each(data, function(i, e) {
                    if (i != 'main') {
                        $('.' + i).replaceWith(e);
                    }
                });
            } else {
                $(event.target).closest('.ajax-form-target').replaceWith(data);
            }
        });
    });



    /* RECIPIENT */
    if ($('#recipients_finder').length) {
        initRecipients();
    }



    /* DISCS */
    $(document).on('click', '.disc-cover', function(event) {
        event.preventDefault();

        var $disc = $(event.currentTarget).closest('.disc');
        var wasActive = $disc.hasClass('active');

        $('.disc.active').removeClass('active');
        $('.disc-content').slideUp('fast', function() {
            $(this).remove();
        });

        if (wasActive) {
            return;
        }

        var $lastInRow = $disc;
        $.each($disc.nextAll('.disc'), function(i, elem) {
            $lastInRow = $(elem);
            if ($(elem).position().left == 0) {
                $lastInRow = $(elem).prev();
                return false;
            }
        });

        $.get($(event.currentTarget).find('a').attr('href'), function(data) {
            var $data = $(data);
            $lastInRow.after($data.hide());
            $data.slideDown(250);
            $disc.addClass('active');
        });
    });
    $(document).on('click', '.audio-controls', function(event) {
        event.preventDefault();

        var title = $(event.currentTarget).attr('title');
        $(event.currentTarget).attr('title', $(event.currentTarget).attr('title-alt'));
        $(event.currentTarget).attr('title-alt', title);


        var audio = $(event.currentTarget).siblings('audio')[0];
        if (audio.paused || audio.ended) {
            $('.audio-controls').each(function(i, elem) {
                $(elem).removeClass('active');
                $(elem).siblings('audio')[0].pause();
            });

            $(event.currentTarget).addClass('');
            $(event.currentTarget).addClass('active');
            audio.play();
        } else {
            $(event.currentTarget).removeClass('active');
            audio.pause();
        }
    });


    // $(document).on('click', '.disc a', function(event) {
    //     event.preventDefault();
    //     if ($(event.target).closest('.disc').hasClass('active')) { // Disc detail close
    //         $('.disc-detail-container').slideUp('fast', function() { $('.disc-detail-nodge').remove(); $(this).remove(); });
    //         $('.disc').removeClass('active');
    //     } else {
    //         $('.disc').removeClass('active');
    //         $.get(event.currentTarget.href, function(data) {
    //             $(event.target).closest('.disc').addClass('active');
    //             if ($(event.target).closest('.discs-row').next().is('.disc-detail-nodge')) { // There is alreaty a disc detail open in the current row
    //                 $(event.target).closest('.discs-row').nextAll('.disc-detail-container').replaceWith(data);
    //                 $('.disc-detail-nodge').animate({ left: $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13 }, 300);
    //             } else { // No disc detail open in the current row
    //                 $('.disc-detail-nodge').remove();
    //                 $('.disc-detail-container').remove();
    //                 $(event.target).closest('.discs-row').after('<div class="disc-detail-nodge"></div>' + data);
    //                 $('.disc-detail-container').slideDown(500);
    //                 $('.disc-detail-nodge').css('left', $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13);
    //             }
    //             $('.disc-detail-container').removeClass('hidden');
    //         });
    //     }
    // });
});