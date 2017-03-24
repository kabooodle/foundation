<div id="modal_claim_wrapper" class="modal" data-backdrop="true" style="display: none;">
    <div class="row-col h-v">
        <div class="row-cell v-m">
            <div class="modal-dialog">
                <div class="modal-content">
                    @if(user())
                        {{ Form::open(['id' => 'form-save']) }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
                    @else
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h5 class="modal-title text-center">Please tell us who's making the claim</h5>
                        </div>
                        <div class="modal-body">
                            <check-in
                                check-in-type=null
                                sign-in-route="{{ route('auth.login.store') }}"
                                password-reset-route="{{ route('auth.password.reset.index') }}"
                                register-route="{{ route('auth.register.store') }}"
                                guest-claim-endpoint="{{ $guestClaimEndpoint }}"
                                guest-convert-endpoint="{{ route('auth.guest-convert') }}"
                                csrf="{{ csrf_token() }}"
                                redirect="{{ $redirect }}"
                            ></check-in>
                        </div>
                        {{--<div class="modal-footer">--}}
                            {{--<button type="button" class="btn white" id="btn_confirmed_claim_cancel"--}}
                                    {{--data-dismiss="modal">Cancel--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    @endif
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
            var that = $(this);
            var claimCloneEl = that.clone(true);
            that.addClass('disabled').prop('disabled', true).html('<i class="fa-spinner fa-spin fa"></i>');

            $.ajax({
                url: that.attr('data-route'),
                type: "POST",
                dataType: "json"
            })
                .done(function (json) {
                    that.html('Success! One moment...');
                    window.location.href = '{{ $redirect }}'
                })
                .fail(function (xhr, status, errorThrown) {
                    if (xhr.responseJSON.status_code == 401) {
                        alert('You must be signed in to claim items.');
                    } else {
                        alert(xhr.responseJSON.message);
                    }
                    btnConfirmedClaimCancelEl.show();
                    that.replaceWith(claimCloneEl);
                });
        });
    });
</script>

@endpush