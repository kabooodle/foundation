<p>Hey {{ $user->full_name }}, <a href="{!! $verifyLink !!}">please verify your recent claim!</a></p>

<p>Someone (hopefully you) has used this email at {{ env('APP_NAME') }} to claim an item. Please click the button below to verify the claim. The claim will not go through until you do so, or the seller approves it. The item is on hold for you for 5 minutes. After 5 minutes others may claim it. So please hurry!</p>

<a href="{!! $verifyLink !!}">
    <button class="btn primary btn-block p-x-md">Verify your claim</button>
</a>

<p>Or click on this link: <a href="{!! $verifyLink !!}">{!! $verifyLink !!}</a></p>
