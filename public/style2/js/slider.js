// Initialize Slick Slider for discography section
$(document).ready(function(){
    $('.discography-slider').slick({
        dots: true,
        arrows: true,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true
    });
}); 