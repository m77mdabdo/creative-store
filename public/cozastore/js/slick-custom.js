/* Slick's RTL mode positions slides correctly only when the slider element
   itself carries dir="rtl"; inheriting it from <html> is not enough. */
(function ($) {
    var rtl = $('html').attr('dir') === 'rtl';
    $('.slick1, .slick2, .slick3, .slick-blog').attr('dir', rtl ? 'rtl' : 'ltr');
})(jQuery);

(function ($) {
    "use strict";

    var isRTL = window.isRTL === true || $('html').attr('dir') === 'rtl';

    /*==================================================================
    [ Slick1 ]*/
    $('.wrap-slick1').each(function(){
        var wrapSlick1 = $(this);
        var slick1     = $(this).find('.slick1');
        var itemSlick1 = $(slick1).find('.item-slick1');
        var layerSlick1 = $(slick1).find('.layer-slick1');
        var actionSlick1 = [];

        $(slick1).on('init', function(){
            var layerCurrentItem = $(itemSlick1[0]).find('.layer-slick1');

            for (var i = 0; i < actionSlick1.length; i++) {
                clearTimeout(actionSlick1[i]);
            }

            $(layerSlick1).each(function(){
                $(this).removeClass($(this).data('appear') + ' visible-true');
            });

            for (var i = 0; i < layerCurrentItem.length; i++) {
                actionSlick1[i] = setTimeout(function(index) {
                    $(layerCurrentItem[index]).addClass(
                        $(layerCurrentItem[index]).data('appear') + ' visible-true'
                    );
                }, $(layerCurrentItem[i]).data('delay'), i);
            }
        });

        var showDot = $(wrapSlick1).find('.wrap-slick1-dots').length > 0;

        $(slick1).slick({
            pauseOnFocus: false,
            pauseOnHover: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: false,          // ← fade لازم false مع RTL (Slick bug معروف)
            speed: 1000,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 6000,
            arrows: true,
            rtl: isRTL,           // ← الإضافة الأساسية
            appendArrows: $(wrapSlick1),
            prevArrow: '<button class="arrow-slick1 prev-slick1"><i class="zmdi zmdi-caret-left"></i></button>',
            nextArrow: '<button class="arrow-slick1 next-slick1"><i class="zmdi zmdi-caret-right"></i></button>',
            dots: showDot,
            appendDots: $(wrapSlick1).find('.wrap-slick1-dots'),
            dotsClass: 'slick1-dots',
            customPaging: function(slick, index) {
                var linkThumb = $(slick.$slides[index]).data('thumb');
                var caption   = $(slick.$slides[index]).data('caption');
                return '<img src="' + linkThumb + '">' +
                       '<span class="caption-dots-slick1">' + caption + '</span>';
            },
        });

        $(slick1).on('afterChange', function(event, slick, currentSlide){
            var layerCurrentItem = $(itemSlick1[currentSlide]).find('.layer-slick1');

            for (var i = 0; i < actionSlick1.length; i++) {
                clearTimeout(actionSlick1[i]);
            }

            $(layerSlick1).each(function(){
                $(this).removeClass($(this).data('appear') + ' visible-true');
            });

            for (var i = 0; i < layerCurrentItem.length; i++) {
                actionSlick1[i] = setTimeout(function(index) {
                    $(layerCurrentItem[index]).addClass(
                        $(layerCurrentItem[index]).data('appear') + ' visible-true'
                    );
                }, $(layerCurrentItem[i]).data('delay'), i);
            }
        });
    });

    /*==================================================================
    [ Slick2 ]*/
    $('.wrap-slick2').each(function(){
        $(this).find('.slick2').slick({
            slidesToShow: 4,
            slidesToScroll: 4,
            infinite: false,
            autoplay: false,
            autoplaySpeed: 6000,
            arrows: true,
            rtl: isRTL,           // ← الإضافة
            appendArrows: $(this),
            prevArrow: '<button class="arrow-slick2 prev-slick2"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
            nextArrow: '<button class="arrow-slick2 next-slick2"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
            responsive: [
                { breakpoint: 1200, settings: { slidesToShow: 4, slidesToScroll: 4 } },
                { breakpoint: 992,  settings: { slidesToShow: 3, slidesToScroll: 3 } },
                { breakpoint: 768,  settings: { slidesToShow: 2, slidesToScroll: 2 } },
                { breakpoint: 576,  settings: { slidesToShow: 1, slidesToScroll: 1 } }
            ]
        });
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var nameTab = $(e.target).attr('href');
        $(nameTab).find('.slick2').slick('reinit');
    });

    /*==================================================================
    [ Slick3 ]*/
    $('.wrap-slick3').each(function(){
        $(this).find('.slick3').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: false,          // ← false مع RTL
            infinite: true,
            autoplay: false,
            autoplaySpeed: 6000,
            arrows: true,
            rtl: isRTL,           // ← الإضافة
            appendArrows: $(this).find('.wrap-slick3-arrows'),
            prevArrow: '<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
            nextArrow: '<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
            dots: true,
            appendDots: $(this).find('.wrap-slick3-dots'),
            dotsClass: 'slick3-dots',
            customPaging: function(slick, index) {
                var portrait = $(slick.$slides[index]).data('thumb');
                return '<img src=" ' + portrait + ' "/><div class="slick3-dot-overlay"></div>';
            },
        });
    });

/*==================================================================
[ Slick2 ]
*/
$('.wrap-slick2').each(function () {
    var $wrap  = $(this);
    var $slick = $wrap.find('.slick2');

    if (!$slick.length) return;

    if ($slick.hasClass('slick-initialized')) {
        $slick.slick('unslick');
    }

    $slick.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 1800,
        speed: 500,
        arrows: true,
        dots: true,
        pauseOnHover: true,
        pauseOnFocus: false,
        swipe: true,
        draggable: true,
        rtl: $('html').attr('dir') === 'rtl',

        appendArrows: $wrap,
        appendDots: $wrap.find('.wrap-slick2-dots'),
        dotsClass: 'slick2-dots',

        prevArrow: '<button class="arrow-slick2 prev-slick2"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
        nextArrow: '<button class="arrow-slick2 next-slick2"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',

        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var nameTab = $(e.target).attr('href');
    var $slick = $(nameTab).find('.slick2');

    if ($slick.length && $slick.hasClass('slick-initialized')) {
        $slick.slick('setPosition');
    }
});


/*==================================================================
[ Slick Blog ]
*/
/*==================================================================
[ Slick Blog ]
==================================================================*/
$('.wrap-slick-blog').each(function () {
    var $wrap = $(this);
    var $slick = $wrap.find('.slick-blog');
    var isRTL = $('html').attr('dir') === 'rtl' || window.isRTL === true;

    if (!$slick.length) return;

    if ($slick.hasClass('slick-initialized')) {
        $slick.slick('unslick');
    }

    $slick.slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2500,
        speed: 600,
        arrows: true,
        dots: true,
        pauseOnHover: true,
        pauseOnFocus: false,
        swipe: true,
        draggable: true,
        adaptiveHeight: false,
        rtl: isRTL,
        appendArrows: $wrap,
        appendDots: $wrap.find('.wrap-slick-blog-dots'),
        dotsClass: 'slick-blog-dots',
        prevArrow: '<button class="arrow-slick-blog prev-slick-blog"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
        nextArrow: '<button class="arrow-slick-blog next-slick-blog"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});

/* لو عندك tabs أو resize */
$(window).on('load resize orientationchange', function () {
    $('.slick-blog').each(function () {
        var $this = $(this);
        if ($this.hasClass('slick-initialized')) {
            $this.slick('setPosition');
        }
    });
});

})(jQuery);
