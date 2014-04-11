$(function() {
    $(window).resize(function(event) {
        resizeMessages();
    });



    // load a conversation
    $(document).on('click', 'li.thread a', function(event) {
        event.preventDefault();

        var url = $(event.currentTarget).attr('href');
        $.get(url, function(data) {
            $('#conversation').hide().html(data).fadeIn('fast');

            resizeMessages();

            history.pushState(url, '', url);
        });
    });



    // AJAX form submit
    $(document).on('submit', '#message-new form', function(event) {
        event.preventDefault();

        $(event.currentTarget).ajaxSubmit({
            success: function(data, statusText, xhr, form) {
                $('#messages').append(data);
                resizeMessages();
                $(form).find('textarea').removeAttr('readonly').focus().val('');
            }
        });
    });

    // send message
    $(document).on('keydown', '#message-new form textarea', function(event) {
        if (event.keyCode == '13' && event.shiftKey === false) {
            event.preventDefault();
            if ($(event.currentTarget).val().length >= 1) {
                $(event.currentTarget).attr('readonly', 'readonly');
                $(event.currentTarget).closest('form').submit();
            }
        }
    });
});