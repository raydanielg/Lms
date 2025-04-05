(function ($) {
  "use strict";

  var defaultBreakpoints = {
    991: {
      slidesPerView: 3
    },
    660: {
      slidesPerView: 2
    }
  };
  var sliders = [{
    container: 'features-swiper-container',
    pagination: "features-swiper-pagination",
    breakpoints: false
  }, {
    container: 'upcoming-courses-swiper',
    pagination: "upcoming-courses-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'latest-webinars-swiper',
    pagination: "latest-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'latest-bundle-swiper',
    pagination: "bundle-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'best-sales-webinars-swiper',
    pagination: "best-sales-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'best-rates-webinars-swiper',
    pagination: "best-rates-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'has-discount-webinars-swiper',
    pagination: "has-discount-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'free-webinars-swiper',
    pagination: "free-webinars-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'new-products-swiper',
    pagination: "new-products-swiper-pagination",
    breakpoints: {
      1200: {
        slidesPerView: 4
      },
      991: {
        slidesPerView: 3
      },
      660: {
        slidesPerView: 2
      }
    }
  }, {
    container: 'testimonials-swiper',
    pagination: "testimonials-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'subscribes-swiper',
    pagination: "subscribes-swiper-pagination",
    breakpoints: defaultBreakpoints
  }, {
    container: 'organization-swiper-container',
    pagination: "organization-swiper-pagination",
    breakpoints: {
      991: {
        slidesPerView: 4
      },
      660: {
        slidesPerView: 2
      }
    }
  }, {
    container: 'trend-categories-swiper',
    pagination: "trend-categories-swiper-pagination",
    breakpoints: {
      1200: {
        slidesPerView: 6
      },
      991: {
        slidesPerView: 4
      },
      660: {
        slidesPerView: 2
      }
    }
  }];

  var _loop = function _loop() {
    var slider = _sliders[_i];
    var swip = new Swiper('.' + slider.container, {
      slidesPerView: 1,
      spaceBetween: 16,
      loop: false,
      autoplay: {
        delay: 5000,
        disableOnInteraction: true,
        pauseOnMouseEnter: true
      },
      pagination: {
        el: '.' + slider.pagination,
        clickable: true
      },
      breakpoints: slider.breakpoints
    });
    var $el = $("." + slider.container);
    $el.mouseenter(function () {
      swip.autoplay.stop();
    });
    $el.mouseleave(function () {
      swip.autoplay.start();
    });
  };

  for (var _i = 0, _sliders = sliders; _i < _sliders.length; _i++) {
    _loop();
  }

  $('.instructors-swiper-container').owlCarousel({
    loop: true,
    center: true,
    items: 3,
    margin: 0,
    autoplay: true,
    dots: true,
    autoplayTimeout: 5000,
    smartSpeed: 450,
    responsive: {
      0: {
        items: 1
      },
      768: {
        items: 2
      },
      1170: {
        items: 4
      }
    }
  });
  $(document).ready(function () {
    for (var i = 1; i <= 6; i++) {
      new Parallax(document.getElementById('parallax' + i), {
        relativeInput: true
      });
    }
  });
})(jQuery);
