// this has to be the first jquery script
$(document).ready(function() {
    $.ajaxSetup({ cache: false });
});

// is :hover
function isHover($elem) {
    return $elem.parent().find(':hover').get(0) == $elem.get(0);
}

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
function infiniteScroll(target, container, condition, loop, callback) {
    if (!target.attr('load_more_loading') && condition(target, container)) {
        target.attr('load_more_loading', 'load');
        target.children('a').html('<img src="/bundles/cm/images/layout/loader.gif" />');
        $.get(target.children('a').attr('href'), function(data) {
            var parent = target.parent();
            if (callback) {
                callback = callback.substring(1);
                var func = callback.split('(')[0];
                var args = callback.split('(').slice(1).join('(').slice(0, -1);
                window[func](data, target, container, args);
            } else {
                target.replaceWith(data);
            }
            target = parent.find('[load_more]');
        }).success(function() {
            if (loop) {
                infiniteScroll(target, container, condition, loop, callback);
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

function insertRelationItem(c, d, a) {
    $(c).closest('.relation-menu').find('.relation-button').replaceWith(d.button);
    $(c).closest('div').replaceWith(d.item);
}

/*
function initPopoverPublisher($elem) {
    if ($elem.attr('popover-publisher') == 'init') return;
    $elem.attr('popover-publisher', 'init');

    var hoverOut = function($e) {
        setTimeout(function() {
            if (!isHover($e) && !isHover($('.popover'))) {
                $e.removeClass('popover-publisher-in').popover('hide');
            }
        }, 250);
    };

    var hoverIn = function($e) {
        if (isHover($e)) {
            $e.popover('show');
            $('.popover').addClass('popover-publisher').on('mouseleave', function() {
                hoverOut($e);
            });
        }
    };
    $elem.popover({
        selector: '[popover-publisher]',
        trigger: 'manual',
        placement: 'auto top',
        delay: {show: 700, hide: 250},
        container: 'body',
        html: true,
        content: function() {
            var content;
            $.ajax({
                url: $(this).attr('data-href'),
                async: false
            }).done(function(data) {
                content = data;
            });
            return content;
        }
    }).on('mouseenter', function(event) {
        hoverIn($(event.currentTarget));
    }).on('mouseleave', function(event) {
        hoverOut($(event.currentTarget));
    });
    if (isHover($elem)) {
        hoverIn($elem);
    }
}
*/

function initSlideshow($slideshow) {
    $slideshow.cycle({
        loader: true,
        log: false,
        next: '.box-partner-nav-next',
        pauseOnHover: true,
        prev: '.box-partner-nav-prev',
        slides: '> div',
        swipe: true,
        fx: 'scrollHorz'
    });
}

function initSlideshowSponsored($slideshow) {
    $slideshow.cycle({
        fx: 'scrollVert',
        log: false,
        pauseOnHover: true,
        slides: '> div',
        speed: 250
    });
}

$(function() {
    UserActive.begin();

    /* PUBLISHER POPOVER */
    $(document).on('mouseenter mouseleave', '[popover-publisher]', function(event) {
        if (event.type == 'mouseenter') {
            if (typeof $popover !== 'undefined') {
                $popover.popover('destroy');
            }
            $popover = $(event.currentTarget);
            timeout = setTimeout(function() {
                if ($popover.attr('popover-publisher') != 'loaded') {
                    $.ajax({
                        url: $popover.attr('data-href'),
                        async: false
                    }).done(function(data) {
                        $('[popover-publisher][data-href="' + $popover.attr('data-href') + '"]').attr('popover-publisher', 'loaded').attr('data-content', data);
                    });
                }
                $popover.popover({
                    selector: '[popover-publisher]',
                    trigger: 'manual',
                    placement: 'auto top',
                    container: 'body',
                    html: true,
                    template: '<div class="popover popover-publisher' + (logged ? ' logged' : '') + '"><div class="arrow"></div><div class="popover-content"></div></div>'
                }).popover('show');
                $('.popover').on('mouseleave', function () {
                    setTimeout(function () {
                        $('[popover-publisher][data-href="' + $popover.attr('data-href') + '"]').attr('data-content', $('.popover-content').html());
                        console.log($('.popover-content').html());
                        $popover.popover('destroy');
                    }, 200);
                });
            }, 200); 
        } else {
            clearTimeout(timeout);
            setTimeout(function () {
                if (!$('.popover-publisher:hover').length) {
                    $('[popover-publisher][data-href="' + $popover.attr('data-href') + '"]').attr('data-content', $('.popover-content').html());
                    $popover.popover('destroy');
                }
            }, 200);
        }
    });
/*
    popover.hover(function (e) {
        timeout = setTimeout(function(e) {
            popover.popover({
                selector: '[popover-publisher]',
                trigger: 'manual',
                placement: 'auto top',
                delay: {show: 700, hide: 250},
                container: 'body',
                html: true,
                content: function() {
                    var content;
                    $.ajax({
                        url: $(this).attr('data-href'),
                        async: false
                    }).done(function(data) {
                        content = data;
                    });
                    return content;
                }
            }).popover('show'); 
        }, 500); 
    }, function (e) { 
        clearTimeout(timeout);
        popover.popover('hide'); 
    });
*/
/*     initPopoverPublisher($('[popover-publisher][popover-publisher!="init"]')); */
/*
    $(document).on('mouseenter', '[popover-publisher][popover-publisher!="init"]', function(event) {
        initPopoverPublisher($(event.currentTarget));
    });
*/
    // function initPopoverPublisher($elem) {
    //     if ($elem.attr('popover-publisher') == 'active') return;

    //     $elem.attr('popover-publisher', 'active');
    //     $elem.popover({
    //         selector: '[popover-publisher]',
    //         trigger: 'manual',
    //         placement: 'auto top',
    //         delay: {show: 1000, hide: 250},
    //         container: 'body',
    //         html: true,
    //         content: function() {
    //             var content;
    //             $.ajax({
    //                 url: $(this).attr('data-href'),
    //                 async: false
    //             }).done(function(data) {
    //                 content = data;
    //             });
    //             return content;
    //         }
    //     }).on('mouseenter', function(event) {
    //         $(event.currentTarget).addClass('hover');
    //         setTimeout(function() {
    //             if ($(event.currentTarget).hasClass('hover')) {
    //                 $(event.currentTarget).popover('show');
    //                 $('.popover').addClass('hover').addClass('popover-publisher').on('mouseleave', function () {
    //                     $('.popover').removeClass('hover');
    //                     $(event.currentTarget).popover('hide');
    //                 });
    //             }
    //         }, 1000);
    //     }).on('mouseleave', function(event) {
    //         $(event.currentTarget).removeClass('hover');
    //         setTimeout(function() {
    //             if (!$(event.currentTarget).hasClass('hover') && !$('.popover').hasClass('hover')) {
    //                 $(event.currentTarget).popover('hide');
    //             }
    //         }, 250);
    //     });
    // }
    // initPopoverPublisher($('[popover-publisher]'));
    // $(document).on('mouseenter', '[popover-publisher!="active"]', function(event) {
    //     initPopoverPublisher($(event.currentTarget));
    // });

    /* INFINITE SCROLL */
    $('[load_more]').each(function() {
        var container = $(this).closest('[load_more_container]').length > 0 ? $(this).closest('[load_more_container]') : $(window);
        infiniteScroll($(this), container, function(target, container) {
            if (target.attr('load_more') == 'reverse') {
                return target.is(':visible') && target.offset().top + target.height() > container.scrollTop();
            } else {
                return target.is(':visible') && target.offset().top - container.height() < container.scrollTop();
            }
        }, true);
    });

    $('body').on('click', '[load_more] a', function(event) {
        event.preventDefault();
        var target = $(event.target).closest('[load_more]');
        infiniteScroll(target, null, function() { return true; }, false, target.attr('load_more-callback')); 
    });

    $('[load_more_container]').on('scroll', function(event) {
        event.stopImmediatePropagation();
        var target = $(this).find('[load_more]');
        infiniteScroll(target, $(this), function(t, c) {
            if (target.attr('load_more') == 'reverse') {
                return t.length > 0 && t.is(':visible') && t.first().position().top + t.first().height() + infiniteScrollOffset > c.position().top;
            } else {
                return t.length > 0 && t.is(':visible') && t.first().position().top - infiniteScrollOffset < c.height();
            }
        }, true, target.attr('load_more-callback'));
        return false;
    });
    $(document).on('scroll', function(event) {
        $('[load_more]').each(function() {
            var target = $(this);
            var container = $(this).closest('[load_more_container]').length > 0 ? $(this).closest('[load_more_container]') : $(window);
            infiniteScroll(target, container, function(t, c) {
                if (target.attr('load_more') == 'reverse') {
                    return t.is(':visible') && t.offset().top + t.height() + infiniteScrollOffset > c.scrollTop();
                } else {
                    return t.is(':visible') && t.offset().top - c.height() - infiniteScrollOffset < c.scrollTop();
                }
            }, true, target.attr('load_more-callback'));
        });
    });



    /* INFINITE UPDATE */
    if ($('[update_more]').length > 0) {
        setInterval(infiniteUpdate, 60000);
    }



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
    $('body').on('mouseenter', '[data-toggle=tooltip]', function(event) {
        $(event.currentTarget).tooltip({
            delay: { show: 250, hide: 0 }
        }).tooltip('show');
        $(event.currentTarget).on('remove', function() {
            $(event.currentTarget).tooltip('destroy');
        });
    });



    /* FULLSCREEN */

    // TODO: http://codecanyon.net/item/nacho-lightbox-flat-responsive-lightbox/5434882
    
    // images
    // $('body').on('click', '[ilightbox="album"]', function(event){
    //     event.preventDefault();

    //     console.log($(event.currentTarget).closest('[ilightbox="album"]').find('[ilightbox="image"]'));

    //     $('[ilightbox="album"]').iLightBox({
    //     });

    // });

    // document.getElementById('links').onclick = function (event) {
    //     event = event || window.event;
    //     var target = event.target || event.srcElement,
    //         link = target.src ? target.parentNode : target,
    //         options = {index: link, event: event},
    //         links = this.getElementsByTagName('a');
    //     blueimp.Gallery(links, options);
    // };


    $('body').on('click', '[lightbox="image"]', function(event){
        var originalUrl = document.URL;
        var originalTitle = document.title;
        var historyCount = 0;
        var $sidebar = $('#blueimp-gallery-container .sidebar');
        var $title = $('#blueimp-gallery .title');
        var $sequence = $('#blueimp-gallery .sequence');
        var $target = $(event.currentTarget);
        var href = $target.attr('lightbox-src') || $target.find('img').attr('src');
        var imageUrl = href.split('/');
        imageUrl = imageUrl.slice(0, imageUrl.length - 1).join('/') + '/';
        var pageUrl = $target.attr('href').split('/');
        pageUrl = pageUrl.slice(0, pageUrl.length - 1).join('/') + '/';
        var json = null;
        var sidebarReq = null;
        var sidebarTimer = null;

        var gallery = blueimp.Gallery([{
                href: href,
                thumbnail: href
            }], {
                event: event,
                onopen: function() {
                    $('#body').addClass('fixing');
                    var top = $(document).scrollTop();
                    $('#body').css('top', -top).addClass('fixed');

                    $sidebar.show();
                },
                onopened: function() {
                    $.get($target.attr('lightbox-json'), function(data) {
                        json = data;

                        var index = -1;
                        for (var i = 0; i < json.images.length; i++) {
                            var image = json.images[i];
                            
                            if (image.id == json.id) {
                                index = i;
                            } else if (index != -1) {
                                json.images[i].index = i;

                                gallery.add([{
                                    href: imageUrl + image.img,
                                    thumbnail: imageUrl.replace('full', 'small') + image.img
                                }]);
                            }
                        }

                        if (index == -1) {
                            return;
                        }
                        $sequence.html((index + 1) + ' / ' + json.images.length);

                        for (var i = 0; i < json.images.length; i++) {
                            var image = json.images[i];

                            json.images[i].index = i;
                            if (i == index) {
                                break;
                            }

                            gallery.add([{
                                href: imageUrl + image.img,
                                thumbnail: imageUrl.replace('full', 'small') + image.img
                            }]);
                        }

                        json = json.images.slice(index).concat(json.images.slice(0, index));
                    });

                    history.replaceState(originalUrl, originalTitle, originalUrl);
                },
                onslide: function(i, slide) {
                    if (sidebarTimer != null) {
                        clearTimeout(sidebarTimer);
                    }
                    if (sidebarReq != null) {
                        sidebarReq.abort();
                    }
                    $title.html('');
                    $sidebar.html('');

                    var url;
                    if (i == 0) {
                        url = $target.attr('href');
                    } else {
                        url = pageUrl + json[i].id;
                    }

                    sidebarTimer = setTimeout(function() {
                        sidebarReq = $.get(url, function(data) {
                            history.pushState(url, data.albumTitle, url);
                            historyCount++;
                            document.title = data.albumTitle;
                            
                            $title.html('<a href="' + data.albumLink + '">' + data.albumTitle + '</a>');
                            $sidebar.html(data.sidebar);
                        });
                    }, 100);
                    if (json != null) {
                        $sequence.html((json[i].index + 1)  + ' / ' + json.length);
                    }
                },
                onclose: function() {
                    $sidebar.hide();

                    history.go(-historyCount);
                    document.title = originalTitle;
                },
                onclosed: function() {
                    var top = $('#body').position().top;
                    
                    $('#body').css('top', '').removeClass('fixed');
                    $(document).scrollTop(-top);
                    $('#body').removeClass('fixing');
                }
        });
    });
    
    
    /* CHANGE TEXT */
    $('body').on('click', '[text-alt]', function(event) {
        previousText = $(event.currentTarget).html();
        $(event.currentTarget).html($(event.currentTarget).attr('text-alt'));
        $(event.currentTarget).attr('text-alt', previousText);
    });
    
    
    
    /* LIKE */
    $('body').on('click', '.iLikeIt', function(event) {
        event.preventDefault();
        $.getJSON(event.currentTarget.href, function(data) { 
            if ($(event.currentTarget).is('[social-selector]')) {
                social = $($(event.currentTarget).attr('social-selector'));
                $(event.currentTarget).replaceWith(data.likeActionsButton);
            } else {
                social = $(event.target).closest('.object, .bottom');
                $('[social-selector="#' + social.attr('id') + '"]').replaceWith(data.likeActionsButton);
            }     
            social.find('.bottom-like-count').replaceWith(data.likeCount);
            social.find('.bottom-likes').replaceWith(data.likes);
/*             fix_triangle(social); */
            social.find('.iLikeIt').replaceWith(data.likeActions);
        });
    });



    /* EXPANDABLE */
    $('textarea[expandable]').autosize();
    $(document).on('focus', 'textarea[expandable]', function() { 
        $(this).autosize();
    });
    
    
    
    /* COMMENTS */
    // Show comment form
    $('body').on('click', '.comment_new-show', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $($(event.currentTarget).attr('href')).focus();
    });
    // Hide comment form on blur
    $('body').on('blur', '.comment_new:not(.object-detail .comment_new) form textarea, .comment_new:not(.object-detail .comment_new) form input[type="submit"]', function() {
        if ($(this).closest('.bottom').find('li.comment').length == 1) {
            $(this).closest('li.comment').addClass('hide');
            fix_triangle($(this));
        }
    });
    // Show comments on likes_comments_expanded == false
    $('body').on('click', '.bottom-comment-count', function(event) {
        event.preventDefault();
        $(this).closest('.bottom').find('ul').fadeToggle('fast');
    });
    // Show all comments
    $('body').on('click', '.comments-show_all', function(event) {
        event.preventDefault();
        $(event.currentTarget).closest('.bottom').find('.comment').removeClass('hidden'); // TODO: .fadeIn() not working anymore
        $(event.currentTarget).parent().remove();
    });
    

    // Hide comment form submit button
    // $('.comment_new form input[type="submit"]').addClass('hide');
    // Enter key press submit
    $('body').on('keydown', '.comment_new form textarea, .comment_new form input, .comment_edit form textarea', function(event) {
        if (event.keyCode == '13' && event.shiftKey === false) {
            event.preventDefault();
            if ($(this).val().length > 1) {
                $(this).closest('form').submit();
            }                         
        }
    });
    // AJAX comment form
    $(document).on('submit', '.comment_new form, .comment_edit form', function(event) {
        event.preventDefault();
        $(event.currentTarget).find('.comment').attr('readonly', 'readonly');
        $(event.currentTarget).ajaxSubmit({
            dataType: 'json',
            success: function(data, statusText, xhr, $form) {
                var commentType = $form.find('[comment-type]').attr('comment-type').split(' ');
                var $media = $form.closest('.media');

                if (commentType == 'upward') {
                    $media.before(data.comment);
                    $form.closest('.bottom').find('.bottom-comment-count').replaceWith(data.commentCount);
                    $form.find('.comment').focus().val('').trigger('autosize.resize');
                } else if (commentType == 'downward') {
                    $media.closest('.box').after(data.comment);
                    $form.find('.comment').focus().val('').trigger('autosize.resize');
                } else if ($.inArray('edit', commentType)) {
                    $form.closest('.comment').replaceWith(data.comment);
                }
            },
            complete: function() {
                $(event.currentTarget).find('.comment').removeAttr('readonly');
            }
        });
    });
    // // Delete comment
    // $('body').on('click', '.comment .popover .btn-primary', function(event) {
    //     event.preventDefault();
    //     event.stopPropagation();
    //     $.get($(event.target).attr('href'), function(data) {
    //         // $(event.target).closest('.bottom').find('.modal').modal('hide');  
    //         $(event.target).closest('li').slideUp(300, function() {
    //             $(event.target).closest('.bottom').find('.bottom-comment-count').replaceWith(data); 
    //             $(this).remove(); 
    //         });
    //     });
    // });
    // Modify comment
    $('body').on('click', '.comment .comment_edit-btn', function(event) {
        event.preventDefault();

        var $comment = $(event.currentTarget).closest('.comment');
        var $image = $comment.find('.image_box');
        var href = $(event.currentTarget).attr('href');
        var text = $comment.find('.comment-body').text().trim();
        var old = $comment.html();
        $comment.html('');
        $comment.append($image);
        $comment.append('\
            <div class="media-body comment_edit">\
                <form method="post" action="' + href + '" class="form-horizontal" novalidate="novalidate">\
                    <div>\
                        <textarea id="cm_cmbundle_comment_comment" name="cm_cmbundle_comment[comment]" required="required" class="input-lg form-control" comment_new="" placeholder="write a comment..." comment-type="upward edit" autocomplete="off"></textarea>\
                    </div>\
                    <input type="submit" name="commit" value="Send" class="pull-right btn btn-mini hidden">\
                </form>\
                <div class="text-muted">Press ESC to cancel</div>\
            </div>'
        );
        $comment.find('textarea').focus().html(text);

        $comment.find('textarea').on('keyup', function(event) {
            if (event.keyCode == 27) {
                $comment.html(old);
            }
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

    /* GMAPS */
    // directions
    $('body').on('click', '[gmap-directions]', function(event) {
        coords = $(event.currentTarget).attr('href').match(/daddr=([\d\.]+),([\d\.]+)/);

        if (coords != null) {
            $.ajax({
                url: 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&lang=' + culture + '&latlng=' + coords[1] + ',' + coords[2],
                async: false
            }).done(function(data) {
                console.log(data);
                if (data.status == 'OK') {
                    $(event.currentTarget).attr('href', 'https://maps.google.com/maps?daddr=' + data.results[0].formatted_address);
                }
            });
        }
    });

    // map visualization
    $(document).on('click', '[data-toggle="collapser"]', function(event) {
        event.preventDefault();
        $($(event.currentTarget).attr('href')).toggleClass('in');
    });

    
    
    /* AJAX LOAD CONTROLLER */
    $('[data-ajax-url]').each(function(i, elem) {
        $.get($(this).attr('data-ajax-url'), function(data) {
            $data = $(data);
            $data.hide();
            $(elem).replaceWith($data);
            $data.fadeIn('fast');
            $data.trigger('loaded.data-ajax', $data);
        });
    });  



    /* MODAL */
    $('body').on('click', 'a[data-toggle="confirm"]', function(event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        var $link = $(event.currentTarget);
        var title = $link.attr('data-confirm-title') || 'Confirm';
        var text = $link.attr('data-confirm-text') || 'Are you sure you want to proceed?';
        var btn1 = $link.attr('data-confirm-btn1') || 'Confirm';
        var btn2 = $link.attr('data-confirm-btn2') || 'Cancel';
        var btn1Class = $link.attr('data-confirm-btn1-class') || 'primary';
        var btn2Class = $link.attr('data-confirm-btn2-class') || 'default';
        var size = $link.attr('data-confirm-size') || false;
        var remote = $link.attr('data-confirm-remote') || false;
        var callback = $link.attr('data-confirm-callback') || false;

        var html = '\
            <div class="modal fade" tabindex="-1" role="dialog">\
                <div class="modal-dialog ' + (size ? 'modal-' + size : '') + '">\
                    <div class="modal-content">';
        if (!remote) {
            if (title != 'false') {
                html += '\
                        <div class="modal-header">\
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                            <h4 class="modal-title">' + title + '</h4>\
                        </div>\
                        <div class="modal-body">\
                            <p>' + text + '</p>\
                        </div>\
                        <div class="modal-footer">\
                            <button type="button" class="btn btn-' + btn1Class + '">' + btn1 + '</button>\
                            <button type="button" class="btn btn-' + btn2Class + '" data-dismiss="modal">' + btn2 + '</button>\
                        </div>';
            }
        }
        html += '   </div>\
                </div>\
            </div>';

        var $modal = $(html);

        $('body').append($modal);

        $modal.modal({
            remote: remote
        });

        $modal.find('.modal-footer button:first').on('click', function(event) {
            $link.removeAttr('data-toggle');
            $link[0].click();
            $link.attr('data-toggle', 'confirm');
            $modal.modal('hide');
        });

        if (callback) {
            $modal.on(remote ? 'loaded.bs.modal' : 'shown.bs.modal', function(event) {
                callback = callback.substring(1);
                var func = callback.split('(')[0];
                var args = callback.split('(').slice(1).join('(').slice(0, -1);
                window[func]($modal, args);
            });
        }
        
        $modal.on('hidden.bs.modal', function(event) {
            $modal.remove();
        });
    });



    /* RELATIONS */
    $('body').on('click', '.relations-menu li a', function(event, target) {
        event.preventDefault();
        event.stopPropagation();

        var $target = $(target || event.currentTarget);

        $.get($target.attr('href'), function(data) {
            $target.closest('.relations-menu').children('button:first').replaceWith(data.button);
            $target.closest('div.relation-type').replaceWith(data.item);
        });
    });
    $('body').on('hide.bs.dropdown', function(event) {
        if ($(event.currentTarget).is('.modal-open')) {
            event.preventDefault();
        }
    });
    $(document).on('click', '.relation-typeahead .dropdown-menu li', function (event) {
        $(event.currentTarget).closest('.relation-typeahead').attr('typeahead-callback', $(event.currentTarget).attr('typeahead-callback'));
    });



    /* SHOW MORE TEXT */
    $('body').on('click', '[show-more-trigger]', function(event) {
        event.preventDefault();

        $(event.currentTarget).parent().slideToggle(300, function() {
            $(event.currentTarget).parent().parent().find('[show-more]').slideToggle(300);
        });
    });
    $('body').on('click', '[show-less-trigger]', function(event) {
        event.preventDefault();

        $(event.currentTarget).parent().slideToggle(300, function() {
            $('html, body').animate({
                scrollTop: $(event.currentTarget).parent().parent().offset().top - 20
            }, 300);
            $(event.currentTarget).parent().parent().find('[show-less]').slideToggle(300);
        });
    });



    /* SLIDESHOW */
    $(document).on('loaded.data-ajax', function(event, data) {
        initSlideshow($(data).find('.cycle-slideshow'));
    });



    /* SPONSORED */
    $(document).on('loaded.data-ajax', function(event, data) {
        initSlideshowSponsored($(data).find('.event-sponsored-dates'));
    });
});