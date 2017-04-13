<div class="box white">
    <div class="row-col m-b">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-8">
                    <div class="box no-shadow white p-a">
                        <div id="item-{{$listable->id}}-carousel" class="carousel image-carousel-container slide" data-ride="carousel">
                            <div class="carousel-outer">
                                <div class="carousel-inner" role="listbox">
                                    @foreach($listable->getAllImages() as $key => $image)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : null }}" >
                                            <img
                                                src="{{ $image->location }}"
                                            >
                                            <p class="text-muted text-sm text-center m-b-0"
                                               data-toggle="lightbox"
                                               data-remote="{{ $image->location }}"
                                               data-gallery="gallery"
                                            >Enlarge Image <i class="fa fa-search-plus" aria-hidden="true"></i></p>
                                        </div>
                                    @endforeach
                                </div>
                                <a style="background: none;" class="left carousel-control" href="#item-{{$listable->id}}-carousel" role="button" data-slide="prev">
                                    <span class="icon-prev" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a style="background: none;" class="right carousel-control" href="#item-{{$listable->id}}-carousel" role="button" data-slide="next">
                                    <span class="icon-next" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                            <ol class="carousel-indicators">
                                @foreach($listable->getAllImages() as $key => $image)
                                    <li data-target="#item-{{$listable->id}}-carousel" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : null }}">
                                        <img src="{{ $image->location }}" style="width: 64px; height: 64px;">
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box-header no-shadow">
                        <h2><span class="_800">{!! $listable->title !!}</span></h2>
                        <p class="m-b-0 m-t-1 h4 text-warning _500">{{ isset($_price) ? currency($_price) : currency($listable->getPrice()) }}</p>
                        @if(webuser() && $listable->user_id == webUser()->id)
                            <table id="listable-qty" class="table table-condensed m-t-1 text-muted">
                                <thead class="text-primary">
                                    <tr>
                                        <th colspan="2">Quantity</th>
                                    </tr>
                                </thead>
                                <tfoot class="text-primary">
                                    <tr>
                                        <th>Available</th>
                                        <th class="text-right">{!! $listable->available_quantity !!}</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <tr>
                                        <td>On hand</td>
                                        <td class="text-right">{!! $listable->initial_qty !!}</td>
                                    </tr>
                                    <tr>
                                        <td> - On hold</td>
                                        <td class="text-right">{!! $listable->getOnHoldQuantity() !!}</td>
                                    </tr>
                                    @if($listable instanceof \Kabooodle\Models\Inventory)
                                        <tr>
                                            <td> - In locked outfits</td>
                                            <td class="text-right">{!! $listable->lockedGroupings->count() !!}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @endif
                        @if(! webuser() || $listable->user_id != webUser()->id)
                            <div class="m-t-2 m-b-0">
                                <v-card
                                    extra_class="list-group-item no-border b-a-0 "
                                    :already_following="{{ $listable->user->is_following ? 1 : 0 }}"
                                    follow_endpoint="{{ apiRoute('user.followers.store', [$listable->owner->id]) }}"
                                    able_type="{{ get_class($listable->owner) }}"
                                    able_id="{{ $listable->user->id }}"
                                    :user="{{ $listable->user }}"
                                    message_endpoint="{{ apiRoute('messenger.store') }}"
                                ></v-card>
                            </div>
                        @endif
                    </div>
                    <div class="box-body">
                        <p class="m-b-lg text-muted text">{!! nl2br($listable->description) !!}</p>
                    </div>
                    <div class="box no-shadow white p-a">
                        {!! $listable->present()->listableShowOutfitSection(isset($listingItem) ? $listingItem : null) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 b-l no-shadow">

            <div class=" p-a-md">
                <h6 class="text-muted">Categories</h6>
                @if($listable->categories->count() > 0)
                    @foreach($listable->categories as $tag)
                        <span class="label">{{ $tag->name }}</span>
                    @endforeach
                @else
                    <small class="text-muted text-sm"><em>None</em></small>
                @endif
            </div>
        </div>
    </div>
</div>
