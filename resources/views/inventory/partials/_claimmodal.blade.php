<div id="modal_claim_wrapper" class="modal" data-backdrop="true" style="display: none;">
    <div class="row-col h-v">
        <div class="row-cell v-m">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{ Form::open(['id' => 'form-save']) }}
                    <div class="modal-header">
                        <h5 class="modal-title">Claim item</h5>
                    </div>
                    <div class="modal-body">
                        <p>By claiming you agree to the sales terms set by the seller. You understand that within
                            24hours of no confirmation of payment by the seller, this item will be "unclaimed" and be
                            once again available for everyone to claim.
                            Blah blah blah.</p>
                    </div>
                    <div class="modal-footer">
                        <button data-route="{{ $post }}" type="button" class="btn claim" id="btn_confirmed_claim">Confirm Claim!</button>
                        <button type="button" class="btn white" id="btn_confirmed_claim_cancel"
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
    $(function () {
        var btnConfirmedClaimEl = $('#btn_confirmed_claim');
        var btnConfirmedClaimCancelEl = $('#btn_confirmed_claim_cancel');

        btnConfirmedClaimEl.click(function (e) {
            e.preventDefault();
            btnConfirmedClaimCancelEl.hide();
            var that = $(this); // because of clone
            var claimCloneEl = that.clone(true);
            that.addClass('disabled').prop('disabled', true).html('<i class="fa-spinner fa-spin fa"></i>');

            $.ajax({
                url: that.attr('data-route'),
                type: "POST",
                dataType: "json"
            })
                    .done(function (json) {
//                        $('#modal_claim_wrapper').modal('hide');
                        that.html('Success! One moment...');
                        window.location.href = '{{ $redirect }}'
                    })
                    .fail(function (xhr, status, errorThrown) {
                        alert(xhr.responseJSON.message);
                        btnConfirmedClaimCancelEl.show();
                        that.replaceWith(claimCloneEl);
                    });
        });
    })
</script>

@endpush