<p>You recently created a new listing for {{ $listing->type }} ({{ $listing->sale_name }}).</p>

@if($listing->isFacebook())
<p>The listing is scheduled for {{ $listing->scheduled_for->setTimezone($timezone) }}.</p>
<p>Options you included are:</p>
<ul>
    @if($listing->include_link_in_descr)
    <li>Include link to {{ env('APP_NAME') }} claim page, in description</li>
    @endif

    @if($listing->scheduled_until)
    <li>Date to remove the listing: {{ $listing->scheduled_until->setTimezone($timezone) }}</li>
    @endif

    @if($listing->claimable_at)
    <li>Start date for claiming: {{ $listing->claimable_at->setTimezone($timezone) }}</li>
    @endif

    @if($listing->claimable_until)
    <li>Last date for claiming: {{ $listing->claimable_until->setTimezone($timezone) }}</li>
    @endif
</ul>
<p>You can make changes to the listing until it has been queued to be listed to Facebook.  Once its been queued or listed, you can only remove the listing and/or items.</p>
<h3>Share the link!</h3>
<p>If you wish to share the page containing your listing, use this link:  <pre>{{ route('listings.shorthand', [$listing->uuid]) }}</pre></p>
@endif

<br><br>
--
the {{ env('APP_NAME') }} Team