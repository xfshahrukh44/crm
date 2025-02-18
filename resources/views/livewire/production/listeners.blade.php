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

        // -------------------------project detail scripts-------------------------
        $('body').on('click', '.btn_read_more', function () {
            $('#fancybox-content').html($(this).data('text'));
            $('#fancybox_modal').modal('show');

            $('#fancybox_modal_user_name').html($(this).data('user'));
            $('#fancybox_modal_message_date').html($(this).data('time'));
        });

        let subtask_id = '';
        $('body').on('click', '.btn_assign_subtask', function () {
            subtask_id = $(this).data('subtask');
            $('#exampleModalCenter').modal('show');
        });

        $('body').on('click', '#btn_save_changes_assign_subtask', function () {
            let val = $('#assign_subtask_user_id').val();
            let comment = $('#assign_subtask_comment').val() ?? '';
            if (val == '') {
                alert('Please select a valid option');
                return false;
            }

            $('#exampleModalCenter').modal('hide');
            $('#assign_subtask_user_id').val('');
            $('#assign_subtask_comment').val('');

            Livewire.emit('assign_subtask', {
                subtask_id: subtask_id,
                member_id: val,
                comment: comment,
            });
            return false;
        });

        $('body').on('click', '#btn_send_message', function () {
            if (typeof Livewire !== 'undefined' && Livewire.emit) {
                let val = $('#textarea_send_message').val();
                if (val === '') return false;

                Livewire.emit('send_message', {
                    task_id: $(this).data('project'),
                    message: val
                });

                $('#textarea_send_message').val('');
            } else {
                setTimeout(() => {
                    alert('agn');
                    $(this).trigger('click');
                }, 500);
            }
        });

        $('body').on('click', '#btn_download_all_files', function () {
            $('.anchor_test').each((i, item) => {
                item.click();
            });
        });

        $('body').on('click', '.btn_clear_subtask_notification', function () {
            let val = $(this).data('notification');

            Livewire.emit('clear_subtask_notification', {
                notification_id: val
            });

            return false;
        });

        $('body').on('click', '#btn_search_messages', function () {
            $('#input_search_messages').val('');
            $('#search_messages_modal').modal('show');
        });

        $('body').on('shown.bs.modal', '#search_messages_modal', function () {
            $('#input_search_messages').focus();
        });

        $('body').on('keyup', '#input_search_messages', function (e) {
            let val = $(this).val();
            if ((e.key === "Enter" || e.keyCode === 13) && val !== '') {
                if(val === '') {
                    return false;
                }

                $('#search_messages_modal').modal('hide');
                Livewire.emit('set_search_message_query', val);
            }

            return false;
        });

        function get_status(status) {
            if(status == 0){
                return "<span class='badge badge-danger badge-sm'>Open</span>";
            }else if(status == 1){
                return "<span class='badge badge-primary badge-sm'>Re Open</span>";
            }else if(status == 2){
                return "<span class='badge badge-info badge-sm'>Hold</span>";
            }else if(status == 3){
                return "<span class='badge badge-success badge-sm'>Completed</span>";
            }else if(status == 4){
                return "<span class='badge badge-warning badge-sm'>In Progress</span>";
            }else if(status == 5){
                return "<span class='badge badge-info badge-sm'>Sent for Approval</span>";
            }else if(status == 6){
                return "<span class='badge badge-warning badge-sm'>Incomplete Brief</span>";
            }
        }

        $('body').on('click', '.btn_view_assigned_members', function () {
            let members = $(this).data('members') ?? [];

            $('#modal_tbody_assigned_members').html('');

            for (const member of members) {
                let name = member.assigned_to_user.name + ' ' + member.assigned_to_user.last_name;
                let status = get_status(member.status);

                $('#modal_tbody_assigned_members').append(`<tr>
                                                                <td>`+name+`</td>
                                                                <td>
                                                                    `+status+`
                                                                </td>
                                                            </tr>`);
            }

            $('#modal_assigned_members').modal('show');
        });

        $('body').on('click', '#btn_upload', function () {
            // $('.anchor_test').each((i, item) => {
            //     item.click();
            // });
        });
        // -------------------------project detail scripts-------------------------
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
