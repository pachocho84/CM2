$(function() {
    /* AJAX LINK */
    $(document).on('click', '.ajax-link', function(event) {
        event.preventDefault();
        event.stopPropagation();

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