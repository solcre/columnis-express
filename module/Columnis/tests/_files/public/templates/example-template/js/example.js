$(document).ready(function() {
    if ($('html').hasClass('no-touch')) {
        windowHeight = $(window).height();
    }

    if (originalDevice != 'desktop') {
        sliderHeaderHeight = 65;
    }

    var sliderPpal = new MasterSlider();

    sliderPpal.control('arrows', {insertTo: '#sliderPpal'});
    sliderPpal.control('bullets');

    sliderPpal.setup('sliderPpal', {
        autoplay: true,
        width: 1440,
        height: 900,
        space: 5,
        view: 'basic',
        layout: 'fullscreen',
        fullscreenMargin: sliderHeaderHeight,
        speed: 20,
        loop: true
    });
    
    if (device == 'desktop' && $('html').hasClass('no-touch')) {
        headerOffset = $(window).height();
        $(window).scroll(fixHeader);
    }

}) // document ready

var windowHeight = 0;
var sliderHeaderHeight = 0;
var headerOffset = 20;

function fixHeader() {

    if ($(window).scrollTop() < headerOffset ) {
        $('body').removeClass('fixedHeader');
    } else {
        $('body').addClass('fixedHeader');
    }
}
