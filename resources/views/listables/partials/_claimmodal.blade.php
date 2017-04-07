<div id="modal_claim_wrapper" class="modal" data-backdrop="true" style="display: none;">
    <div class="row-col h-v">
        <div class="row-cell v-m">
            <div class="modal-dialog">
                <div class="modal-content">
                    @if(webUser())
                        {{ Form::open(['id' => 'form-save']) }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            @if(webUser()->id == $listable->owner->id)
                                <h5 class="modal-title">Who are you claiming this item for?</h5>
                            @else
                                <h5 class="modal-title">Claim item</h5>
                            @endif
                        </div>
                        <div class="modal-body">
                            @if(webUser()->id == $listable->owner->id)
                                <owner-claim
                                    search_endpoint="{{ apiRoute('users.search') }}"
                                    endpoint="{{ apiRoute('listables.claims.store', [$listable->user->username, $listable->id]) }}"
                                    claim-redirect="{{ $redirect }}"
                                    convert-endpoint="{{ route('auth.guest-convert') }}"
                                    csrf="{{ csrf_token() }}"
                                ></owner-claim>
                            @else
                                <p>By claiming you agree to the sales terms set by the seller. You understand that within
                                    24hours of no confirmation of payment by the seller, this item will be "unclaimed" and be
                                    once again available for everyone to claim.
                                    Blah blah blah.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            @if(webUser()->id != $listable->owner->id)
                                <button data-route="{{ $post }}" type="button" class="btn claim" id="btn_confirmed_claim">Confirm Claim!</button>
                            @endif
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
                        <div class="modal-body" style="max-height: 350px; overflow-y: auto;">
                            <check-in
                                check-in-type=null
                                request-type="api"
                                sign-in-web-route="{{ route('auth.login.store') }}"
                                sign-in-api-endpoint="{{ apiRoute('auth.login.store') }}"
                                password-reset-route="{{ route('auth.password.reset.index') }}"
                                register-route="{{ route('auth.register') }}"
                                claim-endpoint="{{ $guestClaimEndpoint }}"
                                convert-endpoint="{{ route('auth.guest-convert') }}"
                                csrf="{{ csrf_token() }}"
                                redirect="{{ $redirect }}"
                            ></check-in>
                        </div>
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