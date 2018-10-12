// Main Gallery
$('.business-image-gallery').slick({
    arrows: false,
    infinite: true,
    speed: 300,
    fade: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    adaptiveHeight: true,
    autoplay: true,
    autoplaySpeed: 5000,
    asNavFor: '.business-image-carousel'
});


// Carousel
$('.business-image-carousel').slick({
    arrows: true,
    infinite: true,
    speed: 300,
    slidesToShow: 6,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 5000,
    asNavFor: '.business-image-gallery',
    focusOnSelect: true,
    responsive: [
        {
            breakpoint: 950,
            settings: {
                slidesToShow: 4
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 3
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2
            }
        },
    ]
});