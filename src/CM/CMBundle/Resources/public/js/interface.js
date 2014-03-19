$(function() {
    /* MENU */
    $('#menu ul.pull-right li.menu-tab a').on('click', function(event) {
        if ($(this).parent('li.dropdown.menu-tab').hasClass('open')) {
            return true;
        }

        $.get(event.currentTarget.href, function(data) {
            $(event.target).closest('li.menu-tab').find('.dropdown-menu-body .dropdown-menu-loader').hide();
            $(event.target).closest('li.menu-tab').find('.dropdown-menu-body .media-list').html(data);
            $(event.target).closest('li.menu-tab').find('.countNew').empty();
        });
    });
    $('#menu ul.nav.pull-right').on('click', '.dropdown-menu', function(event) {
        if (!$(event.target).hasClass('ajax-link')) {
            event.stopPropagation();
        }
    });
    $('#menu ul.nav.pull-right .dropdown-menu-body').on('mousewheel', function(event) {
        totalHeight = 0;
        $(this).children().each(function(){
            totalHeight = totalHeight + $(this).outerHeight();
        });

        // TODO: 8-damned-px
        if (($(this).scrollTop() <= 0 && event.originalEvent.deltaY < 0) || ($(this).scrollTop() - 8 >= totalHeight - $(this).outerHeight() && event.originalEvent.deltaY > 0)) {
            event.preventDefault();
        }
    });
    
    // $('#menu').hcSticky({
    //     noContainer: true
    // });
  
  
  
    /* SIDEBAR */
    // $('[sticky]').hcSticky({
    //     top: 50,
    //     bottom: 15
    // });
    
    
    
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
    
    
    
    // Discs list
    $(document).on('click', '.disc a', function(event) {
        event.preventDefault();
        if ($(event.target).closest('.disc').hasClass('active')) { // Disc detail close
            $('.disc-detail-container').slideUp('fast', function() { $('.disc-detail-nodge').remove(); $(this).remove(); });
            $('.disc').removeClass('active');
        } else {
            $('.disc').removeClass('active');
            $.get(event.currentTarget.href, function(data) {
                $(event.target).closest('.disc').addClass('active');
                if ($(event.target).closest('.discs-row').next().is('.disc-detail-nodge')) { // There is alreaty a disc detail open in the current row
                    $(event.target).closest('.discs-row').nextAll('.disc-detail-container').replaceWith(data);
                    $('.disc-detail-nodge').animate({ left: $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13 }, 300);
                } else { // No disc detail open in the current row
                    $('.disc-detail-nodge').remove();
                    $('.disc-detail-container').remove();
                    $(event.target).closest('.discs-row').after('<div class="disc-detail-nodge"></div>' + data);
                    $('.disc-detail-container').slideDown(500);
                    $('.disc-detail-nodge').css('left', $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13);
                }
                $('.disc-detail-container').removeClass('hidden');
            });
        }
    });

    

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
    
    

    /* SCROLL FIXED */
    var initialTop = $('#menu').position().top;
    var oldTop = $(window).scrollTop();

    if (oldTop > initialTop) {
        $('#menu').addClass('fixed');
    }

    $(window).scroll(function(event) {
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
});