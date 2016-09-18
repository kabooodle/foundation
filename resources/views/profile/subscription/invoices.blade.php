@extends('profile.settingstemplate')


@section('settings-content')

    @if(user()->stripe_id)
    <table class="table table-condensed">
        <tbody>
        @foreach (user()->invoices() as $invoice)
            <tr>
                <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                <td>{{ $invoice->total() }}</td>
                <td><a href="/user/invoice/{{ $invoice->id }}">Download</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p class="">You currently aren't subscribed to a billable account type.</p>
    @endif

@endsection