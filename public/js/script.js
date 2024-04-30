$('.main-hovers').hover(function () {
  var isHovered = $(this).is(':hover')
  if (isHovered) {
    $(this).children('.sub_menu').stop().slideDown(100)
  } else {
    $(this).children('.sub_menu').stop().slideUp(100)
  }
})

$('.logo-slides').owlCarousel({
  loop: true,
  margin: 10,
  nav: false,
  dots: false,
  responsive: {
    0: {
      items: 1
    },
    600: {
      items: 6
    },
    1000: {
      items: 6
    }
  }
})

$('.ghostwriting-slides').owlCarousel({
  loop: false,
  margin: 10,
  nav: false,
  dots: true,
  responsive: {
    0: {
      items: 1
    },
    600: {
      items: 4
    },
    1000: {
      items: 4
    }
  }
})

$('.testimonial-slides').owlCarousel({
  loop: true,
  margin: 10,
  nav: false,
  dots: true,
  responsive: {
    0: {
      items: 1
    },
    600: {
      items: 1
    },
    1000: {
      items: 1
    }
  }
})

$('.reality-slides').owlCarousel({
  loop: false,
  margin: 10,
  nav: false,
  dots: true,
  responsive: {
    0: {
      items: 1
    },
    600: {
      items: 3
    },
    1000: {
      items: 3
    }
  }
})

$(document).ready(function () {
  $('.tab-a').click(function () {
    $('.tab').removeClass('tab-active')
    $(".tab[data-id='" + $(this).attr('data-id') + "']").addClass('tab-active')
    $('.tab-a').removeClass('active-a')
    $(this).parent().find('.tab-a').addClass('active-a')
  })
})
