@extends('layouts.full')

@section('body-menu')

    <div class="clearfix">
        <div class="pull-right">
            <div class="center-block text-center" data-turbolinks="false">
                <a href="{{ route('flashsales.show', [$item->getUUID()]) }}" class="btn btn-sm default white">
                    Cancel
                </a>
            </div>
        </div>
    </div>
@endsection



@section('body-content')

    {{ Form::model($item, ['route' => ['flashsales.update', $item->getUUID()], 'method' => 'PUT']) }}
    <div class="padding">


        <div class="box">
            <div class="box-divider m-a-0"></div>
            <div class="box-body">

                {{ Form::hidden('type', $item->type) }}
                <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                    <label for="inputPassword3" class="col-sm-3 form-control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('starts_at') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Starting Date and Time</label>
                    <div class="col-sm-9">
                        {{ Form::text('starts_at', Binput::old('ends_at', $item->startsAtPicker()), ['class' => 'form-control', 'id' => 'datetimepicker1']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('ends_at') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Ending Date and Time</label>
                    <div class="col-sm-9">
                        {{ Form::text('ends_at', Binput::old('ends_at', $item->endsAtPicker()), ['class' => 'form-control', 'id' => 'datetimepicker2']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('group_id') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Host</label>
                    <div class="col-sm-9">
                            <div class="list-item box">
                                <div class="list-left">
                                                                            <span class="w-40 avatar"><img
                                                                                        src="https://placekitten.com/g/32/32" alt="..."> <i
                                                                                        class="on b-white bottom"></i></span>
                                </div>
                                <div class="list-body">
                                                {{ $item->host->name }}
                                </div>
                            </div>
                    </div>
                </div>
                @if($item->hostIsGroup())
                <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Admins<br>
                        <small class="text-muted">(from group)</small>
                    </label>
                    <div class="col-sm-9">
                        {{ Form::select('admins[]',  (array) [null => ''] + ($item->group ? $item->group->allMembers()->pluck('name', 'id')->toArray() : []), ($item->admins->count() > 0 ? $item->admins->pluck('id')->toArray() : ''), ['class' => 'multiple form-control', 'id' => 'admins']) }}
                    </div>
                </div>
                @endif
                <div class="form-group row {{ $errors->has('privacy') ? 'has-danger' : null }}">
                    <label for="inputPassword3" class="col-sm-3 form-control-label">Privacy</label>
                    <div class="col-sm-9">
                        <div class="radio">
                            <label class="md-check">
                                {{ Form::radio('privacy', 'private', null, ['class'=>'has-value']) }}<i
                                        class="green"></i> private
                            </label>
                        </div>
                        <div class="radio">
                            <label class="md-check">
                                {{ Form::radio('privacy', 'public', null, ['class'=>'has-value']) }}<i
                                        class="green"></i> public
                            </label>
                        </div>
                        <div class="radio">
                            <label class="md-check">
                                {{ Form::radio('privacy', 'secret', null, ['class'=>'has-value']) }}<i
                                        class="green"></i> secret
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3>Sellers ({{ $item->sellers->count() }})</h3>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">
                <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Seller Rules <small class="block text-muted">(Optional)</small></label>
                    <div class="col-sm-9">
                        {{ Form::textarea('seller_rules', null, ['class'=> 'form-control']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Invite Sellers <small class="block text-muted">Enter email addresses to invite sellers.</small></label>
                    <div class="col-sm-9">
                        {{ Form::text('sellers_invites',  null, ['class' => 'form-control', 'id' => 'sellers', 'placeholder' => '']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Pending Seller Invitations ({{$item->pendingInvitations->count()}})</label>
                    <div class="col-sm-9">
                        <ul class="list-inline">
                        @if($item->pendingInvitations->count() > 0)
                                @foreach($item->pendingInvitations as $pendingInvite)
                                    <li class="list-inline-item m-b-1">{!! $pendingInvite->email !!} <small class="text-muted">- Invited {{ $pendingInvite->getInvitedAtHuman() }} by {!!  $pendingInvite->invitedByUser->name  !!}</small></li>
                                @endforeach
                        @else
                                <li class="list-inline-item m-b-1">None</li>
                        @endif
                        </ul>
                    </div>
                </div>
                @if($item->sellers->count() > 0)
                    <hr>
                    <div class="row">
                        <label for="inputEmail3" class="col-sm-3 form-control-label">Permitted Sellers</label>
                        <div class="col-sm-9">
                            <div class="row">
                                @foreach($item->sellers as $seller)
                                    <div class="col-sm-5">
                                        <div class="list-item box r m-b"><a
                                                    href="{{ route('shop.show', [$seller->username]) }}"
                                                    class="list-left"><span class="w-40 avatar"><img
                                                            src="https://placekitten.com/g/32/32" alt="..."> <i
                                                            class="on b-white bottom"></i></span></a>
                                            <div class="list-body">
                                                <div class="text-ellipsis"><a
                                                            href="{{ route('shop.show', [$seller->username]) }}">{{ $seller->name }}</a>
                                                    <i class="text-muted pull-right fa fa-times" aria-hidden="true"></i>
                                                </div>
                                                <?php $l=$item->invitations->filter(function($m) use ($seller) {
                                                    return $m->user_id == $seller->id || $m->email == $seller->email;
                                                })->first();?>
                                                <small class="text-muted text-ellipsis">Accepted {{ $l->getAcceptedAtHuman() }}</small>
                                                <small class="text-muted text-ellipsis">Invited by {!! $l->invitedByUser->name !!}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="box">
            <div class="box-body">
                <div class="form-group row {{ $errors->has('starts_at') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Notifications</label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label class="md-check">
                                <input type="checkbox" name="notifications[sellers]" checked><i class="green"></i>
                                Notify all sellers of changes?
                            </label>
                        </div>
                        <div class="checkbox">
                            <label class="md-check">
                                <input type="checkbox" name="notifications[followers]" checked><i class="green"></i>
                                Notify all followers of changes?
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('starts_at') ? 'has-danger' : null }}">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" class="btn primary btn-xl btn-lg">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
    @push('footer-scripts')
    <script>
        $(function () {

            $('#datetimepicker1').datetimepicker({
                format: "MM/DD/YYYY hh:mmA",
                minDate: new Date(), // Don't allow dates before today.
                sideBySide: true,
                icons: {
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right'
                }
            });
            $('#datetimepicker2').datetimepicker({
                format: "MM/DD/YYYY hh:mmA",
                minDate: new Date(), // Don't allow dates before today.
                sideBySide: true,
                icons: {
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right'
                },
                useCurrent: false //Important! See issue #1075
            });
            $("#datetimepicker1").on("dp.change", function (e) {
                $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker2").on("dp.change", function (e) {
                $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
            });

            $('select.multiple').selectize({
                persist: false,
                maxItems: null,
                plugins: ['remove_button'],
                options: []
            });

            var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

            $('#sellers').selectize({
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

                    // email@address.com
                    regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                    match = input.match(regex);
                    if (match) return !this.options.hasOwnProperty(match[0]);

                    // name <email@address.com>
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
        })
    </script>
    @endpush
@endsection