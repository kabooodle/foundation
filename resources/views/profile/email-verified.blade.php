@extends('layouts.full')

@section('body-content')
    <div class="p-l p-r">
        <div class="row">
            <div class="col-sm-12 col-md-push-2 col-md-8">
                <div class="box">
                    <div class="box-header b-b">
                        <h3>Thank you</h3>
                    </div>
                    <div class="box-body">
                        <p>You have successfully verified your email address. We are looking forward to some wonderful communication!</p>
                        --
                        the {{ env('APP_NAME') }} Team
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
