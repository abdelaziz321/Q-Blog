
(function () {
  let $ = window.$;
  $(document).ready(function () {
    "use strict";

    // Toggle the side navigation
    $("#sidenavToggler").click(function(e) {
      e.preventDefault();
      $("body").toggleClass("sidenav-toggled");
      $("#sidenavToggler").toggleClass('rotate');
      $(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
      $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
      $(".navbar-sidenav .angle-collapse").toggleClass("d-none");
    });

    // Force the toggled class to be removed when a collapsible nav link is clicked
    $(".navbar-sidenav .nav-link-collapse").click(function(e) {
      e.preventDefault();
      if ($("body").hasClass("sidenav-toggled")) {
        $(".navbar-sidenav .angle-collapse").toggleClass("d-none");
        $("#sidenavToggler").toggleClass('rotate');
        $("body").removeClass("sidenav-toggled");
      }
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .navbar-sidenav, body.fixed-nav .sidenav-toggler, body.fixed-nav .navbar-collapse').on('mousewheel DOMMouseScroll', function(e) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    });
  });
})();