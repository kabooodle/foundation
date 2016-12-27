

<script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
<script src="https://cdn.jsdelivr.net/hogan.js/3.0/hogan.min.js"></script>
<script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
<script>
    var client = algoliasearch("CVR820XJXD", "e65243328471ab8b69e6effec8d65c23");
    var teams = client.initIndex('groups_local');
    var players = client.initIndex('users_local');
    var inventory = client.initIndex('inventory_local');
    var flashsales = client.initIndex('flashsales_local');

    // Mustache templating by Hogan.js (http://mustache.github.io/)
    var templatePlayer = Hogan.compile('<div class="player">' +
            '<div class="name">@{{{ _highlightResult.name.value }}} - User</div>' +
            '</div>');
    var templateTeam = Hogan.compile('<div class="team">' +
            '<div class="name">@{{{ _highlightResult.name.value }}} - Group</div>' +
            '</div>');
    var templateInventory = Hogan.compile('<div class="team">' +
            '<div class="name">@{{{ _highlightResult.name.value }}} - Item</div>' +
            '</div>');
    var templateFlashsales = Hogan.compile('<div class="team">' +
            '<div class="name">@{{{ _highlightResult.name.value }}} - Flash Sale</div>' +
            '</div>');

    // autocomplete.js initialization
    autocomplete('#app_search', {hint: false}, [
        {
            source: autocomplete.sources.hits(teams, {hitsPerPage: 5}),
            displayKey: 'name',
            templates: {
//                header: '<div class="category">Groups</div>',
                suggestion: function(hit) {
                    // render the hit using Hogan.js
                    return templateTeam.render(hit);
                },
//                empty: function(options) {
//                    return '<div>No results found.</div>';
//                }
            }
        },
        {
            source: autocomplete.sources.hits(players, {hitsPerPage: 5}),
            displayKey: 'name',
            templates: {
//                header: '<div class="category">Users</div>',
                suggestion: function(hit) {
                    return templatePlayer.render(hit);
                },
//                empty: function(options) {
//                    return '<div>No results found.</div>';
//                }
            }
        },
        {
            source: autocomplete.sources.hits(inventory, {hitsPerPage: 5}),
            displayKey: 'name',
            templates: {
//                header: '<div class="category">Users</div>',
                suggestion: function(hit) {
                    return templateInventory.render(hit);
                },
//                empty: function(options) {
//                    return '<div>No results found.</div>';
//                }
            }
        },
        {
            source: autocomplete.sources.hits(flashsales, {hitsPerPage: 5}),
            displayKey: 'name',
            templates: {
//                header: '<div class="category">Users</div>',
                suggestion: function(hit) {
                    return templateFlashsales.render(hit);
                },
//                empty: function(options) {
//                    return '<div>No results found.</div>';
//                }
            }
        }

    ]).on('autocomplete:selected', function(event, suggestion, dataset) {
        if (suggestion.route) {
            window.location.replace(suggestion.route);
        }
    });
</script>


<div class="footer prpl-800 dk pos-rlt" style="background: url('{{ staticAsset('/assets/images/shopping-bg.png') }}') bottom repeat-x;">
    <div class="padding">
        <div class=" p-a center-block">
            <h3 class="text-center text-white footer-slogan">Start with a 30 day free trial!</h3>
            <h5 class="text-center text-white ">Join a community where you can sell and buy everything and more!</h5>
            <div class="center-block text-center m-t-1">
                <a class="btn primary btn-lg text-center" href="#">Join Kabooodle Free</a>
            </div>

        </div>
    </div>
</div>

<div class="prpl-800 dk pos-rlt">
    <div class="p-md">
        <div class="p-a">
            <div class="row">
                <div class="col-md-3">
                    <div class="clearfix">
                        <a class="navbar-brand kabooodle-brand"><span class=
                                                               "hidden-folded inline"><i class="fa fa-archive" aria-hidden="true"></i> {{ env('APP_NAME') }}</span></a>
                    </div>
                    <div class="m-v-md">
                        <p ><strong>Your everything and more!</strong></p>
                        <p class="text-muted footer-about">We are a social activity or something cool about what we do and why it makes sense use {{ env('APP_NAME') }}, whether as a merchant or buyer.</p>
                    </div>
                </div>
                <div class="col-md-6 text-warning-hover">
                    <div class="row m-t-md">
                        <div class="col-sm-6">
                            <h6 class="text-u-c m-b">Apps</h6>
                            <div class="m-b-md">
                                <ul class="nav l-h-2x">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('api.index') }}">Developer API</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Inbox</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href=
                                        "">Calendar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Contact</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Note</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Todo</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-u-c m-b">Blog</h6>
                            <div class="m-b-md">
                                <ul class="nav l-h-2x">
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Life
                                            style</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Fashion</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Travel</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href=
                                        "">Entertainment</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="">Local</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-u-c m-b m-t-md">
                        asdfasdf
                    </div>
                </div>
            </div>
            <div class="b b-b m-b m-t-lg"></div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="clearfix">
                        <a class="btn btn-icon btn-social btn-sm white" href=
                        "https://www.facebook.com/kabooodle"><i class="fa fa-facebook"></i> <i class=
                                                             "fa fa-facebook indigo"></i></a> <a class=
                                                                                                 "btn btn-icon btn-social btn-sm white" href=
                                                                                                 ""><i class="fa fa-twitter"></i> <i class=
                                                                                                                                     "fa fa-twitter blue"></i></a> <a class=
                                                                                                                                                                      "btn btn-icon btn-social btn-sm white" href=
                                                                                                                                                                      ""><i class="fa fa-google-plus"></i> <i class=
                                                                                                                                                                                                              "fa fa-google-plus red"></i></a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="text-sm-right text-xs-left">
                        <small class="text-muted">&copy; 2016</small>
                        <small class="block text-xs text-muted">v.{{ getAppVersion() }}</small>
                        @if(env('APP_ENV') <> 'production')
                            <small class="block text-xs text-muted">release v.{{ getReleaseVersion() }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






