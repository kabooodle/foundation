
@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Cancel Subscription</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p></p>
        </div>
        <form method="POST" action="{{ route('profile.subscription.destroy') }}">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
        <div class="box-body clearfix">
            <div class="pull-right">
                <button class="btn danger">Yes, cancel my subscription immediately.</button>
            </div>
        </div>
        </form>
    </div>

@endsection