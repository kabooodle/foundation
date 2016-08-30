@extends('layouts.body_w_leftnav')



@section('body-content-left-nav')
    @include('flashsales.partials._leftnav')
@endsection

@section('body-inner-content')


    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush


    <div class="box">
        <div class="box-header">
            <h2>Create a flash sale</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            {{ Form::open(['route' => 'flashsales.store']) }}
            <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Name</label>
                <div class="col-sm-9">
                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                <label for="inputPassword3" class="col-sm-3 form-control-label">Description</label>
                <div class="col-sm-9">
                    {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('starts_at') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Starting Date and Time</label>
                <div class="col-sm-9">
                    {{ Form::text('starts_at', null or 0, ['class' => 'form-control', 'id' => 'datetimepicker1']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('ends_at') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Ending Date and Time</label>
                <div class="col-sm-9">
                    {{ Form::text('ends_at', null or 0, ['class' => 'form-control', 'id' => 'datetimepicker2']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('type') ? 'has-danger' : null }}">
                <label for="type" class="col-sm-3 form-control-label">Hosted by</label>
                <div class="col-sm-9">
                    {{ Form::select('type', array_flip(\Kabooodle\Models\FlashSales::getTypes()), null, ['class' => 'form-control', 'id' => 'form-host-select']) }}
                </div>
            </div>
            <div id="form-wrapper-group" class="hide hidden">
                <div class="form-group row {{ $errors->has('group_id') ? 'has-danger' : null }} ">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Group</label>
                    <div class="col-sm-9">
                        {{ Form::select('host_id', user()->groupsAsAdmin->pluck('name', 'id')->toArray(), null, ['class' => 'form-control selectpicker']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Admins</label>
                    <div class="col-sm-9">
                        {{ Form::select('admins[]', [] + (user()->groupsAsAdmin->first() ? user()->groupsAsAdmin->first()->allMembers()->pluck('name', 'id')->toArray() : []), null, ['class' => 'form-control disabled', 'id' => 'admins', 'placeholder' => 'Select a group first', 'disabled']) }}
                    </div>
                </div>
            </div>
            <div class="form-group row {{ $errors->has('admins') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Seller Rules</label>
                <div class="col-sm-9">
                    {{ Form::textarea('seller_rules', null, ['class'=> 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('privacy') ? 'has-danger' : null }}">
                <label for="inputPassword3" class="col-sm-3 form-control-label">Privacy</label>
                <div class="col-sm-9">
                    <div class="radio">
                        <label class="md-check">
                            {{ Form::radio('privacy', 'private', null, ['class'=>'has-value']) }}<i class="green"></i> private
                        </label>
                    </div>
                    <div class="radio">
                        <label class="md-check">
                            {{ Form::radio('privacy', 'public', null, ['class'=>'has-value']) }}<i class="green"></i> public
                        </label>
                    </div>
                    <div class="radio">
                        <label class="md-check">
                            {{ Form::radio('privacy', 'secret', null, ['class'=>'has-value']) }}<i class="green"></i> secret
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row m-t-md">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn primary">Save</button>
                    <a class="m-l text-muted" href="{{ route('flashsales.index') }}">Cancel</a>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>




@endsection

@push('footer-scripts')
<script>
    $(function(){

        $('#datetimepicker1').datetimepicker({
            format: "MM/DD/YYYY hh:mmA",
            minDate: new Date(), // Don't allow dates before today.
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
            minDate: new Date(), // Don't allow dates before today.
            sideBySide: true,
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            },
            useCurrent: false //Important! See issue #1075
        });
        $("#datetimepicker1").on("dp.change", function (e) {
            $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker2").on("dp.change", function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        });

        $('select#admins').selectize({
            persist: false,
            maxItems: null,
            plugins: ['remove_button'],
            options: [ ]
        });

        $('#form-host-select').change(function(e){
            var $that = $(this);
            var $groupWrapper = $('#form-wrapper-group');
            if ($that.find(':selected').val() == '{{ \Kabooodle\Models\FlashSales::TYPE_GROUP }}') {
                $groupWrapper.removeClass('hide hidden').show();
            } else {
                $groupWrapper.addClass('hide hidden').hide();
            }
        });
    })
</script>
@endpush