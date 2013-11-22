/* USER ACTIVE */
var UserActive = {
    timeout: null,
    active: true,
    isActive: function() {
        return this.active;
    },
    begin: function() {
        this.active = true;
        this.watch();
    },
    watch: function() {
        t = this;
        $(document).on('mousemove mousedown scroll keypress', function() {
            t.active = true;
            if (t.timeout != null) clearTimeout(t.timeout);
            t.timeout = setTimeout(function () { t.active = false; }, 300000);
        });
    },
};

infiniteScrollOffset = 300;
function infiniteScroll(target, container, condition, loop) {
    if (!target.attr('load_more_loading') && condition(target, container)) {
        target.attr('load_more_loading', 'load');
        target.children('a').html('<img src="/bundles/cm/images/layout/loader.gif" />');
        $.get(target.children('a').attr('href'), function(data) {
            parent = target.parent();
            target.replaceWith(data);
            target = parent.find('.load_more');
        }).success(function() {
            if (loop) {
                infiniteScroll(target, container, condition, loop);
            }
        });
    }
}

function infiniteUpdate() {
    if (UserActive.isActive()) {
        $.get($('[update_more]:first').attr('update_more'), function(data) {
            $('[update_more]:first').before(data);
        }); 
    }  
}

$(function() {
    UserActive.begin();

    /* INFINITE SCROLL */
    $('body').ready(function() {
        $('.load_more').each(function() {
            container = $(this).closest('[load_more_container]').length > 0 ? $(this).closest('[load_more_container]') : $(window);
            infiniteScroll($(this), container, function(target, container) {
                return target.is(':visible') && target.offset().top - container.height() < container.scrollTop();
            }, true);
        });
    });

    $('body').on('click', '.load_more a', function(event) {
        event.preventDefault(); 
        infiniteScroll($(event.target).closest('.load_more'), null, function() { return true; }, false); 
    });

    $('[load_more_container]').on('scroll', function(event) {
        event.stopImmediatePropagation();
        infiniteScroll($(this).find('.load_more'), $(this), function(target, container) {
            return target.length > 0 && target.is(':visible') && target.first().position().top - infiniteScrollOffset < container.height();
        }, true);
        return false;
    });
    $(document).on('scroll', function(event) {
        $('.load_more').each(function() {
            container = $(this).closest('[load_more_container]').length > 0 ? $(this).closest('[load_more_container]') : $(window);
            infiniteScroll($(this), container, function(target, container) {
                return target.is(':visible') && target.offset().top - container.height() - infiniteScrollOffset < container.scrollTop();
            }, true);
        });
    });

    /* INFINITE UPDATE */
    if ($('[update_more]').length > 0) {
        setInterval(infiniteUpdate, 60000);
    }

    /* FILE INPUT */
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        $(event.target).closest('[btn-file-container]').find('input[readonly]').val(label);
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