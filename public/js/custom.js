$('.product-slides').owlCarousel({
  loop: false,
  margin: 10,
  nav: false,
  dots: false,
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

$('.client_testimonials').owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
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

$('.collaps-side').hover(function () {
  var isHovered = $(this).is(':hover')
  if (isHovered) {
    $(this).children('.menu-browse').stop().slideDown(300)
  } else {
    $(this).children('.menu-browse').stop().slideUp(300)
  }
})

$(document).ready(function () {
  $('.fa-angle-up').click(function () {
    // Toggle the visibility of the sibling .info-cycle element
    $(this).siblings('.shop_show').slideToggle('slow')
    $(this).toggleClass('clicked')
  })
})
