@extends('layouts.full')

@section('body-content')
    <div class="p-l p-r">
        <div class="row">
            <div class="col-sm-12 col-md-push-2 col-md-8">
                <div class="box">
                    <div class="box-header b-b">
                        <h3>We're sorry</h3>
                    </div>
                    <div class="box-body">
                        <p>Your claim was not verified because the item's available inventory ran out before you verified your claim. Better luck next time! Consider
                            <a href="{{ route('auth.register') }}">registering as a {{ env('APP_NAME') }} user</a> to avoid this in the future.</p>
                        --
                        the {{ env('APP_NAME') }} Team
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
