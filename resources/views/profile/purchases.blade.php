@extends('layouts.full')

@section('body-menu')

    <div class="btn-toolbar center-block text-center">
        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Filter</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Claimed</a>
                <a class="dropdown-item" href="">Purchased</a>
            </div>
        </div>
    </div>

@endsection

@section('body-content')
    <div class="box white">
        <div class="row-col m-b">
            <div class="col-md-6">
                <div class="box no-shadow white p-a">
                    <div class="carousel image-carousel-container slide" data-ride="carousel">
                        <div class="carousel-outer">
                            <div class="carousel-inner" role="listbox">
                                <p>Purchases</p>
                            </div>
            </div>
        </div>
    </div>

@endsection