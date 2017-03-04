<tr>
    <td> <div class="avatar-thumbnail-container">
            <div class="avatar-thumbnail _32">
                <img src="{{ $item->listedItem->cover_photo->location }}">
            </div>
            <span>{{ $item->listedItem->name_with_variant }}</span>
        </div></td>
    <td>{{ $item->sales->count() }}</td>
    <td>{{ $item->pendingSales->count() }}</td>
    <td>{{ $item->views->count() }}</td>
    <td>{{ $item->watchers->count() }}</td>
    <td>${{ $item->sales->sum('price') }}</td>
    @if($item->type <> Kabooodle\Models\Listings::TYPE_CUSTOM)
        <td>{!! $item->present()->getStatus()  !!} {!! $item->present()->facebookPhotoLink() !!}</td>
    @endif
    <td>
        <div class="pull-md-right">
            <a class="btn btn-xs white pull-left" href="{{ route('listingitems.show', [$item->obfuscateIdToString()]) }}">View item listing</a>
            @if($item->type == Kabooodle\Models\Listings::TYPE_FACEBOOK && ! in_array($item->status,['deleted', 'scheduled_delete', 'queued_delete']))
                <div class="dropdown pull-right m-l-xs">
                    <a class="btn btn-xs white dropdown-toggle no-caret" href="#" data-toggle="dropdown">
                        <i class="hidden-sm-down fa fa-ellipsis-h" aria-hidden="true"></i>
                        <span class="hidden-sm-up">Options</span>
                    </a>
                    <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                        <a @click.prevent="deleteListingItem('{{  apiRoute('listings.listingitems.destroy', [$item->listing_id, $item->id])  }}', {{ $item->id }}, '{{ $item->type }}', $event)" type="button" class="text-danger bg-danger-hover text-white-hover dropdown-item" >Delete</a>
                        <a @click.prevent="deleteFacebookListingItem('{{  apiRoute('listings.listingitems.facebook.destroy', [$item->listing_id, $item->id])  }}', {{ $item->id }}, '{{ $item->type }}', $event)" type="button" class="text-danger bg-danger-hover text-white-hover dropdown-item" >Delete from Facebook</a>
                    </div>
                </div>
            @else
                <button @click.prevent="deleteListingItem('{{  apiRoute('listings.listingitems.destroy', [$item->listing_id, $item->id])  }}', {{ $item->id }}, '{{ $item->type }}', $event)" type="button" class="m-l-xs btn btn-xs white pull-right" >Delete</button>
            @endif
        </div>
    </td>
</tr>