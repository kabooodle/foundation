@extends('layouts.body_w_leftnav')

@section('body-content-left-nav')
    @include('groups.partials._leftnav')
@endsection


@section('body-inner-content')
    {{ Form::open(['route' => 'groups.store']) }}
            <div class="box">
                <div class="box-header">
                    <h2>Create a New Group</h2>
                </div>
                <div class="box-divider m-a-0"></div>
                <div class="box-body">

                    <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                        <label for="inputEmail3" class="col-sm-3 form-control-label">Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('name', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                        <label for="inputPassword3" class="col-sm-3 form-control-label">Members</label>
                        <div class="col-sm-9">
                            {{ Form::select('members[]',[], null, ['id' => 'select_members', 'placeholder' => 'Enter emails', 'class' => 'form-control', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row {{ $errors->has('privacy') ? 'has-danger' : null }}">
                        <label for="inputPassword3" class="col-sm-3 form-control-label">Privacy</label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="privacy" value="{{ \Kabooodle\Models\Groups::PRIVACY_PRIVATE }}" checked> {{ \Kabooodle\Models\Groups::PRIVACY_PRIVATE }}
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="privacy" value="{{ \Kabooodle\Models\Groups::PRIVACY_PUBLIC }}"> {{ \Kabooodle\Models\Groups::PRIVACY_PUBLIC }}
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="privacy" value="{{ \Kabooodle\Models\Groups::PRIVACY_SECRET }}"> {{ \Kabooodle\Models\Groups::PRIVACY_SECRET }}
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group row m-t-md">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn primary">Create</button>
                    <a class="m-l text-muted" href="{{ route('groups.index') }}">Cancel</a>
                </div>
            </div>
            {{ Form::close() }}
    @push('footer-scripts')
    <script>

        $(function(){
            var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

            $('select').selectize({
                persist: false,
                maxItems: null,
                valueField: 'email',
                labelField: 'name',
                searchField: ['name', 'email'],
                options: [ ],
                plugins: ['remove_button'],
                render: {
                    item: function(item, escape) {
                        return '<div>' +
                                (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                                (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                                '</div>';
                    },
                    option: function(item, escape) {
                        var label = item.name || item.email;
                        var caption = item.name ? item.email : null;
                        return '<div>' +
                                '<span class="label">' + escape(label) + '</span>' +
                                (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
                                '</div>';
                    }
                },
                createFilter: function(input) {
                    var match, regex;

                    regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                    match = input.match(regex);
                    if (match) return !this.options.hasOwnProperty(match[0]);

                    regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                    match = input.match(regex);
                    if (match) return !this.options.hasOwnProperty(match[2]);

                    return false;
                },
                create: function(input) {
                    if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                        return {email: input};
                    }
                    var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
                    if (match) {
                        return {
                            email : match[2],
                            name  : $.trim(match[1])
                        };
                    }
                    alert('Invalid email address.');
                    return false;
                }
            });
        });
    </script>
    @endpush

@endsection