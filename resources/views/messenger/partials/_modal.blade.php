
<div class="modal fade" id="kbdl_modal_messenger">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Create new message</h6>
            </div>
            <div class="modal-body">
                @include('messenger.partials._formfields')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn primary btn-sm btn-messenger" id="modal-btn-messenger-send">Send</button>
                <button type="button" class="btn white btn-sm btn-messenger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function(){
        let $messengerBtnSend = $('#modal-btn-messenger-send');
        $messengerBtnSend.click(function(event){
            event.preventDefault();
            let $messengerBtns = $('.btn-messenger');
            $messengerBtns.addClass('disabled');
            $messengerBtns.prop('disabled', true);
            $messengerBtnSend.html('Sending... ' + (spinny()));

            $.ajax({
                url : '{{ apiRoute('messenger.store') }}',
                type: 'POST',
                data: {
                    message: 'Hello',
                    recipients: [9872295,9872318,9872319,9872320,9872321],
                    subject: 'when I walk out on stage.'
                }
            }).success(function(){

            }).error(function(){

            }).always(function(){
                $messengerBtns.removeClass('disabled');
                $messengerBtns.prop('disabled', false);
                $messengerBtnSend.html('Send');
            });
        });
    });
</script>