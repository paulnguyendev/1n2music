const listContainer = $(".market-list-track");
listContainer.each(function () {
  let lazyItem = $(this);
  let url = $(this).data("url");
  let name = $(this).data("name");
  let xhtmlBefore;
  let xhtmlComplete = $(".list-skeleton");
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
      let total = response.total ? response.total : 0;
      let take = response.take ? response.take : 0;
      let xhtml = response.xhtml ? response.xhtml : "No data";
      if (xhtml) {
        lazyItem.html(xhtml);
        lazyItem.removeClass("list-skeleton");
        handleMarketPage();
      }
      if (total == 0 || total < take) {
        $("#btnLoadTrack").hide();
      }
    },
    complete: function () {},
  });
});
const handleMarketPage = () => {
  const btnDownloadFree = $(".track-meta-free");
  btnDownloadFree.click(function () {
    alert("Feature is being updated");
  });
  const btnLoadTrack = $("#btnLoadTrack");
  let skip = $(".market-list-track").data("skip");

  btnLoadTrack.click(function () {
    let url = $(".market-list-track").data("url");
    let userID = $(this).data("user");
    $.ajax({
      type: "get",
      url: url,
      data: {
        action: "showLoadMore",
        skip: skip,
        user_id: userID,
      },
      dataType: "json",
      beforeSend: function () {
        showLoading();
      },
      success: function (response) {
        skip = skip + 5;
        let xhtml = response.xhtml ? response.xhtml : "";
        if (xhtml) {
          listContainer.append(xhtml);
        } else {
          btnLoadTrack.hide();
        }

        console.log(xhtml);
      },
      complete: function () {
        hideLoading();
      },
    });
  });
  const checkScroll = debounce(() => {
    const container = $(".market-list-track");
    const sectionTracks = $(".section-tracks");
    const footer = $("#footer");
    var allowedAreaTop = sectionTracks.offset().top;
    var allowedAreaBottom = footer.offset().top;
    var windowScrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    if (
      windowScrollTop + windowHeight > allowedAreaBottom &&
      windowScrollTop < allowedAreaTop
    ) {
      loadData(container);
    }
  }, 1000);
};
