<a href="{{ route('groups.index') }}" class="nav-link {{ Request::is('groups') ? 'active' : null }}">
    Manage Groups
</a>
@if (webUser()->hasAtLeastMerchantSubscription())
<a href="{{ route('groups.create') }}" class="nav-link {{ Request::is('groups/create') ? 'active' : null }}">
    Create Group
</a>
@endif

<div class="m-y"><hr></div>
<div class="p-y">
    @foreach(webUser()->allMyGroups('alpha') as $group)
        <a href="{{ route('groups.show', [$group->getUUID()]) }}" class="nav-link {{ Request::is('groups/'.$group->getUUID().'*') ? 'active' : null }}">
            {!! $group->name  !!}
        </a>
    @endforeach
</div>