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
                        <th>Web</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>When a referral joins</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>When an inventory item is claimed</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>When someone comments on your inventory item</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>When an item you are watching is updated</td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label>
                                    <input type="checkbox" checked=""><span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{ Form::close() }}

@endsection