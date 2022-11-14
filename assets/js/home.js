jQuery(document).ready(function(){
    //if (jQuery('body').hasClass("rtl")) {
    	jQuery('.nm-banner-slider').slick({
            rtl: true,
        	prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-angle-thin-left"></i></a>',
        	nextArrow: '<a class="slick-next"><i class="nm-font nm-font-angle-thin-right"></i></a>',
        	autoplay: true,
            autoplaySpeed: 5000,
        });

        jQuery('.alk-partners').slick({
        	rtl: true,
			adaptiveHeight: true,
			arrows: false,
        	prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-angle-thin-left"></i></a>',
        	nextArrow: '<a class="slick-next"><i class="nm-font nm-font-angle-thin-right"></i></a>',
			dots: false,
			edgeFriction: 0,
			infinite: true,
			speed: 350,
			touchThreshold: 30,
			slidesToShow: 4,
			slidesToScroll: 4,
			//centerMode: true,
			responsive: [
				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
					}
				},
				{
					breakpoint: 518,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2,
					}
				}
			]
        });
    //}
});