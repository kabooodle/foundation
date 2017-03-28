@extends('layouts.body_w_leftnav', ['contentId' => 'flashsales_edit'])



@section('body-content-left-nav')
    <a href="{{ route('flashsales.index') }}" class="nav-link {{ Request::is('flashsales') ? 'active' : null }}">
        Browse flash sales
    </a>

    @if (webUser()->hasAtLeastMerchantSubscription())
        <a href="{{ route('flashsales.create') }}" class="nav-link {{ Request::is('flashsales/create') ? 'active' : null }}">
           Create flash sale
        </a>
        <a  @click.prevent="buildGroup" class="nav-link">
            Create seller group
        </a>
    @endif
    @if(user())
        @if(webUser()->flashsales->count() > 0)
            <hr>
            <small class="text-muted text-sm nav-link">Manage my flash sales</small>
            @foreach(webUser()->flashsales as $flashSale)
                <a href="{{ route('flashsales.edit', [$flashSale->getUUID()]) }}" class="nav-link {{ Request::is("flashsales/{$flashSale->getUUID()}/edit") ? 'active' : null }}">
                    {!! $flashSale->name !!}
                </a>
            @endforeach
        @endif
    @endif
@endsection


@section('body-inner-content')

    <build-flashsale
            title="Edit flash sale"
            user_hash="{{ webUser()->public_hash }}"
            s3_bucket="{{ env('AWS_BUCKET') }}"
            s3_key_url="public-read"
            s3_key_url="{{ apiRoute('api.files.sign') }}"
            search_endpoint="{{ apiRoute('users.search') }}"
            save_endpoint="{{ apiRoute('flashsales.update', [$flashsale->id]) }}"
            group_search_endpoint="{{ apiRoute('flashsales.groups.search') }}"
            group_save_endpoint="{{ apiRoute('flashsales.groups.store') }}"
            :existing_data="{{ $flashsale->toJson() }}"
    ></build-flashsale>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsales-edit.js') }}"></script>
@endpush