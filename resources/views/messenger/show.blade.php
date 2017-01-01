@extends('layouts.full', ['contentId' =>'messages_index'])

@section('body-menu')

    <a class="btn white btn-sm" href="{{ route('messenger.index') }}">Messages</a>

@endsection

@section('body-content')

    <div class="box white" >
        <div class="box-header">
            <h4>{{ $thread->subject }}</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body" id="messages-body" style="max-height:450px; position: relative;" data-scrollable="scrollable">
            <messages
                    :thread="{{ $thread->toJson() }}"
                    endpoint="{{ apiRoute('messenger.show', [$thread->id]) }}"
            ></messages>
        </div>
        <div class="box-footer b-t">
            <message-respond
                    endpoint="{{ apiRoute('messenger.update', [$thread->id]) }}"
            ></message-respond>
        </div>
    </div>


@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/messenger-thread-index.js') }}"></script>
@endpush