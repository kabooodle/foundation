@extends('layouts.full')

@section('body-content-left-nav')
    @include('groups.partials._leftnav')
@endsection


@section('body-menu')
    @include('groups.partials._bodymenu')
@endsection


@section('body-content')
    @include('groups.partials._groupheader')
@endsection