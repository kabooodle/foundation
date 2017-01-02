

{{--<script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/hogan.js/3.0/hogan.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>--}}
{{--<script>--}}
    {{--var client = algoliasearch("CVR820XJXD", "e65243328471ab8b69e6effec8d65c23");--}}
    {{--var teams = client.initIndex('groups_local');--}}
    {{--var players = client.initIndex('users_local');--}}
    {{--var inventory = client.initIndex('inventory_local');--}}
    {{--var flashsales = client.initIndex('flashsales_local');--}}

    {{--// Mustache templating by Hogan.js (http://mustache.github.io/)--}}
    {{--var templatePlayer = Hogan.compile('<div class="player">' +--}}
            {{--'<div class="name">@{{{ _highlightResult.name.value }}} - User</div>' +--}}
            {{--'</div>');--}}
    {{--var templateTeam = Hogan.compile('<div class="team">' +--}}
            {{--'<div class="name">@{{{ _highlightResult.name.value }}} - Group</div>' +--}}
            {{--'</div>');--}}
    {{--var templateInventory = Hogan.compile('<div class="team">' +--}}
            {{--'<div class="name">@{{{ _highlightResult.name.value }}} - Item</div>' +--}}
            {{--'</div>');--}}
    {{--var templateFlashsales = Hogan.compile('<div class="team">' +--}}
            {{--'<div class="name">@{{{ _highlightResult.name.value }}} - Flash Sale</div>' +--}}
            {{--'</div>');--}}

    {{--// autocomplete.js initialization--}}
    {{--autocomplete('#app_search', {hint: false}, [--}}
        {{--{--}}
            {{--source: autocomplete.sources.hits(teams, {hitsPerPage: 5}),--}}
            {{--displayKey: 'name',--}}
            {{--templates: {--}}
{{--//                header: '<div class="category">Groups</div>',--}}
                {{--suggestion: function(hit) {--}}
                    {{--// render the hit using Hogan.js--}}
                    {{--return templateTeam.render(hit);--}}
                {{--},--}}
{{--//                empty: function(options) {--}}
{{--//                    return '<div>No results found.</div>';--}}
{{--//                }--}}
            {{--}--}}
        {{--},--}}
        {{--{--}}
            {{--source: autocomplete.sources.hits(players, {hitsPerPage: 5}),--}}
            {{--displayKey: 'name',--}}
            {{--templates: {--}}
{{--//                header: '<div class="category">Users</div>',--}}
                {{--suggestion: function(hit) {--}}
                    {{--return templatePlayer.render(hit);--}}
                {{--},--}}
{{--//                empty: function(options) {--}}
{{--//                    return '<div>No results found.</div>';--}}
{{--//                }--}}
            {{--}--}}
        {{--},--}}
        {{--{--}}
            {{--source: autocomplete.sources.hits(inventory, {hitsPerPage: 5}),--}}
            {{--displayKey: 'name',--}}
            {{--templates: {--}}
{{--//                header: '<div class="category">Users</div>',--}}
                {{--suggestion: function(hit) {--}}
                    {{--return templateInventory.render(hit);--}}
                {{--},--}}
{{--//                empty: function(options) {--}}
{{--//                    return '<div>No results found.</div>';--}}
{{--//                }--}}
            {{--}--}}
        {{--},--}}
        {{--{--}}
            {{--source: autocomplete.sources.hits(flashsales, {hitsPerPage: 5}),--}}
            {{--displayKey: 'name',--}}
            {{--templates: {--}}
{{--//                header: '<div class="category">Users</div>',--}}
                {{--suggestion: function(hit) {--}}
                    {{--return templateFlashsales.render(hit);--}}
                {{--},--}}
{{--//                empty: function(options) {--}}
{{--//                    return '<div>No results found.</div>';--}}
{{--//                }--}}
            {{--}--}}
        {{--}--}}

    {{--]).on('autocomplete:selected', function(event, suggestion, dataset) {--}}
        {{--if (suggestion.route) {--}}
            {{--window.location.replace(suggestion.route);--}}
        {{--}--}}
    {{--});--}}
{{--</script>--}}

{{--@include('layouts.footer._footerbanner')--}}

@include('layouts.footer._footermeta')




