<ul class="timeline" id="timeline">

    @include('shipping.order.partials._shipping_status_item', [
        '_itemStatus' => 'created',
        '_itemTimestamp' => $transaction->createdAtHuman(),
        '_itemName'=>'Created'
    ])

    {{--@include('shipping.order.partials._shipping_status_item', [--}}
        {{--'_itemStatus' => $transaction->isLabelPrinted() ? 'created' : null,--}}
        {{--'_itemTimestamp' => $transaction->isLabelPrinted() ? $transaction->getShippingStatusHuman() : 'Pending',--}}
        {{--'_itemName'=>'Label Created'--}}
    {{--])--}}

    @include('shipping.order.partials._shipping_status_item', [
        '_itemStatus' => $transaction->isWithCarrierHistory() ? 'with_carrier' : null,
        '_itemTimestamp' => $transaction->isWithCarrierHistory() ? $transaction->isWithCarrierHistory()->created_at->format('m-d-Y h:ia') : null,
        '_itemName'=>'With Carrier'
    ])

    @include('shipping.order.partials._shipping_status_item', [
        '_itemStatus' => $transaction->isInTransitHistory() ? 'in_transit' : null,
        '_itemTimestamp' => $transaction->isInTransitHistory() ? $transaction->isInTransitHistory()->created_at->format('m-d-Y h:ia') : null,
        '_itemName'=>'In Transit'
    ])

    @include('shipping.order.partials._shipping_status_item', [
        '_itemStatus' => $transaction->isDeliveredHistory() ? 'complete' : null,
        '_itemTimestamp' => $transaction->isDeliveredHistory() ? $transaction->isDeliveredHistory()->created_at->format('m-d-Y h:ia') : null,
        '_itemName'=>'Delivered'
    ])
</ul>