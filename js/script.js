$(function() {
    $('.button-show').on('click', function () {
        if($('.left-panel').hasClass('hiden')) {
            $('.button-show').find('i').addClass('fa-rotate-180');
            $('.left-panel').removeClass('hiden');
            $('.left-panel').animate({
                width: "338px",
            })
        } else {
            $('.button-show').find('i').removeClass('fa-rotate-180');
            $('.left-panel').addClass('hiden');
            $('.left-panel').animate({
                width: "60px",
            })
        }

    })
});
