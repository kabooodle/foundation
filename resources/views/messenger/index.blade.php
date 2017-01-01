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
            direct_to_user="false"
            endpoint=""
            modal_el_id="msg_modal"
    >
    </message-modal>

    <select class="" multiple></select>

    <script>
        $(function(){
            $('select').selectize({
                valueField: 'id',
                labelField: 'full_name',
                searchField: 'full_name',
                maxItems: null,
                options: [],
                create: false,
                plugins: ['remove_button'],
                render: {
                    option: function (item, escape) {
                        console.log(item);
                        return '<div>'+ escape(item.full_name)  +' ('+escape(item.username)+')</div>';
                    }
                },
                load: function(query, callback) {
                    if (!query.length || query.length < 4) return callback();
                    $.ajax({
                        url: '{{ apiRoute('users.search') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            q: query
                        },
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res.users);
                        }
                    });
                }
            });
        });
    </script>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/messenger-index.js') }}"></script>
@endpush