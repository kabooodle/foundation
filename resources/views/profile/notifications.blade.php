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
                        <th>SMS*</th>
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
                        <td>
                            @if($notification->type_sms)
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input
                                            data-type="sms"
                                            data-id="{{ $notification->id }}"
                                    @change="changed"
                                    type="checkbox"
                                    {{ $userNotifications->first(function($v, $k) use ($notification) {  return $k->pivot->notification_id == $notification->id && $k->pivot->sms == 1; }) ? ' checked' : null }}
                                    ><span></span>
                                </label>
                            </div>
                            @else
                                <small class="text-muted text-sm">--</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ Form::close() }}

    <div class="box white">
        <div class="box-header">
            <h4>Phone number for SMS notifications</h4>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <phone-number
                    fetch_endpoint="{{ apiRoute('phonenumbers.index') }}"
                    verify_endpoint="{{ apiRoute('phonenumbers.update') }}"
                    create_endpoint="{{ apiRoute('phonenumbers.store') }}"
            ></phone-number>

            <p class="text-muted text-sm  m-t-2 m-b-0"><small >*SMS notifications can only be send to valid US phone numbers. Carrier charges applied, please check with your carrier for incoming prices.</small></p>
        </div>
    </div>

    <script>
        const notifications_route = '{{ route('profile.notifications.update') }}';
    </script>

    <script src="{{  staticAsset('/assets/js/profile-notifications.js') }}"></script>

@endsection