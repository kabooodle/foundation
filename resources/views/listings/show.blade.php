@extends('layouts.full', ['contentId' => 'listing-page'])

@section('body-menu')
    <button class="btn white btn-sm">Filter</button>
@endsection

@section('body-content')

    <div class="row">
        <div class="col-md-3">
            <div class="list-group m-b">
                <a href="" class="list-group-item">
                    <span class="pull-right label">12</span>
                    Carly
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">5</span>
                    Lindsey's
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right text-muted m-l-xs"></span>
                    <span class="pull-right label ">4</span>
                    Socks
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">9</span>
                    Nike
                </a>
                <a href="" class="list-group-item">
                    <span class="pull-right label ">10</span>
                    Adidas
                </a>
            </div>
        </div>
        <div class="col-md-9"></div>
    </div>

@endsection
