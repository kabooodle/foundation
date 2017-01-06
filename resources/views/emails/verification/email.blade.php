<p>Hey {{ $user->username }}, <a href="{!! $verifyLink !!}">please verify your email address!</a></p>

<p>Someone (hopefully you) has used this email at {{ env('APP_NAME') }}. Please click the button below to verify your ownership of this email for the account, {{ $user->username }}.</p>

<a href="{!! $verifyLink !!}">
    <button class="btn primary btn-block p-x-md">Verify your email</button>
</a>

<p>Or click on this link: <a href="{!! $verifyLink !!}">{!! $verifyLink !!}</a></p>
