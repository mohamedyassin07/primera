jQuery(document).ready(function () {
    //initialize swiper when document ready
    var mySwiper = new Swiper ('.swiper-container', {
	slidesPerView: 3,
	freeMode: true,
	freeModeSticky: true,
	spaceBetween: 5,
	passiveListeners: false,
	resistance: false,
	touchReleaseOnEdges: false,
    })

	jQuery('.nm-banner-slider').slick({
            rtl: true,
            arrows: false,
        	prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-angle-thin-left"></i></a>',
        	nextArrow: '<a class="slick-next"><i class="nm-font nm-font-angle-thin-right"></i></a>',
        	autoplay: true,
            autoplaySpeed: 5000,
        });
  });