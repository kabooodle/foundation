<tr>
    @unless(isset($_excludeActionCol))
    <td><input type="checkbox"></td>
    @endunless
    <td>{{ $listing->type ==  Kabooodle\Models\Listings::TYPE_FACEBOOK ? $listing->fb_name : $listing->flashsale_name }} @include('listings._listingtype', ['_type' => $listing->type])</td>
    <td>{{ humanizeDateTime($listing->scheduled_for) }}</td>
    <td>{{ $listing->use_link == 1 ? 'Yes' : 'No' }}</td>
    <td>{{ $listing->albums_count }}</td>
    <td>{{ $listing->items_count }}</td>
    <td>{{ $listing->accepted_sales_count }}</td>
    <td>{{ $listing->pending_sales_count }}</td>
    <td>${{ $listing->gross }}</td>
    <td>{!! listingStatusHtml($listing->status) !!}</td>
    @unless(isset($_excludeActionCol))
    <td>
        <div class="pull-md-right">
            <div class="dropdown">
                <a class="text-muted btn btn-xs white dropdown-toggle no-caret" href="#" data-toggle="dropdown">
                    <i class="hidden-sm-down fa fa-ellipsis-h" aria-hidden="true"></i>
                    <span class="hidden-sm-up">Options</span>
                </a>
                <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                    @if(Kabooodle\Models\Listings::isStillEditable($listing->status))
                        <a href="{{ route('listings.edit', [$listing->uuid]) }}" class="dropdown-item">Edit</a>
                    @endif
                    <a href="{{ route('listings.show', [$listing->uuid]) }}" class="dropdown-item">View</a>
                    <a href="{{ route('listings.show', [$listing->uuid]) }}" class="text-warning dropdown-item">Delete</a>
                </div>
            </div>
        </div>
    </td>
    @endunless
</tr>