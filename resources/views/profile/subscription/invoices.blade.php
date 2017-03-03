@extends('profile.settingstemplate')


@section('settings-content')

    @if(webUser()->stripe_id && $nextInvoice = webUser()->upcomingInvoice())
        <div class="box">
            <div class="box-header">
                <h2 class="m-b-0">Upcoming Invoice</h2>
            </div>
            <div class="box-divider"></div>
            <div class="box-body">
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $nextInvoice->date()->toFormattedDateString() }}</td>
                        <td>{{ $nextInvoice->total() }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="box">
        <div class="box-header">
            <h2 class="m-b-0">Payment History</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            @if(webUser()->stripe_id)
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach (webUser()->invoicesIncludingPending() as $invoice)
                        <tr>
                            <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                            <td>{{ $invoice->total() }}</td>
                            <td>@if($invoice->closed == true || $invoice->ending_balance == 9) <span
                                        class="">Paid</span> @endif</td>
                            <td>
                                <div class="pull-right">
                                    <a class="btn btn-xs white"
                                       href="{{ route('profile.subscription.invoices.show', [$invoice->id]) }}">View</a>
                                    <a class="btn btn-xs white"
                                       href="{{ route('profile.subscription.invoices.download', [$invoice->id]) }}">Download</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="">You currently aren't subscribed to a billable account type.</p>
            @endif
        </div>
    </div>


@endsection