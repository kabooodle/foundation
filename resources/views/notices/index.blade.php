@extends('layouts.full')

@section('body-menu')
    <div class="btn-toolbar center-block text-center">
        <div class="btn-group">
            <a href="" class="btn btn-sm white" >Mark all as read</a>
        </div>
    </div>
@endsection

@section('body-content')
    @foreach($notices as $notice)
        <p>{{ $notice->title }}</p>
    @endforeach

@endsection