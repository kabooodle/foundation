@extends('layouts.full')



@section('body-menu')

@endsection


@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Listings</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox"></th>
                        <th scope="col">Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Listing Date</th>
                        <th scope="col">Albums</th>
                        <th scope="col">Items</th>
                        <th scope="col">Sales</th>
                        <th scope="col">Views</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>Weekend Sale Trial</td>
                        <td>Pipers Group</td>
                        <td>11/01/2016 10:00</td>
                        <td>9</td>
                        <td>4</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td><span class="w-8 rounded deep-purple-500" style="margin-right: 2px"></span> <span class="text-deep-purple-500">Scheduled</span></td>
                        <td>
                            <div class="pull-md-right">
                                <a href="/listings/1" class="btn btn-xs white">View</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>Multiconsultant Sale</td>
                        <td>Pipers Group</td>
                        <td>11/22/2016 10:00</td>
                        <td>2</td>
                        <td>9</td>
                        <td>13</td>
                        <td>126</td>
                        <td><span class="w-8 rounded success" style="margin-right: 2px"></span> <span class="text-success">Listed</span></td>
                        <td>
                            <div class="pull-md-right">
                                <a href="/listings/1" class="btn btn-xs white">View</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>Multiconsultant Sale 2</td>
                        <td>Pipers Group</td>
                        <td>11/09/2016 10:00</td>
                        <td>14</td>
                        <td>9</td>
                        <td>76</td>
                        <td>32</td>
                        <td><span class="w-8 rounded warning" style="margin-right: 2px"></span> <span class="text-warning">Partial</span></td>
                        <td>
                            <div class="pull-md-right">
                                <a href="/listings/1" class="btn btn-xs white">View</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


@endsection