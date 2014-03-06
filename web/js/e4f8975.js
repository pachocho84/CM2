$(function() { 
    
    /* EQUAL HEIGHT COLUMN */     
    // $('.row:has(.column)').each(function() {
    //     $(this).find('.column').css({ 'height': $(this).height() })
    // });
    
    /* EVENTS */
    $('.events_box-events-container').cycle({
        fx:         'scrollVert',           
        next:   $('#events_box-next'),
        pause:  true,
        prev:   $('#events_box-prev'),
        rev:        true
    });                 
                          
    /* BOX HOME SLIDE 3 */  
    $('.box_home-slide3').each(function() {  
        var next = $(this).find('.box_home-next');
        var prev = $(this).find('.box_home-prev');
        $(this).find('.box_home-oggetti-container').cycle({
            fx:         'scrollHorz', 
            speed:      500, 
            timeout:    0, 
            next:       next, 
            prev:       prev 
        }); 
    });    
    
    /* BOX HOME FADE 1 */ 
    $('.box_home-fade1').each(function() {  
        var next = $(this).find('.box_home-next');
        var prev = $(this).find('.box_home-prev');
        $(this).find('.box_home-oggetti').cycle({
            fx:         'fade', 
            speed:      500, 
            timeout:    4000,  
            next:       next, 
            pause:      true,                    
            prev:       prev 
        }); 
    });    
                          
    /* LAST BACHECA POSTS */
    $('#last_bacheca_posts_box-container > .oggetto').on({
        mouseenter: function() {                                                         
            $('#last_bacheca_posts_box-container .post-popup-container').remove();                                              
            var post = $(this);
            call = $.get($(this).find('.post-link').attr('href'), function(data) {  
                $(post).append('<div class="post-popup-container"><div class="border-notch notch"></div><div class="notch"></div><div class="post-popup">' + data + '</div></div>');
                $(post).find('.post-popup-container').fadeIn('fast');
            });        
        },
        mouseleave: function() {
            call.abort();                                     
            $(this).find('.post-popup-container').fadeOut('fast', function() { $(this).remove(); }); 
        } 
    });  
        
                         
    
    
    /* PARTNER BOX */
    $('.partner_box .partner_box-right .partner_box-ticker').cycle({
        fx:         'scrollVert',            
        pause:  true,                    
        rev:        true
    }); 
    $('.partner_box .partner_box-left').cycle({
        fx:         'fade',                  
        pause:  true
    });
    
    /*$('.row .immagine img').draggable({ 
        axis: 'y',
        //containment: $(this).parents('.immagine') 
    });*/                                                                         
    
                         
    
    /* PHOTO GALLERY */
    $('#photo_gallery #photo_gallery-photos-container').cycle({
        fx:             'scrollHorz', 
    speed:      500, 
    timeout:    0, 
    next:       '#photo_gallery-prev', 
    prev:       '#photo_gallery-next',
        rev:        true  
    });                      
    
    $('#photo_gallery a.photo_gallery-photo-link').colorbox({  
        current:            "immagine {current} di {total}",
        previous:       'precedente',
        next:               'successiva',
        close:              'chiudi',
        opacity:            0.7,
        rel:                    'photo_gallery-photo-link',        
        transition:     'none'
    });    
            
    
    
    /* VIDEO GALLERY */ 
    $('#video_gallery #video_gallery-photos-container').cycle({
        fx:             'scrollHorz', 
    speed:      500, 
    timeout:    0, 
    next:       '#video_gallery-prev', 
    prev:       '#video_gallery-next',
        rev:        true  
    });              
    /*var current_video_gallery = 0;
    var count_video_galleries = $('.video_gallery-videos').length;     
         
    $('.video_gallery-videos:gt('+current_video_gallery+')').css({ position: 'absolute', left: 633, top: 0 });     
    $('#video_gallery-prev').live('click', function() { 
        if (current_video_gallery > 0)
        {                                                              
            $('.video_gallery-videos:eq('+current_video_gallery+')').css({ position: 'absolute' }).animate({ left: 633 }, 750);  
            current_video_gallery--;                                         
            $('.video_gallery-videos:eq('+current_video_gallery+')').css({ position: 'absolute' }).animate({ left: 0 }, 750);
        }
        return false;
    });   
    $('#video_gallery-next').live('click', function() {
        if (current_video_gallery < count_video_galleries-1)  
        {                                                               
            $('.video_gallery-videos:eq('+current_video_gallery+')').css({ position: 'absolute' }).animate({ left: -633 }, 750);  
            current_video_gallery++;                                         
            $('.video_gallery-videos:eq('+current_video_gallery+')').css({ position: 'absolute' }).animate({ left: 0 }, 750);
        }
        return false;
    }); */   
    
    $('#video_gallery a.video_gallery-video').colorbox({  
        current:            "video {current} di {total}",
        previous:       'precedente',
        next:               'successivo',
        close:              'chiudi',
        opacity:            0.7,
        rel:                    'video_gallery-video',        
        transition:     'none'
    });    
    
    
    
    /* LOGIN */
    // Default Username & Password
    var passwordField = $('#login_box input[name="signin[password]"]');
    var usernameField = $('#login_box input[name="signin[username]"]');
    var usernameFieldDefault = usernameField.val();
    passwordField.after('<input id="passwordPlaceholder" type="text" value="Password" autocomplete="off" class="form-input form-placeholder" />');
    var passwordPlaceholder = $('#passwordPlaceholder');
    passwordPlaceholder.show();
    passwordField.hide();
    passwordPlaceholder.focus(function() {
        passwordPlaceholder.hide();
        passwordField.show().focus();
    });
    passwordField.blur(function() {
        if(passwordField.val() == '') {
            passwordPlaceholder.show();
            passwordField.hide();
        }
    });
    usernameField.focus(function() {
        if(usernameField.val() == usernameFieldDefault) { usernameField.val(''); }
        $(this).removeClass('form-placeholder');
    });
    usernameField.blur(function() {
        if(usernameField.val() == '') { usernameField.val(usernameFieldDefault); } 
        $(this).addClass('form-placeholder');                  
    });    
                   
    
                            
});