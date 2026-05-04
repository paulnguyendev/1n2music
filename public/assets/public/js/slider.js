// $(".list-track").slick({
//   slidesToShow: 5,
//   slidesToScroll: 1,
//   arrows: true,
//   dots: false,
//   infinite: true,
//   touchMove: true,
//   customPaging: function (slider, i) {
//     return '<span class="dot"></span>';
//   },
//   prevArrow:
//     '<button class="slick-prev"> <i class="far fa-chevron-left"></i> </button>',
//   nextArrow:
//     '<button class="slick-next"> <i class="far fa-chevron-right"></i> </button>',
//   responsive: [
//     {
//       breakpoint: 1024,
//       settings: {
//         slidesToShow: 3,
//         slidesToScroll: 3,
//         infinite: true,
//         dots: true,
//       },
//     },
//     {
//       breakpoint: 600,
//       settings: {
//         slidesToShow: 2,
//         slidesToScroll: 2,
//       },
//     },
//     {
//       breakpoint: 480,
//       settings: {
//         slidesToShow: 1,
//         slidesToScroll: 1,
//       },
//     },
//   ],
// });
// $(".list-producer").slick({
//   slidesToShow: 6,
//   slidesToScroll: 1,
//   arrows: true,
//   dots: false,
//   infinite: true,
//   touchMove: true,
//   customPaging: function (slider, i) {
//     return '<span class="dot"></span>';
//   },
//   prevArrow:
//     '<button class="slick-prev"> <i class="far fa-chevron-left"></i> </button>',
//   nextArrow:
//     '<button class="slick-next"> <i class="far fa-chevron-right"></i> </button>',
//   responsive: [
//     {
//       breakpoint: 1024,
//       settings: {
//         slidesToShow: 3,
//         slidesToScroll: 3,
//         infinite: true,
//         dots: true,
//       },
//     },
//     {
//       breakpoint: 600,
//       settings: {
//         slidesToShow: 2,
//         slidesToScroll: 2,
//       },
//     },
//     {
//       breakpoint: 480,
//       settings: {
//         slidesToShow: 1,
//         slidesToScroll: 1,
//       },
//     },
//   ],
// });
const listSlider = $(".rrt-slick-slider");
listSlider.each((index, value) => {

  let numberShow = $(value).data("number-show");
  let numberShowTablet = $(value).data("number-show-tablet");
  let numberScroll = $(value).data("number-scroll");
  let fade = $(value).data("fade");
  let autoplay = $(value).data("autoplay");
  let dots = $(value).data("dots");
  let arrows = $(value).data("arrows");
  let numberShowMobile   = $(value).data("number-show-mobile");
  dots = dots ? dots : false;


  numberShow = numberShow ? numberShow : 3;
  numberShowTablet = numberShowTablet ? numberShowTablet : 3;
  numberScroll = numberScroll ? numberScroll : 1;
  fade = fade ? fade : false;
  autoplay = autoplay ? autoplay : false;
  numberShowMobile = numberShowMobile ? numberShowMobile : 2;

  $(value).slick({
    slidesToShow: numberShow,
    slidesToScroll: numberScroll,
    arrows: arrows,
    dots: dots,
    infinite: true,
    touchMove: true,
    autoplay:autoplay,
    fade: fade,
    cssEase: 'linear',
    autoplaySpeed: 5000,
    customPaging: function (slider, i) {
      return '<span class="dot"></span>';
    },
    prevArrow:
      '<button class="slick-prev"> <i class="far fa-chevron-left"></i> </button>',
    nextArrow:
      '<button class="slick-next"> <i class="far fa-chevron-right"></i> </button>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: numberShow,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
        },
      },
      {
        breakpoint: 800,
        settings: {
          slidesToShow: numberShowTablet,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: numberShowMobile,
          slidesToScroll: 1,
        },
      },
    ],
  });
});
const addSlider = (ele) => {
  $(`#${ele}`).slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    infinite: true,
    touchMove: true,
    customPaging: function (slider, i) {
      return '<span class="dot"></span>';
    },
    prevArrow:
      '<button class="slick-prev"> <i class="far fa-chevron-left"></i> </button>',
    nextArrow:
      '<button class="slick-next"> <i class="far fa-chevron-right"></i> </button>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
    ],
  });
};
