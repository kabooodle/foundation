<a href="{{ route('groups.index') }}" class="nav-link {{ Request::is('groups') ? 'active' : null }}">
    Manage Groups
</a>
<a href="{{ route('groups.create') }}" class="nav-link {{ Request::is('groups/create') ? 'active' : null }}">
    Create Group
</a>

<div class="m-y"><hr></div>
<div class="p-y">
    @foreach(user()->allMyGroups('alpha') as $group)
        <a href="{{ route('groups.show', [$group->getUUID()]) }}" class="nav-link {{ Request::is('groups/'.$group->getUUID().'*') ? 'active' : null }}">
            {!! $group->name  !!}
        </a>
    @endforeach
</div>