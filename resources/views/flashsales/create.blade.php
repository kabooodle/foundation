@extends('layouts.body_w_leftnav', ['contentId' => 'create_flashsale'])



@section('body-content-left-nav')
        <a href="{{ route('flashsales.index') }}" class="nav-link {{ Request::is('flashsales') ? 'active' : null }}">
            View Flash Sales
    </a>

    @if (user()->hasAtLeastMerchantSubscription())
        <a href="{{ route('flashsales.create') }}" class="nav-link {{ Request::is('flashsales/create') ? 'active' : null }}">
            Create Flash Sale
        </a>
        <a  @click.prevent="buildGroup" class="nav-link">
            Create Seller Group
        </a>
    @endif
    @if(user())
        @if(user()->flashsales->count() > 0)
        <hr>
        <small class="text-muted text-sm nav-link">Manage my flash sales</small>
        @foreach(user()->flashsales as $flashSale)
            <a href="{{ route('flashsales.show', [$flashSale->getUUID()]) }}" class="nav-link {{ Request::is("flashsales/{$flashSale->getUUID()}") ? 'active' : null }}">
                {!! $flashSale->name !!}
            </a>
        @endforeach
        @endif
    @endif
@endsection

@section('body-inner-content')
            <build-flashsale
                    user_hash="{{ user()->public_hash }}"
                    s3_key_url="{{ apiRoute('api.files.sign') }}"
                    search_endpoint="{{ apiRoute('users.search') }}"
                    save_endpoint="{{ apiRoute('flashsales.store') }}"
                    group_search_endpoint="{{ apiRoute('flashsales.groups.search') }}"
                    group_save_endpoint="{{ apiRoute('flashsales.groups.store') }}"
            ></build-flashsale>
@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsale-create.js') }}"></script>
@endpush