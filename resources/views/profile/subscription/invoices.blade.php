@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2 class="m-b-0">Purchase History</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            @if(user()->stripe_id)
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach (user()->invoices() as $invoice)
                        <tr>
                            <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                            <td>{{ $invoice->total() }}</td>
                            <td class="pull-right">
                                <a class="btn btn-xs white" href="{{ route('profile.subscription.invoices.show', [$invoice->id]) }}">View</a>
                                <a class="btn btn-xs white" href="{{ route('profile.subscription.invoices.download', [$invoice->id]) }}">Download</a>
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