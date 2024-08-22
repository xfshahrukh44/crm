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

            $(data['selector']).on('change', function () {
                // alert($(this).data('wire'));
                Livewire.emit('set_tiny_mce_field_value', {
                    name: data['name'],
                    value: $(this).val()
                });
            })
        });

        let img_uploader_init = false;
        Livewire.on('emit_init_image_uploader', function (selector) {
            if (!img_uploader_init) {
                $(selector).imageUploader();
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
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
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
                    tinymce.get('editmessage').setContent(data.data.message);
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
        var txt='';

        for(var i = 0; i < inputs.length; i++) {
            if(inputs[i].type.toLowerCase() == 'file') {
                if(inputs[i].value.length > 0)
                {
                    var x = inputs[i];
                    txt += "<div class='item-attachments-wrapper'><strong>" + inputs[i].value + "</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:Clicked_h_hrefRemoveFileUploadControl(" + x.id + ");'>Delete</a></div>";
                    document.getElementById(inputs[i].id).style.visibility = "hidden";
                    document.getElementById(inputs[i].id).style.height = "0";
                    document.getElementById(inputs[i].id).style.width = "0";
                }else{
                    document.getElementById(inputs[i].id).style.visibility = "visible";
                }
            }
            document.getElementById("h_ItemAttachments").innerHTML = txt;
        }
    }
</script>