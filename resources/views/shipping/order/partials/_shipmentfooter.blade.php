<div class="row">
    <div class="col-md-4">
        <div class="box">
            <div class="box-header">
                <h4 class="m-b-0">Claims</h4>
            </div>
            <div class="box-divider"></div>
            @foreach($shipment->claims as $claim)
                <div class="box-body">
                    <div class="avatar-thumbnail-container">
                        <div class="avatar-thumbnail _32">
                            <img src="{{$claim->listable->cover_photo->location}}">
                        </div>
                    <span>{{ $claim->claimer->username }} - {{ $claim->listable->getTitle() }} - ${{ $claim->listable->price_usd }}</span>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    <div class="col-md-4">
        <div class="box">
            <div class="box-header">
                <h4 class="m-b-0">Parcel</h4>
            </div>
            <div class="box-divider"></div>
            <div class="box-body">
                {{ $shipment->parcelTemplate ? $shipment->parcelTemplate->name  : 'Custom' }}
                <br>
                {{ $shipment->getMeasurements() }}
                <br>
                {{ $shipment->parcel_data['weight'] }} {{ $shipment->parcel_data['mass_unit'] }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box">
            <div class="box-header">
                <h4 class="m-b-0">Recipient Address</h4>
            </div>
            <div class="box-divider"></div>
            <div class="box-body">
                <address class="m-b-0">
                    <span class="_500 block">{{ $shipment->recipient_data->name }}</span>
                    <span class="block">{{ $shipment->recipient_data->street1 }}</span>
                    @if($shipment->recipient_data->street2)
                        <span class="block">{{ $shipment->recipient_data->street2 }}</span>
                    @endif
                    <span class="block">{{ $shipment->recipient_data->city }}, {{ $shipment->recipient_data->state }}, {{ $shipment->recipient_data->zip }}</span>
                    <a href="mailto:{{ $shipment->recipient_data->email }}">{{ $shipment->recipient_data->email }}</a>
                    @if($shipment->recipient_data->phone)
                        <span class="block">{{ $shipment->recipient_data->phone }}</span>
                    @endif
                </address>
            </div>
        </div>
    </div>
</div>