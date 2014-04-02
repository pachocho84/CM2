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
            items: 'div.image',
            forcePlaceholderSize: true,
            stop: function() {
                $.each($('.image input[type="hidden"]').get().reverse(), function(i, e) {
                    $(e).val($('.image input[type="hidden"]').length - i);
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
                $('.progress-bar').closest('.objects').removeClass('hidden');
                data.submit();
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                if (progress == 100) {
                    $('.progress .progress-bar').closest('.objects').addClass('hidden').css('width', '0%');
                } else {
                    $('.progress .progress-bar').css('width', progress + '%').find('span').text(progress + '%');
                }
            },
            done: function(e, data) {
                $('.image:last').after(data.result);
            },
            fail: function(e, data) {  
                $('.upload-errors').show().find('ul li:last').clone().fadeIn().prependTo('.upload-errors ul').find('span.upload-errors-file').text(data.files[0].name).parent().find('span.upload-errors-error').text(data.files[0].error);
            }  
        });
    }
});