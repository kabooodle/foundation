@extends('layouts.full')



@section('body-menu')
    <div class="pull-left">
        <a class="btn btn-sm white" href="{{ route('listings.index') }}">
            Filter Listings
        </a>
    </div>

@endsection


@section('body-content')
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h4>Multiconsultant sale 2 - 11/01/2016 10:00 </h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col"><input type="checkbox"></th>
                    <th scope="col">Type</th>
                    <th scope="col">Group</th>
                    <th scope="col">Album</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Views</th>
                    <th scope="col">Gross</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>Facebook Group</td>
                    <td>Pipers Group</td>
                    <td>Joey (SM)</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>$0.00</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">View</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>Facebook Group</td>
                    <td>Pipers Group</td>
                    <td>Irma (XL)</td>
                    <td>9</td>
                    <td>13</td>
                    <td>126</td>
                    <td>$720.10</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">View</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>Facebook Group</td>
                    <td>Pipers Group</td>
                    <td>Joy (XXL)</td>
                    <td>9</td>
                    <td>76</td>
                    <td>32</td>
                    <td>$108.10</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">View</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>Flashsale</td>
                    <td>Jakes private flashsale</td>
                    <td>N/A</td>
                    <td>9</td>
                    <td>76</td>
                    <td>32</td>
                    <td>$108.10</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">View</a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


@endsection