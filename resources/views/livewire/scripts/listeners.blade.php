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

        Livewire.on('emit_select2', function (data) {
            $(data['selector']).select2();

            $(data['selector']).on('change', function () {
                Livewire.emit('set_select2_field_value', {
                    name: data['name'],
                    value: $(this).val()
                });
            })
        });

        Livewire.on('emit_init_tiny_mce', function (data) {
            initTinyMCE(data['selector']);

            // $(data['selector']).on('keyup', function () {
            //     Livewire.emit('set_tiny_mce_field_value', {
            //         name: data['name'],
            //         value: $(this).val()
            //     });
            // })

            // setTimeout(() => {
            //     $('#message_ifr').on('change', function () {
            //         alert();
            //     });
            // }, 4000);
        });

        let img_uploader_init = false;
        Livewire.on('emit_init_image_uploader', function (selector) {
            if (!img_uploader_init) {
                $(selector).imageUploader();
                $('input[name="images[]"]').attr('wire:model', 'message_client_files');

                $('input[name="images[]"]').on('change', function () {
                    var fileData = [];

                    $('input[name="images[]"]').each(function () {
                        if (this.files.length > 0) {
                            for (var i = 0; i < this.files.length; i++) {
                                var file = this.files[i];
                                var reader = new FileReader();

                                // Closure to capture the file information
                                reader.onload = (function(file) {
                                    return function(e) {
                                        fileData.push({
                                            name: file.name,
                                            size: file.size,
                                            type: file.type,
                                            binaryData: e.target.result // Base64 encoded binary data
                                        });

                                        // Emit the file data to Livewire
                                        Livewire.emit('mutate', {
                                            name: 'message_client_files',
                                            value: fileData,
                                        });
                                    };
                                })(file);

                                // Read the file as a data URL (base64 encoded string)
                                reader.readAsDataURL(file);
                            }
                        }
                    });
                });

                img_uploader_init = true;
            }
        });

        Livewire.on('emit_scroll_to_bottom', function (delay) {
            setTimeout(() => {
                $("html, body").animate({ scrollTop: document.body.scrollHeight }, "slow");
            }, delay);
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

    function initTinyMCE(selector) {
        tinymce.init({
            selector: selector,
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    }

    let assign_pending_id = '';
    let assign_pending_form = '';
    let assign_pending_agent_id = '';
    function assignAgentToPending(id, form, brand_id){
        $('#agent-name-wrapper-2').html('');
        var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
        url = url.replace('temp', brand_id);
        $.ajax({
            type:'GET',
            url: url,
            success:function(data) {
                var getData = data.data;
                $('#agent-name-wrapper-2').append('<option value="" selected>Select agent</option>');
                for (var i = 0; i < getData.length; i++) {
                    $('#agent-name-wrapper-2').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                }

                $('#agent-name-wrapper-2').select2();
            }
        });

        assign_pending_id = id;
        assign_pending_form = form;

        $('#agent-name-wrapper-2').on('change', function () {
            if ($(this).val() != '') {
                assign_pending_agent_id = $(this).val();
            }
        });

        $('#btn_assignPendingModel').on('click', function () {
            if (assign_pending_id ==  '') {
                return false;
            }

            Livewire.emit('mutate', {
                name: 'assign_pending_agent_id',
                value: assign_pending_agent_id,
            });
            Livewire.emit('mutate', {
                name: 'assign_pending_id',
                value: assign_pending_id,
            });
            Livewire.emit('mutate', {
                name: 'assign_pending_form',
                value: assign_pending_form,
            });

            $('#assignPendingModel').modal('hide');
            Livewire.emit('assign_pending');
        });

        $('#assignPendingModel').find('#pending_assign_id').val(id);
        $('#assignPendingModel').find('#pending_form_id').val(form);
        $('#assignPendingModel').modal('show');
    }

    let reassign_pending_project_id = '';
    let reassign_pending_agent_id = '';
    function assignAgent(id, form, brand_id){
        $('#agent-name-wrapper').html('');
        var url = "{{ route('get-support-agents', ['brand_id' => 'temp']) }}";
        url = url.replace('temp', brand_id);
        $.ajax({
            type:'GET',
            url: url,
            success:function(data) {
                var getData = data.data;
                $('#agent-name-wrapper').append('<option value="" selected>Select agent</option>');
                for (var i = 0; i < getData.length; i++) {
                    $('#agent-name-wrapper').append('<option value="'+getData[i].id+'">'+getData[i].name+ ' ' +getData[i].last_name+'</option>');
                }

                $('#agent-name-wrapper').select2();
            }
        });

        reassign_pending_project_id = id;

        $('#agent-name-wrapper').on('change', function () {
            if ($(this).val() != '') {
                reassign_pending_agent_id = $(this).val();
            }
        });

        $('#btn_assignModel').on('click', function () {
            if (reassign_pending_agent_id ==  '') {
                return false;
            }

            Livewire.emit('mutate', {
                name: 'reassign_pending_project_id',
                value: reassign_pending_project_id,
            });
            Livewire.emit('mutate', {
                name: 'reassign_pending_agent_id',
                value: reassign_pending_agent_id,
            });

            $('#assignModel').modal('hide');
            Livewire.emit('reassign_pending');
        });

        $('#assignModel').find('#assign_id').val(id);
        $('#assignModel').find('#form_id').val(form);
        $('#assignModel').modal('show');
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
<script>
    g_FileUploadControlCounter = 0;

    function Clicked_h_btnAddFileUploadControl() {
        var v_btnFileUploadControl = document.getElementById("h_btnAddFileUploadControl");
        v_btnFileUploadControl.value = "Add Another Attachment";

        var n="h_Item_Attachments_FileInput[]";
        var z="h_Item_Attachment" + g_FileUploadControlCounter;
        var x = document.createElement("INPUT");

        x.setAttribute("type", "file");
        x.setAttribute("id", z);
        x.setAttribute("name", n);
        x.setAttribute("onchange", "UpdateAttachmentsDisplayList()");
        x.setAttribute("class", "Otr_Std_pad");
        document.getElementById("h_ItemAttachmentControls").appendChild(x);
        g_FileUploadControlCounter++;
    }

    function Clicked_h_hrefRemoveFileUploadControl(v_Item_Attachment) {

        document.getElementById(v_Item_Attachment.id).value = null;
        UpdateAttachmentsDisplayList();
    }

    function UpdateAttachmentsDisplayList() {
        var inputs = document.getElementsByTagName('input');
        var fileData = [];

        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type.toLowerCase() == 'file' && inputs[i].value.length > 0) {
                fileData.push({
                    id: inputs[i].id,
                    value: inputs[i].value
                });

                var x = inputs[i];
                var txt = "<div class='item-attachments-wrapper'><strong>" + inputs[i].value + "</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:Clicked_h_hrefRemoveFileUploadControl(" + x.id + ");'>Delete</a></div>";
                document.getElementById(inputs[i].id).style.visibility = "hidden";
                document.getElementById(inputs[i].id).style.height = "0";
                document.getElementById(inputs[i].id).style.width = "0";
            } else {
                document.getElementById(inputs[i].id).style.visibility = "visible";
            }
        }

        document.getElementById("h_ItemAttachments").innerHTML = txt;

        // Emit file data to Livewire
        console.log(fileData)
        Livewire.emit('mutate', {
            name: 'message_client_files',
            value: fileData,
        });
    }
</script>