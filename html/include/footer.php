<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
        <script>
    var selector = '.main-id-date p';

    $(selector).on('click', function () {
        $(selector).removeClass('active');
        $(this).addClass('active');
    });
</script>

<script>
  // Wait for the DOM to load
  document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('redirectButton');
    
    if (button) {
      button.addEventListener('click', function() {
        const currentPath = window.location.pathname;

        // Check if we're on 'index.php' (or root page)
        if (currentPath === '/index.php' || currentPath === '/' || currentPath.endsWith('/index.php')) {
          // Redirect to 'messages.php'
          window.location.href = "messages.php";
        } 
        // Check if we're on 'messages.php'
        else if (currentPath === '/messages.php' || currentPath.endsWith('/messages.php')) {
          // Redirect to 'index.php'
          window.location.href = "index.php";
        }
      });
    }
  });
</script>


</body>

</html>