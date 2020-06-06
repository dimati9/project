$(function() {
    $('.button-show').on('click', function () {
        if($('.left-panel').hasClass('hiden')) {
            $('.button-show').find('i').addClass('fa-rotate-180');
            $('.left-panel').removeClass('hiden');
            $('.button-show').find('i').css('opacity', 0);
            $('.left-panel').animate({
                width: "338px",
            })
            $('.button-show').find('i').animate({
                opacity: "1",
            })
        } else {
            $('.button-show').find('i').css('opacity', 0);
            $('.button-show').find('i').removeClass('fa-rotate-180');
            $('.left-panel').addClass('hiden');
            $('.left-panel').animate({
                width: "60px",
            })
            $('.button-show').find('i').animate({
                opacity: "1",
            })
        }

    })
});
