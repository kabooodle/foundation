
<td><input type="checkbox" class="shipment_checkbox"></td>
<td>
    <div class="avatar-thumbnail-container">
        <div class="avatar-thumbnail _32">
            <img src="{{ $sale->listable->cover_photo->location }}">
        </div>
        <span>{{ $sale->listable->title }}</span>
    </div>
</td>
<td>{{ $sale->accepted_price ? currency($sale->accepted_price) : currency($sale->price) }}</td>
<td>{{ $sale->humanizeNoTime($sale->accepted_on) }}</td>
<td>{!! $sale->claimer->username !!}</td>
<td>
    {!! $sale->present()->getShippingStatus() !!}
</td>
<td>
    <div class="pull-right">
        @if($sale->shipmentTransaction())
            <a class="btn white btn-xs" href="{{ route('merchant.shipping.transactions.show', [$sale->shipmentTransaction()->shipping_shipments_uuid, $sale->shipmentTransaction()->uuid]) }}">Track Shipping</a>
        @else
            @if($sale->queuedToShip())
                <button
                        type="button"
                        data-route="{{apiRoute('claims.toggle', [$sale->id])}}"
                        data-toggle="toggleShippingMethod"
                        data-method="external"
                        class="btn btn-xs white"
                >Ship Externally</button>
            @else
                <button
                        type="button"
                        data-route="{{apiRoute('claims.toggle', [$sale->id])}}"
                        data-toggle="toggleShippingMethod"
                        data-method="kabooodle"
                        class="btn btn-xs white"
                >Ship via Kabooodle</button>
            @endif
        @endif
    </div>
</td>
