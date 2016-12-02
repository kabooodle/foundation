
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Accepted Price</label>
                                        <input type="number" name="accepted_price" class="form-control float" placeholder="Accepted Price (optional)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Accepted Date</label>
                                        <input type="text" name="accepted_on" class="form-control datetimepicker" placeholder="Accepted Date (optional)">
                                    </div>
                                </div>
                            </div>
                            <textarea name="notes" class="form-control"  placeholder="Enter any notes about the transaction here. These notes are only viewable to you."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                    @click="acceptSelectedClaim"
                                    class="btn p-x-md btn-sm primary btn-claim-action-final"
                                    id="btn_confirmed_claim">Save
                            </button>
                            <button
                                    type="button"
                                    class="btn btn-sm white"
                                    id="btn_confirmed_claim_cancel"
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
                            <button type="button"
                                    @click="rejectSelectedClaim"
                                    class="btn-sm btn primary btn-claim-action-final p-x-md"
                                    id="btn_confirmed_claim">Save</button>
                            <button type="button" class="btn-sm btn white" id="btn_confirmed_claim_cancel"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>