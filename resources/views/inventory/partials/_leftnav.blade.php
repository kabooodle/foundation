{{--<a href="{{ route('shop.inventory.index', [user()->username]) }}" class="nav-link {{ Request::is('shop/*/inventory') ? 'active' : null }}">--}}
    {{--Manage All Items--}}
{{--</a>--}}
<a href="{{ route('shop.inventory.create', [user()->username]) }}" class="nav-link {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">
    Add Items
</a>