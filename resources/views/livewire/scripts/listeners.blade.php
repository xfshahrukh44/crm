<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('success', function (message) {
            // You can trigger a toastr notification here as well
            toastr.success(message);
        });

        Livewire.on('error', function (message) {
            // You can trigger a toastr notification here as well
            toastr.error(message);
        });

        Livewire.on('emit_select2', function (selector) {
            $(selector).select2();

            $(selector).on('change', function () {
                // alert($(this).data('wire'));
                Livewire.emit('set_select2_field_value', {
                    name: $(this).data('wire'),
                    value: $(this).val()
                });
            })
        });
    });


    function generatePassword() {
        var length = 16,
            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
        for (var i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        return retVal;
    }

    $('body').on('click', '.auth_create', function () {
        var id = $(this).data('id');
        var pass = generatePassword();

        var userInput = prompt("Enter Password", pass);

        // Check if the user clicked "Cancel" or entered an empty value
        if (userInput === null) {

        } else if (userInput === "") {
            console.log("Please enter a password");
        } else {
            Livewire.emit('client_auth_create', {id: id, pass:userInput});
        }
    });

    $('body').on('click', '.auth_update', function () {
        var id = $(this).data('id');
        var pass = generatePassword();

        var userInput = prompt("Enter Password", pass);

        // Check if the user clicked "Cancel" or entered an empty value
        if (userInput === null) {

        } else if (userInput === "") {
            console.log("Please enter a password");
        } else {
            Livewire.emit('client_auth_update', {id: id, pass:userInput});
        }
    });
</script>