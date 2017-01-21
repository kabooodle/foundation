 @if($_type == Kabooodle\Models\Listings::TYPE_FACEBOOK)
     <img style="vertical-align:top" src="{{staticAsset('/assets/images/icons/FB-f-Logo__blue_18.png')}}" height="{{ $_size or 18 }}" title="Facebook Group">
 @else
     <img style="vertical-align:top"  src="{{ staticAsset('/assets/images/icons/kabooodle_logo_18.png') }}" height="{{ $_size or 18 }}" title="Kabooodle Flashsale">
@endif