<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
  integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<script>
  $('.dropify').dropify();
</script>

<script>
    
$(document).ready(function () {
    $('.open-side-menu').on('click', function (e) {
        e.preventDefault(); // Prevent default action if it's a link or button
        $('.side-menu').toggleClass('active');
        $('.for-slider-main-banner').toggleClass('active');
    });
});
</script>

<script type="text/javascript">
  const progress = document.querySelector('.progress-done');

  progress.style.width = progress.getAttribute('data-done') + '%';
  progress.style.opacity = 1;


</script>
</body>

</html>