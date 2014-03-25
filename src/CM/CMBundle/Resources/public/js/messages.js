$(function() {
    $(window).resize(function(event) {
        var messageHeight = document.getElementById('body').clientHeight - getPosition(document.getElementById('message-interface')).top - 1;
        document.getElementById('message-interface').style.height = messageHeight + 'px';
    });
});