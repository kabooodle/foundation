{{--<form method="{{ route('messenger.store') }}" action="POST">--}}
    {{--{{ csrf_field() }}--}}
    {{--<div class="box white">--}}
        {{--<div class="box-header">--}}
            {{--<h4 class="">Create new message</h4>--}}
        {{--</div>--}}
        {{--<div class="box-divider"></div>--}}
        {{--<div class="box-body">--}}
            {{--@include('messenger.partials._formfields')--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="form-group">--}}
        {{--<button class="btn primary">Send</button>--}}
    {{--</div>--}}
{{--</form>--}}

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#kbdl_modal_messenger" data-keyboard="false" data-backdrop="static">
    Launch demo modal
</button>

@include('messenger.partials._modal')