@extends('profile.settingstemplate')


@section('settings-content')

    {{ Form::open(['route' => 'profile.notifications.update', 'method' => 'POST']) }}
    <div class="box">
        <div class="box-header">
            <h2>Notification Settings</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Group</th>
                        <th>Email</th>
                        {{--<th>Web</th>--}}
                    </tr>
                </thead>
                <tbody>
                @foreach($notifications as $group => $notific)
                    @foreach($notific as $notification)
                    <tr>
                        <td>{{ $notification->description }}</td>
                        <td>{{ ucfirst($notification->group) }}</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input
                                            data-type="email"
                                        data-id="{{ $notification->id }}"
                                    @change="changed"
                                    type="checkbox"
                                    {{ $userNotifications->first(function($v, $k) use ($notification) {  return $k->pivot->notification_id == $notification->id && $k->pivot->email == 1; }) ? ' checked' : null }}
                                    ><span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ Form::close() }}

    <script>
        const notifications_route = '{{ route('profile.notifications.update') }}';
    </script>

    <script src="{{  staticAsset('/assets/js/profile-notifications.js') }}"></script>

@endsection