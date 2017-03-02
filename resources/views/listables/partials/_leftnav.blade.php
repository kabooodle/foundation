{{--<a href="{{ route('shop.inventory.index', [webUser()->username]) }}" class="nav-link {{ Request::is('shop/*/inventory') ? 'active' : null }}">--}}
    {{--Manage All Items--}}
{{--</a>--}}
<a href="{{ route('shop.inventory.create', [webUser()->username]) }}" class="nav-link {{ Request::is('shop/*/inventory/create') ? 'active' : null }}">
    Add Items
</a>