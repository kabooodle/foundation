@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Account Credits</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <p>Buy credits in bulk for larger discounts:</p>
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
                            <label class="btn white pull-right m-a-0">
                                <input type="radio" data-type="select" name="p" data-id="{{ $type->id }}" value="{{ $type->id }}"> Select
                            </label>
                            <button type="submit" data-type="buy" class="btn m-a-0 primary pull-right" data-id="{{ $type->id }}" style="display: none;">Purchase</button>
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
            <h6 class="_400 m-b-1 l-h-1x" style="line-height:22px;">Credits are the most flexible way to download
                photos, illustrations and vectors and the only way to download video clips on iStock.</h6>
            <h6 class="_400 l-h-1x m-b-0" style="line-height:26px;">It’s up to you how you want to use credits – buy
                just enough for the images you need or stock up and save on future downloads – the more you buy, the
                less they cost. Unlike your monthly download limit with a subscription, there’s no limit for when you
                use your credit downloads. And since they won’t expire as long as you sign in to your account at least
                once a year, they’ll always be there when you need them.</h6>
        </div>
    </div>

@endsection


@push('footer-scripts')
<script>
    $(function(){
        var selectBtnEl = $('[data-type="select"]');
        selectBtnEl.on('click change', function(e){
            var $that = $(this);
            $that.parent().hide();
            var purchaseBtn = $that.closest('.action-column').find('[data-type="buy"]');
            purchaseBtn.show();
            $(document).find('[data-type="buy"]').not(purchaseBtn).hide();
            $(document).find('[data-type="select"]').not($that).prop('checked', false).parent().show();
        });

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
            });
        });
    });
</script>
@endpush