<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/v342h96m9l2d2xvl69w2yxp6fwd33xvey1c4h3do99vwwpt2/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="{{ asset('newglobal/js/image-uploader-2.min.js') }}"></script>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('success', function (message) {
            toastr.success(message);
        });

        Livewire.on('error', function (message) {
            toastr.error(message);
        });

        Livewire.on('emit_pre_render', function () {
            window.scrollTo(0, 0);
        });

        Livewire.on('emit_scroll_to_bottom', function (delay) {
            setTimeout(() => {
                $("html, body").animate({ scrollTop: document.body.scrollHeight }, "slow");
            }, delay);
        });

        Livewire.on('emit_select2', function (data) {
            $(data['selector']).select2();

            $(data['selector']).on('change', function () {
                Livewire.emit('set_select2_field_value', {
                    name: data['name'],
                    value: $(this).val()
                });
            })
        });

        Livewire.on('scroll_to_bottom', function (selector) {
            let el = document.getElementById(selector);
            if (el) el.scrollTop = el.scrollHeight;
        });

        Livewire.on('copy_link', function (data) {
            // Create a temporary textarea to hold the link
            var tempInput = document.createElement("textarea");
            tempInput.value = data; // Assign the link to the textarea
            document.body.appendChild(tempInput); // Append textarea to body (temporarily)

            tempInput.select(); // Select the text
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy"); // Copy to clipboard

            document.body.removeChild(tempInput); // Remove the temporary textarea

            toastr.success('Link copied to clipboard!');
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

    @php
        $services = \App\Models\Service::all();
    @endphp
    @if($services)
        let service_map = {};
        @foreach($services as $service)
            service_map['{{$service->id}}'] = '{{$service->name}}';
        @endforeach

        let tickbox_array = {};

        $('body').on('change', '#service', function () {
            $('#show_service_form_checkboxes').html('');
            $('#tickboxes_wrapper').html('');
            tickbox_array = {};
            let service_ids = $(this).val();

            tickbox_array['on'] = [];
            for (const service_id of service_ids) {
                $('#show_service_form_checkboxes').append(`<div class="col-md-12">
                                                                <input type="checkbox" data-id="`+service_id+`" data-name="`+service_map[service_id]+`" class="service_tickbox" value="`+service_id+`" checked>
                                                                <label for="service_`+service_id+`">`+service_map[service_id]+`</label>
                                                            </div>`);

                tickbox_array['on'].push(service_id);
                $('#tickboxes_wrapper').append(`<input type="hidden" wire:model="client_payment_create_show_service_forms[on][]" value="`+service_id+`">`);
            }

            Livewire.emit('mutate', {
                name: 'client_payment_create_show_service_forms',
                value: tickbox_array,
            });
        });

        $('body').on('change', '.service_tickbox', function () {
            $('#tickboxes_wrapper').html('');
            tickbox_array = {};
            tickbox_array['on'] = [];
            tickbox_array['off'] = [];

            $('.service_tickbox').each((i, item) => {
                if ($(item).is(':checked')) {
                    tickbox_array['on'].push($(item).data('id'));
                    $('#tickboxes_wrapper').append(`<input type="hidden" wire:model="client_payment_create_show_service_forms[on][]" value="`+$(item).data('id')+`">`);
                } else {
                    tickbox_array['off'].push($(item).data('id'));
                    $('#tickboxes_wrapper').append(`<input type="hidden" wire:model="client_payment_create_show_service_forms[off][]" value="`+$(item).data('id')+`">`);
                }
            });

            Livewire.emit('mutate', {
                name: 'client_payment_create_show_service_forms',
                value: tickbox_array,
            });

        });
    @endif

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

    $('body').on('click', '.span_client_project_status_badge', function () {
        let dropdown = $('#project_statusDropdown');
        dropdown.css('display', dropdown.css('display') === "none" ? "block" : "none");
    });

    document.addEventListener("click", function (event) {
        $('#project_statusDropdown').css('display', dropdown.css('display') === "none" ? "block" : "none");
    });

    $('body').on('click', '.badge_select_project_status', function () {
        let project_id = $(this).data('project');
        let value = $(this).data('value');

        Livewire.emit('set_project_priority', {
            project_id: project_id,
            value: value
        });
    });
</script>

<script>
    function editMessage(message_id){
        var url = "{{ route('support.message.edit', ":message_id") }}";
        url = url.replace(':message_id', message_id);
        $.ajax({
            type:'GET',
            url: url,
            success:function(data) {
                if(data.success){
                    // CKEDITOR.instances['editmessage'].setData(data.data.message);
                    $('#editmessage').summernote('code', data.data.message);
                    $('#exampleModalMessageEdit').find('#message_id').val(data.data.id);
                    $('#exampleModalMessageEdit').modal('toggle');
                    console.log();
                }
            }
        });
    }
</script>
