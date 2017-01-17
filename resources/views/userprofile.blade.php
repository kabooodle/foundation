@extends('welcome')
@section('profile-body')
        <div class="col-sm-12 col-md-push-3 col-md-6">
            <div class="box">
                <div class="box-header b-b">
                    <h3>What's  New</h3>
                    {{ var_dump(auth()->user()) }}
                </div>
                <div class="box-body">
                    <div class="row row-sm">
                        <div class="col-xs-4">
                            <a href>
                                <img src="https://unsplash.it/160/160/?random" class="w-full"></img>
                            </a>
                        </div>
                        <div class="col-xs-4">
                            <a href>
                                <img src="https://unsplash.it/160/160/?random" class="w-full"></img>
                            </a>
                        </div>
                        <div class="col-xs-4">
                            <a href>
                                <img src="https://unsplash.it/160/160/?random" class="w-full"></img>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <form>
                    <textarea class="form-control no-border" rows="3" placeholder="Type something..."></textarea>
                </form>
                <div class="box-footer clearfix">
                    <button class="btn btn-info pull-right btn-sm">Post</button>
                    <ul class="nav nav-pills nav-sm">
                        <li class="nav-item"><a class="nav-link" href><i class="fa fa-camera text-muted"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href><i class="fa fa-video-camera text-muted"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h3>Feeds <span class="label success">5</span></h3>
                </div>
                <div class="box-body">
                    <div class="streamline b-l m-l-md">
                        <div class="sl-item">
                            <div class="sl-left">
                                <img src="https://unsplash.it/32/32/?random" class="img-circle">
                            </div>
                            <div class="sl-content">
                                <div class="sl-date text-muted">2 minutes ago</div>
                                <div class="sl-author">
                                    <a href>Peter Joo</a>
                                </div>
                                <div>
                                    <p>Consectetur adipiscing elit. Morbi id neque quam. Aliquam sollicitudin venenatis ipsum ac feugiat. Vestibulum ullamcorper sodales nisi nec condimentum. Mauris convallis mauris at pellentesque volutpat. Phasellus at ultricies neque, quis malesuada augue. Donec eleifend</p>
                                </div>
                                <div class="sl-footer">
                                    <a href data-toggle="collapse" data-target="#reply-1">
                                        <i class="fa fa-fw fa-mail-reply text-muted"></i> Reply
                                    </a>
                                </div>
                                <div class="box collapse m-a-0 b-a" id="reply-1">
                                    <form>
                                        <textarea class="form-control no-border" rows="3" placeholder="Type something..."></textarea>
                                    </form>
                                    <div class="box-footer clearfix">
                                        <button class="btn btn-info pull-right btn-sm">Post</button>
                                        <ul class="nav nav-pills nav-sm">
                                            <li class="nav-item"><a class="nav-link" href><i class="fa fa-camera text-muted"></i></a></li>
                                            <li class="nav-item"><a class="nav-link" href><i class="fa fa-video-camera text-muted"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img src="https://unsplash.it/32/32/?random" class="img-circle">
                            </div>
                            <div class="sl-content">
                                <div class="sl-date text-muted">8:30</div>
                                <div class="sl-author">
                                    <a href>Moke</a>
                                </div>
                                <div>
                                    <p>Just followed <a href class="text-info">Jacob</a> and she followed you too.</p>
                                    <p>
                                  <span class="inline p-a-xs b-a dark-white">
                                    <img src="https://unsplash.it/32/32/?random" class="img-responsive">
                                  </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left">
                                <img src="https://unsplash.it/32/32/?random" class="img-circle">
                            </div>
                            <div class="sl-content">
                                <div class="sl-date text-muted">Sat, 5 Mar</div>
                                <div class="sl-author">
                                    <a href>Moke</a>
                                </div>
                                <blockquote>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante soe aiea ose dos soois.</p>
                                    <small>Someone famous in <cite title="Source Title">Source Title</cite></small>
                                </blockquote>


                                <div class="sl-item">
                                    <div class="sl-left">
                                        <img src="https://unsplash.it/32/32/?random" class="img-circle">
                                    </div>
                                    <div class="sl-content">
                                        <div class="sl-date text-muted">Sun, 11 Feb</div>
                                        <p><a href class="text-info">Jessi</a> assign you a task <a href class="text-info">Mockup Design</a>.</p>
                                    </div>
                                </div>
                                <div class="sl-item">
                                    <div class="sl-left">
                                        <img src="https://unsplash.it/32/32/?random" class="img-circle">
                                    </div>
                                    <div class="sl-content">
                                        <div class="sl-date text-muted">Thu, 17 Jan</div>
                                        <p>Follow up to close deal</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>

    @endsection