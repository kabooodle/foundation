@extends('layouts.body_w_leftnav', ['contentId' => 'create_flashsale'])



@section('body-content-left-nav')
    @include('flashsales.partials._leftnav')
@endsection

@section('body-inner-content')

    {{ Form::open(['route' => 'flashsales.store']) }}

    <div class="box">
        <div class="box-header">
            <h2>Create a flash sale</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">

            <build-flashsale
                    user_hash="{{ user()->public_hash }}"
                    s3_key_url="{{ apiRoute('api.files.sign') }}"
                    search_endpoint="{{ apiRoute('users.search') }}"
                    :form_errors="{{ $errors->toJson() }}"
            ></build-flashsale>

        </div>
    </div>


    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>
    {{ Form::close() }}

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsale-create.js') }}"></script>
<script>
    $(function(){

        $('#datetimepicker1').datetimepicker({
            format: "MM/DD/YYYY hh:mmA",
            minDate: new Date(),
            sideBySide: true,
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            }
        });
        $('#datetimepicker2').datetimepicker({
            format: "MM/DD/YYYY hh:mmA",
            minDate: new Date(),
            sideBySide: true,
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            },
            useCurrent: false
        });
        $("#datetimepicker1").on("dp.change", function (e) {
            $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker2").on("dp.change", function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        });
    })
</script>
@endpush