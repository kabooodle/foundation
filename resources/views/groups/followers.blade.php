@extends('layouts.full')

@section('body-menu')
    @include('groups.partials._bodymenu')
@endsection

@section('body-content')

    @include('groups.partials._groupheader')

    @include('groups.partials._followers')

@endsection