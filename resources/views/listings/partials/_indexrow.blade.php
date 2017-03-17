<tr data-id="{{ $listing->id }}">
    {{--@unless(isset($_excludeActionCol))--}}
    {{--<td><input type="checkbox"></td>--}}
    {{--@endunless--}}
    <td>@include('listings._listingtype', ['_type' => $listing->type])
        @if($listing->type == Kabooodle\Models\Listings::TYPE_CUSTOM)
            {{ $listing->custom_name }}
        @elseif($listing->type ==  Kabooodle\Models\Listings::TYPE_FACEBOOK)
            {{$listing->fb_name}}
        @else
            {{ $listing->flashsale_name }}
        @endif
    </td>
    <td>{{ $listing->scheduled_for ? humanizeDateTime($listing->scheduled_for) : null }}</td>
    <td>{{ $listing->type ==  Kabooodle\Models\Listings::TYPE_FACEBOOK ? $listing->albums_count : 'n/a' }}</td>
    <td>{{ $listing->items_count }}</td>
    <td>{{ $listing->accepted_sales_count }}</td>
    <td>{{ $listing->pending_sales_count }}</td>
        <td>{{ $listing->pageviews_count }}</td>
    <td>${{ $listing->gross }}</td>
    <td>@if($listing->type == Kabooodle\Models\Listings::TYPE_CUSTOM) n/a @else {!! listingStatusHtml($listing->status) !!} @endif</td>
    @unless(isset($_excludeActionCol))
    <td>
        <div class="pull-md-right">
            <div class="dropdown">
                <a class="btn btn-xs white dropdown-toggle no-caret" href="#" data-toggle="dropdown">
                    <i class="hidden-sm-down fa fa-ellipsis-h" aria-hidden="true"></i>
                    <span class="hidden-sm-up">Options</span>
                </a>
                <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                    <a href="{{ route('listings.show', [$listing->uuid]) }}" class="dropdown-item">Public listing</a>
                    <div class="divider"></div>
                    @if(in_array($listing->type, [Kabooodle\Models\Listings::TYPE_FLASHSALE,  Kabooodle\Models\Listings::TYPE_CUSTOM]))
                    <a href="{{ route('merchant.listings.group.show', [ $listing->uuid, $listing->type, $listing->flashsale_id ]) }}" class="dropdown-item">Manage items</a>
                    @elseif($listing->type == Kabooodle\Models\Listings::TYPE_FACEBOOK)
                        <a href="{{ route('merchant.listings.show', [$listing->uuid]) }}" class="dropdown-item">Manage albums</a>
                    @endif
                    <div class="divider"></div>
                    @if(! in_array($listing->status,['processing_delete', 'queued_delete']))
                    <button type="button" @click="deleteListingItem('{{ apiRoute('listings.destroy', [$listing->id]) }}', {{ $listing->id }}, $event)" class="text-danger bg-danger-hover text-white-hover dropdown-item">Delete</button>
                    @endif
                    @if($listing->type == Kabooodle\Models\Listings::TYPE_FACEBOOK && ! in_array($listing->status,['deleted', 'scheduled_delete', 'processing_delete', 'queued_delete']))
                        <button type="button" @click="deleteFacebookListing('{{ apiRoute('listings.facebook.destroy', [$listing->id]) }}', {{ $listing->id }}, $event)" class="text-danger bg-danger-hover text-white-hover dropdown-item">Delete from Facebook</button>
                    @endif
                </div>
            </div>
        </div>
    </td>
    @endunless
</tr>