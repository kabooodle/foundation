{!! $comment->author->name !!} commented on {!!  $commentable->getName()  !!}:<br><Br>

{!! nl2br($comment->text)  !!}
<br><br>
--
the {{ env('APP_NAME') }} Team
