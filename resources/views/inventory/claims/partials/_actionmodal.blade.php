<div id="modal_claim_accepted" class="modal" data-backdrop="true" style="display: none;">
    <div class="row-col h-v">
        <div class="row-cell v-m">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{ Form::open(['id' => 'form-save']) }}
                    <div class="modal-header">
                        <h5 class="modal-title">Mark as accepted</h5>
                    </div>
                    <div class="modal-body">
                        <textarea name="notes" class="form-control" placeholder="Enter any notes about the transaction here."></textarea>
                        <p class="m-b-0 m-t-1 text-muted text-sm"><em>These notes are only viewable to you.</em></p>
                    </div>
                    <div class="modal-footer">
                        <button data-route="" type="button" class="btn claim p-x-md" id="btn_confirmed_claim">Save</button>
                        <button type="button" class="m-l-1 btn btn-link" id="btn_confirmed_claim_cancel"
                                data-dismiss="modal">Cancel
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modal_claim_rejected" class="modal" data-backdrop="true" style="display: none;">
    <div class="row-col h-v">
        <div class="row-cell v-m">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{ Form::open(['id' => 'form-save']) }}
                    <div class="modal-header">
                        <h5 class="modal-title">Mark as rejected</h5>
                    </div>
                    <div class="modal-body">
                        <textarea name="rejected_reason" class="form-control" placeholder="Optionally let the claimer know why you are rejecting their claimed item."></textarea>
                        <p class="m-b-0 m-t-1 text-muted text-sm"><em>Reminder: After an item is rejected, it is returned to your available inventory.</em></p>
                    </div>
                    <div class="modal-footer">
                        <button data-route="" type="button" class="btn claim p-x-md" id="btn_confirmed_claim">Save</button>
                        <button type="button" class="m-l-1 btn btn-link" id="btn_confirmed_claim_cancel"
                                data-dismiss="modal">Cancel
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('footer-scripts')

<script>
//    $(function () {
//        var markAcceptedEl = $('.btn-action--accepted');
//        var markRejectedEl = $('.btn-action--rejected');
//        var markActionEl = $('.btn-action-claim');

//        markActionEl.click(function (e) {
//            e.preventDefault();
//            btnConfirmedClaimCancelEl.hide();
//            var that = $(this); // because of clone
//            var claimCloneEl = that.clone(true);
//            that.addClass('disabled').prop('disabled', true).html('<i class="fa-spinner fa-spin fa"></i>');

//            $.ajax({
//                url: that.attr('data-route'),
//                type: "POST",
//                dataType: "json"
//            })
//                    .done(function (json) {
////                        $('#modal_claim_wrapper').modal('hide');
//                        that.html('Success! One moment...');
//                        window.location.href = ''
//                    })
//                    .fail(function (xhr, status, errorThrown) {
//                        alert(xhr.responseJSON.message);
//                        btnConfirmedClaimCancelEl.show();
//                        that.replaceWith(claimCloneEl);
//                    });
//        });
//    })
</script>

@endpush