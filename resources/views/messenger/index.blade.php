@extends('layouts.full', ['contentId' => 'messages_index'])

@section('body-menu')
    <div class="pull-right">
        <button type="button" class="btn white btn-sm" data-toggle="modal" data-target="#msg_modal">New Message</button>
    </div>
@endsection

@section('body-content')

    <threads
            endpoint="{{ apiRoute('messenger.index') }}"
            thread_endpoint="{{ route('messenger.show', ['::ID::']) }}"
    ></threads>
    <message-modal
            title="Send message"
            direct_to_user="false"
            search_endpoint="{{ apiRoute('users.search') }}"
            endpoint="{{ apiRoute('messenger.store') }}"
            modal_el_id="msg_modal"
    >
    </message-modal>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/messenger-index.js') }}"></script>
@endpush