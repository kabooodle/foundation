@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Account Credits</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table">
                @foreach(creditTypes() as $type)
                    <tr>
                        <td style="vertical-align: middle !important">
                            <span style="font-size: 28px;">${{ $type->credits_equiv}}</span>
                            <span class="block text-muted">credits</span>
                        </td>
                        <td style="vertical-align: middle !important">
                            <span  style="font-size: 28px;">${{  $type->amount  }}</span> USD
                            <span class="block text-muted">${{ $type->per_credit }} per credit</span>
                        </td>
                        <td class="action-column" style="vertical-align: middle !important">
                            {{ Form::open() }}
                            <input type="hidden" name="p" data-id="{{ $type->id }}" value="{{ $type->id }}">
                            <button type="submit" data-type="buy" class="btn white pull-right" data-id="{{ $type->id }}" >Purchase</button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                @endforeach
            </table>

        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <h4 class="m-b-1">What are credits?</h4>
            <h6 class="_400 m-b-1 l-h-1x" style="line-height:22px;">Credits are used to pay for shipping costs and shipping labels. There's no limit, and don't expire.</h6>
        </div>
    </div>

@endsection


@push('footer-scripts')
<script>
    $(function(){
        var buyBtnEl = $('[data-type="buy"]');
        buyBtnEl.click(function(e){
            e.preventDefault();
            var $that = $(this);
            var planId = $that.attr('data-id');

            confirmModal(function($noty){
                $noty.$buttons.find('.btn').each(function(i,x){
                    $(this).addClass('disabled').prop('disabled', true);
                });

                setTimeout(function(){
                    $that.closest('form').submit();
                }, 100);
            },function(){
            });
        });
    });
</script>
@endpush