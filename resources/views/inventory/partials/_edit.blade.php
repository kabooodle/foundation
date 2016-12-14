<div id="inventory_manage">
@include('widgets._fileuploadscripts')

    <form
    id="form_inventory_manage"
    method="POST"
    accept-charset="UTF-8"
    action="{{ apiRoute('inventory.update', [$item->id]) }}"
    >


{{--<validator--}}
{{--name="inventory_validation"--}}
{{--:classes="{ invalid : ' has-danger ' }"--}}
{{-->--}}


<inventory-edit
        :styles="{{ $styles->toJson() }}"
        :item="{{ $item->load('user')->toJson() }}"
        :existingimages="{{ $item->files->toJson() }}"
        tags="{!! $item->tagsString()  !!}"
        api_route="{{ route('api.files.sign') }}"
></inventory-edit>

<script src="{{ staticAsset('/assets/js/inventory-edit.js') }}"></script>
{{--</validator>--}}

</form>
</div>