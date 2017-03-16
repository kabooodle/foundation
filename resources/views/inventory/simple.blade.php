
@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="pull-right">
        <a href="{{ route('shop.inventory.archive.index', [webuser()->username]) }}" class="btn white btn-sm">Archived Inventory</a>
        <a href="{{ route('shop.inventory.index', [webUser()->username]) }}" class="btn white btn-sm"><i class="fa fa-th-list" aria-hidden="true"></i> Detailed View</a>
    </div>
@endsection


@section('body-content')


    <listable-groupings
            listablegroupings_endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
            :display_footer_buttons="true"
            :display_toggle_all_buttons="false"
            inventory-index-route="{{ route('shop.inventory.index', [webUser()->username]) }}"
    ></listable-groupings>


    <onboard-card class="onboard-manageinventory" v-if="listables.length == 0 && ! actions.refreshing_data">
        <template slot="title">No inventory to manage or list</template>
        <template slot="subtext">
            Once you've added inventory, you can list it to Facebook &amp; flash sales anytime!
            <br>
            Wish to edit an item? You would do that here too :)
        </template>
        <template slot="extra"><button class="btn btn-lg btn-grn m-b-2"><a href="{{ route('shop.inventory.create', [webUser()->username]) }}" >Got it! Take me to add inventory</a></button></template>
    </onboard-card>


@endsection

@push('footer-scripts')
<script src="{{ staticAsset("/assets/js/inventory-management.js") }}"></script>
@endpush