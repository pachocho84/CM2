$(function() {
    
    
    
    // Album Cycle
    $('li.album').on('mouseenter', '.album-preview', function(event) { 
        $(this).cycle({ speed: 500, delay: -500, timeout: 500 }); 
    });
    $('li.album').on('mouseleave', '.album-preview', function(event) { 
        $(this).cycle(0).cycle('stop'); 
    });
    
    
    // Album menu
    $('.images').on('mouseenter', 'li.object', function(event) { 
        $(this).closest('.object').find('.btn-group').fadeIn(150);
    });
    $('.images').on('mouseleave', 'li.object', function(event) { 
        $(this).closest('.object').find('.btn-group').hide();
    });
    
    
    // Image overlay
    $('ul.images').on('mouseenter', 'li.image', function(event) { 
        $(this).find('.image-overlay, .image-overlay-content').fadeIn(200); 
    });
    $('ul.images').on('mouseleave', 'li.image', function(event) {
        $(this).find('.image-overlay, .image-overlay-content').fadeOut(150); 
    });
    
    
    
    // Likes & Comments
    $('.image, .album').on('click', ' .bottom-like-count, .bottom-comment-count, .comment_new-show', function(event) {      
        event.preventDefault();
        $('.popover').prev().popover('destroy');
        element = this;
        $.get(script + '/like_comment/' + $(element).closest('.object').attr('data-type') + '/' + $(element).closest('.object').attr('data-id'), function(data) {
            $('body').one('click', function(event) {
                $('.popover').prev().popover('destroy');
            });
            $('body').on('click', '.popover', false);
            $(element).closest('.object').find('.image-container').popover({
                content: data,
                placement: 'bottom',
                html: true
            }).popover('show');
            $(element).closest('.object').find('.popover').css('top', 0);
            if ($(element).hasClass('comment_new-show')) {
                $(element).closest('.object').find('.comment_new').removeClass('hide').find('textarea').focus();
            }
        });
    });
    
    
    
    // Image show
    if ($('#image').length > 0) {   
        // Scroll to image  
/*      $('html, body').scrollTop($('#image').offset().top - 35); */
        $('body').animate({scrollTop : $('#image').offset().top - 35}, 0);
        
        // Navigate throu images with arrows
        $(document).keydown(function(e){
        if (e.keyCode == 37) { 
            $('a.left.carousel-control').click();
        } else if (e.keyCode == 39) { 
            $('a.right.carousel-control').click();
        }
      });
    }
    
    
    
    // Images sort
    if ($('.images-sortable').length > 0) {
        $('.images-sortable form').sortable({ 
            items:      'li',
            stop:       function() {
                console.log($('.image input[type="hidden"]').length);
                $('.image input[type="hidden"]').each(function(i, e) {
                    $(this).val($('.image input[type="hidden"]').length - i);
                });
            }
        }).disableSelection();  
    }
    
    
    
    // Album new
    $('.album-new').on('click', function(event) {
        event.preventDefault();
        $.get(event.currentTarget.href, function(data) {
            $('body').append(data);
            $('#albumNew').modal();
        });
    });
    
    
    // Add images
    if ($('.fileinput-button').length > 0) {
        $('.fileinput-button').fileupload({
            dataType: 'html',
            maxFileSize: 10000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i, 
            add: function(e, data) {
                nb_files = 0;
                nb_files_original = 0;
                for (var i = 0; i < data.originalFiles.length; i++) {
                    if (data.originalFiles[i].error == null) {
                        nb_files_original++;
                    }
                }
                if (nb_files_original > 0) {    
                    $('.bar').closest('.objects').show();
                }
                data.submit();
            },
            done: function(e, data) {
                console.log(2, $('form ul.images'));
                nb_files++;
                $('.image:last').after(data.result);
            },
            always:  function(e, data) {
                $('.bar').css('width', parseInt(nb_files / nb_files_original * 100, 10) + '%');
                if (nb_files == nb_files_original) {
                    $('.bar').closest('.objects').delay(1000).fadeOut('fast', function() { $(this).find('.bar').delay(1000).css('width', '0%') });
                    // if ($('.fileinput-button').attr('data-redirect')) {
                    //     window.location = $('.fileinput-button').attr('data-redirect');
                    // }
                }
            },
            fail: function(e, data) {  
                nb_files_original--;
                $('.upload-errors').show().find('ul li:last').clone().fadeIn().prependTo('.upload-errors ul').find('span.upload-errors-file').text(data.files[0].name).parent().find('span.upload-errors-error').text(data.files[0].error);
            }  
        });
    }
});