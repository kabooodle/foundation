
@if(isset($item))
<div class="pull-left">

        {{--<span class="inline btn-group-vertical" style="margin-top: 5px;"><span class="_800">{{ rand(0,2000) }}</span> Views</span>--}}
        <span class="inline btn-group-vertical" style="margin-top: 5px;"><span class="_800">{{ $item->allMembers()->count() }}</span> <span class="text-muted">{{ str_plural('Member', $item->allMembers()->count()) }}</span></span>
        <span class="inline btn-group-vertical m-l" style="margin-top: 5px;"><span class="_800">{{ $item->activeFlashSales->count() }}</span> <span class="text-muted">Active Flash Sales</span></span>

</div>
@endif

{{--<div class="pull-center">--}}
    {{--<div class="btn-toolbar center-block text-center">--}}
        {{--<div class="btn-group dropdown">--}}
            {{--<button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">--}}
                {{--<span class="dropdown-label">Filter</span>--}}
                {{--<span class="caret"></span>--}}
            {{--</button>--}}
            {{--<div class="dropdown-menu text-left text-sm">--}}
                {{--<a class="dropdown-item" href="">By Name</a>--}}
                {{--<a class="dropdown-item" href="">By Privacy</a>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

<div class="pull-right">
    <a href="{{ route('groups.create') }}" class="btn btn-sm btn-outline b-primary primary" >
        Create Group
    </a>
    @if(isset($item) && $item->isUserAdmin($_current_user))
        <span class="b-l m-l m-r"></span>
    <a href="{{ route('groups.index') }}" class="btn btn-sm default white" >
        Edit
    </a>
   @endif
</div>

{{--@push('footer-scripts')--}}
{{--<script>--}}
    {{--// TODO : Redo this. It's a piece of shit and need VUE--}}
    {{--$(function(){--}}

        {{--$('.btn-follow').click(function(e){--}}
            {{--e.preventDefault();--}}
            {{--var that = $(this).addClass('disabled').prop('disabled', true);--}}
            {{--$.ajax({--}}
                {{--type: 'POST',--}}
                {{--url: "{{ app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('groups.followers.store', [$item->id]) }}"--}}
            {{--}).done(function( msg ) {--}}
                {{--$('.btn-follow').removeClass('hide').hide().removeClass('disabled').prop('disabled', false);--}}
                {{--$('.btn-following').show().removeClass('hide');--}}
            {{--});--}}
        {{--});--}}

        {{--$('.btn-following').click(function(e){--}}
            {{--e.preventDefault();--}}
            {{--var that = $(this).addClass('disabled').prop('disabled', true);--}}
            {{--$.ajax({--}}
                {{--type: 'DELETE',--}}
                {{--url: "{{ app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('groups.followers.destroy', [$item->id, 'x']) }}"--}}
            {{--}).done(function( msg ) {--}}
                {{--$('.btn-following').removeClass('hide').hide().removeClass('disabled').prop('disabled', false);--}}
                {{--$('.btn-follow').removeClass('hide').show();--}}
            {{--});--}}
        {{--});--}}

    {{--});--}}
{{--</script>--}}
{{--@endpush--}}