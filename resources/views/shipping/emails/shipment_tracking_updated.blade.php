<p>{{ $subject }}</p>
<p>You can view the purchase and tracking updates here: <a href="{{  route('profile.claims.show', [$claim->getUUID()])  }}">{{  route('profile.claims.show', [$claim->getUUID()])  }}</a></p>

<br><br>
--
the {{ env('APP_NAME') }} Team