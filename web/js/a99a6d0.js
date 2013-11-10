function infiniteScroll($target) {
    $target.children('a').html('<img src="/bundles/cm/images/layout/loader.gif" />');
    $.get($target.children('a').attr('href'), function(data) {
        $target.replaceWith(data);
        // $('#menu').hcSticky('reinit');
        // $('.sidebar').hcSticky('reinit');
    });
}

i=0;
$('body').ready(function() {
    $('.load_more').each(function() {
        $container = $(this).closest('.load_more_container').length > 0 ? $(this).closest('.load_more_container') : $(window);
        while ($(this).is(':visible') && $(this).offset().top - $container.height() < $container.scrollTop()) {
            if (i > 5) break;
            i++;
            infiniteScroll($(this));
        }
    });
});

$(function() {
    /* INFINITE SCROLL */


    $('body').on('click', '.load_more a', function(event) {
        event.preventDefault(); 
        infiniteScroll($(event.target).closest('.load_more')); 
    });

    $('.load_more_container').on('scroll', function(event) {
        event.stopPropagation();
        $loadMore = $(this).find('.load_more');
        if ($loadMore.length > 0 && $loadMore.is(':visible') && $loadMore.first().position().top < $(this).height()) {
            infiniteScroll($loadMore.first());
        }
    });
    $(document).on('scroll', function(event) {
        $('.load_more').each(function() {
            $container = $(this).closest('.load_more_container').length > 0 ? $(this).closest('.load_more_container') : $(window);
            if ($(this).is(':visible') && $(this).offset().top - $container.height() < $container.scrollTop()) {
                infiniteScroll($(this));
            }
        });
    });

    i = 0;
    
    $(document).on('activate.bs.scrollspy', function(event) {
        event.preventDefault();
        // console.log(i, $(event.target));
        i++;
    });

    /* DELETE CONFIRMATION */
    $('body').popover({
        html:             true,
        selector:     'a[data-toggle="popover"]',
        placement:    function() {
            return $(this.$element.context).attr('data-placement');
        }
    });
    $('body').on('click', 'a[data-toggle="popover"]', function(e) {
      e.preventDefault();
        event.stopPropagation();
  });
    $('body').on('click', '.popover-close', function(event) {
        event.preventDefault();
        $(this).closest('.popover').prev('a[data-toggle="popover"]').popover('hide');
    });
    
    /* AJAX ERROR */
    $(document).ajaxError(function(event, jqxhr, settings, exception) {
        if (jqxhr.status == 401) {
            $.get(script + '/loginDialog', function(data) {
                $('body').append(data);
                $('#loginDialog').modal().on('hidden', function () {
                  $(this).remove();
                });
            });
        } else {
            $(event.target).closest('.ajax-link-target').replaceWith('<span class="btn btn-danger">' + jqxhr.status + ': ' + jqxhr.statusText + '</span>'); 
        }
    });
    // Unlogged
    $('body').on('click', '.unlogged', function(event) {
        $.get(script + '/login');
    });    
    
    
    
    /* SHOW ALL */
    $('.object').on('click', '.show-all', function(event) {
        event.preventDefault();
        $(this).closest('.object').find('li.hide').slideDown();
        // $(this).remove();
    });
    
    
    
    /* TOOLTIP */
    $("*[data-toggle=tooltip]").tooltip({ delay: { show: 250, hide: 0 } });
    
    
    
    /* LIKE */
    $('body').on('click', '.iLikeIt', function(event) {
        event.preventDefault();
        $.getJSON(event.currentTarget.href, function(data) {
            $(event.target).closest('.object').find('.bottom-like-count').replaceWith(data.likeCount);
            $(event.target).closest('.object').find('.bottom-likes').replaceWith(data.likes);
            fix_triangle($(event.target));
            $(event.target).closest('.object').find('.iLikeIt').replaceWith(data.likeActions);
        });
    });
    
    
    
    /* COMMENTS */
    // Show comment form
    $('body').on('click', '.object .comment_new-show', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).closest('.bottom').find('ul').fadeIn('fast');
        $(this).closest('.object').find('.comment_new').removeClass('hide').find('textarea').focus();
        fix_triangle($(this));
    });
    // Hide comment form on blur
    $('.object').on('blur', '.comment_new:not(.object-detail .comment_new) form textarea, .comment_new:not(.object-detail .comment_new) form input[type="submit"]', function() {
        if ($(this).closest('.bottom').find('li.comment').length == 1) {
            $(this).closest('li.comment').addClass('hide');
            fix_triangle($(this));
        }
    });
    // Show comments on likes_comments_expanded == false
    $('.object').on('click', '.bottom-comment-count', function(event) {
        event.preventDefault();
        $(this).closest('.bottom').find('ul').fadeToggle('fast');
    });
    // Show all comments
    $('.object').on('click', '.comments-show_all', function(event) {
        event.preventDefault();
        $(this).closest('.bottom').find('li').removeClass('hide'); // TODO: .fadeIn() not working anymore
        $(this).parent('li').remove();
    });
    

    // Hide comment form submit button
    // $('.comment_new form input[type="submit"]').addClass('hide');
    // Elastic textarea
    $('body').on('keyup', '.bottom textarea', function() { 
      $(this).height(0); 
      $(this).height($(this).get(0).scrollHeight - 8); 
  });
    // Enter key press submit
    $('body').on('keydown', '.comment_new form textarea', function(event) {
        if (event.keyCode == '13' && event.shiftKey === false) { 
            event.preventDefault();
            if ($(this).val().length > 1) { 
                $(this).closest('form').submit();
            }                         
        }
    });
    // AJAX comment form
    $(document).on('submit', '.comment_new form', function(event) {
        event.preventDefault();
        $(event.currentTarget).ajaxSubmit({
            dataType:      'json',
            success:         function(data, statusText, xhr, form) {
                form.closest('li').before(data.comment);
                form.closest('.object').find('.bottom-comment-count').replaceWith(data.commentCount);
                form.find('textarea').focus().val('');
            }
        });
    });



    // Delete comment
    $('body').on('click', '.comment .popover .btn-primary', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $.get($(event.target).attr('href'), function(data) {
/*             $(event.target).closest('.bottom').find('.modal').modal('hide');  */
            $(event.target).closest('li').slideUp(300, function() {
                $(event.target).closest('.object').find('.bottom-comment-count').replaceWith(data); 
                $(this).remove(); 
            });
        });
    });

    
    
    
    /* FIX TRIANGLE */
    function fix_triangle(element) {
        if (element.closest('.object:not(.object-detail)').find('.bottom:not(.well) ul').height() > 0) {
            element.closest('.object').find('ul').addClass('triangle');
        } else {
            element.closest('.object').find('ul').removeClass('triangle');
        }
    }
    
    
    
    /* EVENT CALENDAR */
/*
    $('.events_calendar').on('mouseover', '.object a', function(event) {
        event.preventDefault();
        if (!$(event.target).attr('data-toggle')) {
            $.get($(event.target).attr('data-target'), function(data) {
                $(event.target)
                    .attr('data-toggle', 'popover')
                    .attr('data-content', data)
                    .popover({
                        'html': true,
                        'placement': 'top',
                    })
                    .popover('show');
            });
        } else {
            $(event.target).popover('show');
        }
    });
    $('.events_calendar').on('mouseout', '.object a', function(event) {
        $('.popover').each(function() { $(this).remove()});
        $(event.target).popover('hide');
    });
*/
    
                            
});
$(function() {
    /* AJAX LINK */
    $(document).on('click', '.ajax-link', function(event) {
        event.preventDefault();
        $(this).closest('*[rel="tooltip"]').tooltip('destroy');
        if ($(this).attr('data-loading-text')) {
            $(this).closest('.btn').html('<img src="/images/loader.gif" /> ' + $(this).attr('data-loading-text'));
        }
        $.get(event.currentTarget.href, function(data, status, xhr) {
            if (xhr.getResponseHeader('Content-Type') == 'application/json') {
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
    
    /* MENU */
    $('#menu ul.pull-right li.menu-tab a').on('click', function(event) {
        if ($(this).parent('li.dropdown.menu-tab').hasClass('open')) return;

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
    
    $('#menu').hcSticky({
        noContainer: true
    });
  
  
  
    /* SIDEBAR */
    $('.sidebar').hcSticky({
        top: 50,
        bottom: 15
    });
    
    
    
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
    
    
    
    /* TINY MCE */
/*
    $('textarea.mceSimple').tinymce({
        language: 'it',
      script_url: '/lib/tiny_mce/tiny_mce.js',
      theme: 'simple',
    });
    $('textarea.mceAdvanced').tinymce({
        language: 'it',
      script_url: '/lib/tiny_mce/tiny_mce.js',
      theme: 'advanced',
      plugins: 'inlinepopups,contextmenu,paste,advhr,advimage,advlink',
      theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,cleanup,code',
      theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image',
    });
*/
    
    
    
    // Discs list
    $('.disc').on('click', 'a', function(event) {
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
                    $('.disc-detail-container').removeClass('hide');
                    $('.disc-detail-nodge').animate({ left: $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13 }, 300);
                } else { // No disc detail open in the current row
                    $('.disc-detail-nodge').remove();
                    $('.disc-detail-container').remove();
                    $(event.target).closest('.discs-row').after('<div class="disc-detail-nodge"></div>' + data);
                    $('.disc-detail-container').slideDown(500);
                    $('.disc-detail-nodge').css('left', $(event.target).closest('.disc').position().left + ($(event.target).closest('.disc').outerWidth() / 2) + parseInt($(event.target).closest('.disc').css('margin-left'), 10) - 13);
                }
            });
        }
    });
    
});