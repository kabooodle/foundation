@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Shipping Label Credits</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-9">

                    <p>Buy label credits in bulk for larger discounts:</p>
                    <table class="table table-condensed table-sm">
                        <tr>
                            <td>100 credits</td>
                            <td>10$</td>
                            <td>10% off</td>
                        </tr>
                        <tr>
                            <td>a</td>
                            <td>a</td>
                            <td>a</td>
                        </tr>
                        <tr>
                            <td>a</td>
                            <td>a</td>
                            <td>a</td>
                        </tr>
                        <tr>
                            <td>a</td>
                            <td>a</td>
                            <td>a</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="text-center p-a r-t r-b success">
                        <h3 class="m-a-0 m-v">
                            <span class="text-1x">69</span>
                            <span class="text-xs block _500">Credits remaining</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection