const listSkeleton = $(".list-skeleton");
const cardCkeleton = $(".card-skeleton");
const showSkeletonCard = () => {
  let numberShow = listSkeleton.data("skeleton");
  numberShow = numberShow ? numberShow : 4;
  for (let i = 0; i < numberShow; i++) {
    console.log(cardCkeleton);
    cardCkeleton.clone().appendTo(listSkeleton);
  }
};
const lazy = $(".lazy-content");
lazy.each(function () {
  let lazyItem = $(this);
  let url = $(this).data("url");
  let name = $(this).data("name");
  let xhtmlBefore;
  let xhtmlComplete = $(".list-skeleton");
  let idSlider = $(this).attr("id");

  let currentSlider = $(`#${idSlider}`);
  $.ajax({
    type: "get",
    url: url,
    data: { action: name },
    dataType: "json",
    beforeSend: function () {
      showSkeletonCard();
    },
    success: function (response) {
      console.log(response);
      let xhtml = response.xhtml ? response.xhtml : "";
      if (xhtml) {
        lazyItem.html(xhtml);
        lazyItem.removeClass("list-skeleton");
        if (idSlider) {
          currentSlider.addClass("rrt-slider");
          addSlider(idSlider);
        }
      }
    },
    complete: function () {},
  });
});
