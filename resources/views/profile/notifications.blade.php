@extends('profile.settingstemplate')


@section('settings-content')

    {{ Form::open(['route' => 'profile.notifications.update', 'method' => 'POST']) }}
    <div class="box">
        <div class="box-header">
            <h2>Notification Settings</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <table class="table table-sm table-condensed">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Email</th>
                        {{--<th>Web</th>--}}
                    </tr>
                </thead>
                <tbody>
                @foreach($notifications as $notification)
                    <tr>
                        <td>{{ $notification->description }}</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input
                                            data-type="email"
                                        data-id="{{ $notification->id }}"
                                    @change="changed"
                                    type="checkbox"
                                    {{ user()->notificationsettings->find($notification->id)->pivot->email == true ? 'checked' : null  }}
                                    ><span></span>
                                </label>
                            </div>
                        </td>
                        {{--<td>--}}
                            {{--<div class="checkbox checkbox-slider--b-flat">--}}
                                {{--<label>--}}
                                    {{--<input--}}
                                        {{--data-type="web"--}}
                                            {{--data-id="{{ $notification->id }}"--}}
                                    {{--@change="changed"--}}
                                    {{--type="checkbox"--}}
                                    {{--{{ user()->notificationsettings->find($notification->id)->pivot->web == true ? 'checked' : null  }}--}}
                                    {{--><span></span>--}}
                                {{--</label>--}}
                            {{--</div>--}}
                        {{--</td>--}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ Form::close() }}

    <script>
        const notifications_route = '{{ route('profile.notifications.update') }}';
    </script>

    <script src="/assets/js/profile-notifications.js"></script>

@endsection