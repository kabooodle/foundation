
<a href="{{ route('flashsales.index') }}" class="nav-link {{ Request::is('flashsales') ? 'active' : null }}">
   View Flash Sales
</a>

@if (webUser()->hasAtLeastMerchantSubscription())
<a href="{{ route('flashsales.create') }}" class="nav-link {{ Request::is('flashsales/create') ? 'active' : null }}">
    Create Flash Sale
</a>
@endif
@if(user())
<a href="#" class="nav-link {{ Request::is('flashsales/favorite') ? 'active' : null }}">
    Favorited
</a>
<hr>

@foreach(webUser()->flashsales as $flashSale)
    <a href="{{ route('flashsales.show', [$flashSale->getUUID()]) }}" class="nav-link {{ Request::is("flashsales/{$flashSale->getUUID()}") ? 'active' : null }}">
        {!! $flashSale->name !!}
    </a>
@endforeach
@endif