@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="center-block text-center">
        <div class="btn-group dropdown ">
            <button :disabled="selectedItems.length == 0 || ! ready"
                    v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"
                    class="disabled btn white btn-sm dropdown-toggle"
                    data-toggle="dropdown">
                <span class="dropdown-label">Bulk (@{{selectedItems.length}})</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item text-danger" href="">Delete</a>
            </div>
        </div>
        <button class="btn white btn-sm " id="navbarSideButton">Filter Items</button>
    </div>
@endsection


@section('body-content')




@endsection