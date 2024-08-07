$(function() {
    "use strict";
    $(function() {
        $(".preloader").fadeOut()
    });
    jQuery(document).on('click', '.mega-dropdown', function(e) {
        e.stopPropagation()
    });
    var set = function() {
        var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
        var topOffset = 0;
        if (width < 1170) {
            $("body").addClass("mini-sidebar");
            $('.navbar-brand span').hide();
            $(".sidebartoggler i").addClass("ti-menu")
        } else {
            $("body").removeClass("mini-sidebar");
            $('.navbar-brand span').show()
        }
        var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $(".page-wrapper").css("min-height", (height) + "px")
        }
    };
    $(window).ready(set);
    $(window).on("resize", set);
    $(".sidebartoggler").on('click', function() {
        if ($("body").hasClass("mini-sidebar")) {
            $("body").trigger("resize");
            $("body").removeClass("mini-sidebar");
            $('.navbar-brand span').show()
        } else {
            $("body").trigger("resize");
            $("body").addClass("mini-sidebar");
            $('.navbar-brand span').hide()
        }
    });
    $(".nav-toggler").on('click', function() {
        $("body").toggleClass("show-sidebar");
        $(".nav-toggler i").toggleClass("ti-menu");
        $(".nav-toggler i").addClass("ti-close")
    });
    $(".search-box a, .search-box .app-search .srh-btn").on('click', function() {
        $(".app-search").toggle(200)
    });
    $(".right-side-toggle").on('click', function() {
        $(".right-sidebar").slideDown(50);
        $(".right-sidebar").toggleClass("shw-rside")
    });
    $('.floating-labels .form-control').on('focus blur', function(e) {
        $(this).parents('.form-group').toggleClass('focused', (e.type === 'focus' || this.value.length > 0))
    }).trigger('blur');
    $(function() {
        var url = window.location;
        var element = $('ul#sidebarnav a').filter(function() {
            return this.href == url
        }).addClass('active').parent().addClass('active');
        while (!0) {
            if (element.is('li')) {
                element = element.parent().addClass('in').parent().addClass('active')
            } else {
                break
            }
        }
    });
  
})

