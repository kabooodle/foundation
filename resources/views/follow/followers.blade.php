@extends('layouts.full')

@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Followers</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div id="claims__wrapper">
                <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                    <thead>
                    </thead>
                    <tbody>
                        @if($followers->count() > 0)
                            @foreach($followers as $follower)
                                <p>fuck</p>
                            @endforeach
                        @else
                            <p>You have no followers! :(</p>
                        @endif
                    </tbody>
                </table>

            </div>

        </div>
    </div>

@endsection