@extends('layouts.body_w_leftnav')


@section('body-menu')
    @include('flashsales.partials._bodymenu')
@endsection


@section('body-content-left-nav')
    @include('flashsales.partials._leftnav')
@endsection

@section('body-inner-content')

    @include('flashsales.partials._flashsaleheader')

    <div class="box">
        <div class="box-body">
            <div class="center-block">
                <p class="text text-center">You were invited to participate in this flash sale as a seller
                    by {!! $invitation->invitedByUser->name !!}, {{ $invitation->getInvitedAtHuman() }}.</p>
                @if(user())
                <div class="box light lt p-a-1">
                    <p>Admin Rules:</p>
                    @if ($item->seller_rules)
                        {!! nl2br($item->seller_rules) !!}
                    @else
                    (none)
                    @endif
                </div>
            </div>
            <div class="center-block text-center">

                {{ Form::open(['route' => ['flashsales.invitations.update', $item->getUUID(), $invitation->uuid], 'method' => 'patch', 'class'=>'inline']) }}
                <button class="btn b-primary primary">Accept Invitation</button>
                {{ Form::close() }}

                {{ Form::open(['route' => ['flashsales.invitations.destroy', $item->getUUID(), $invitation->uuid], 'method' => 'delete', 'class'=>'inline']) }}
                <button class="btn b-white white m-l-lg">Ignore Invitation</button>
                {{ Form::close() }}
                <p class="text-muted text-sm-center text-sm p-t-1">By clicking Accept, you agree to {{ env('APP_ENV') }} Seller Terms of User. <br>You also agree to the Admin's above rules.</p>
            </div>

            @else
                <p class="text-center">To accept and begin adding items, please <a href="{{ route('auth.login') }}?redirectTo={{route('flashsales.invitations.show', [$item->getUUID(), $invitation->uuid])}}" class="text-primary">login</a> or <a class="text-primary" href="{{ route('auth.register') }}?redirectTo={{route('flashsales.invitations.show', [$item->getUUID(), $invitation->uuid])}}">create a {{ env('APP_NAME') }} account</a>.</p>
            @endif

        </div>
    </div>

@endsection