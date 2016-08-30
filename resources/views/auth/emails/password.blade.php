Click here to reset your password: <a href="{{ $link = route('auth.password.reset.index', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>

--
the {{ env('APP_NAME') }} Team
