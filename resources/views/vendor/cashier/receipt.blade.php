<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Invoice</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .paid {
            -webkit-transform: rotate(-20deg);
            -moz-transform: rotate(-20deg);
            -o-transform: rotate(-20deg);
            -ms-transform: rotate(-20deg);
            transform: rotate(-20deg);
            position: relative;
            display: inline-block;
            color: #9446ed;
            border: 4px solid #9446ed;
            font-weight: bold;
            border-radius: 10px;
            padding: 10px;
            font-size: 20px;
            bottom: 10px;
            width: 48px;
            right: 0;
            margin-right: 10px;
        }
        .white {
            background-color: #fff;
        }
        body {
            background: #eceef1;
            background-image: none;
            font-size: 12px;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #444;
            text-rendering: optimizeLegibility;
        }
        address{
            margin-top:15px;
        }
        .container {
            width: 540px;
            margin: 30px auto 0;
            padding: 15px 60px;
            border-radius: 5px;
            position: relative;
        }
        h2 {
            background: url(/assets/images/logo/logo-white-md.png) center;
            width: 132px;
            height: 32px;
            display: block;
            text-align: center;
            margin: 30px auto;
            text-indent: -9999px;
        }
        .invoice-head td {
            padding: 0 8px;
        }
        .invoice-body{
            background-color:transparent;
        }
        .logo {
            padding-bottom: 10px;
        }
        .table th {
            vertical-align: bottom;
            font-weight: bold;
            padding: 8px 0 8px 0;
            line-height: 20px;
            text-align: left;
            color: #bbb;
        }
        .table td {
            padding: 8px 0 8px 0;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
            border-top: 1px solid #dddddd;
        }
        .well {
            margin-top: 15px;
        }
        .header,
        .footer {
            color: #888;
            margin: 30px auto;
            text-align: center;
        }
        .header {
            font-size: 14px;
        }
        .header a {
            color: #777;
            text-decoration: none;
        }
    </style>
</head>

<body>
<h2>Kabooodle</h2>
<div class="container white">
    <table style="margin-left: auto; margin-right: auto; width: 100%">
        <tr valign="top">
            <td style="font-size: 18px;">
                Kabooodle
            </td>
            <td style="color:#bbb; text-align: right; font-size: 18px;">Receipt</td>
        </tr>
        <tr><td style="padding-top:15px; padding-bottom: 15px;" colspan="2"></td></tr>
        <tr valign="top">
            <td><p>
                <strong>To:</strong> {{ $user->email ?: $user->name }}
                <br>
                <strong>Date:</strong> {{ $invoice->date()->toFormattedDateString() }}</p>
            </td>
            <td style="text-align: right;">
                <p>
                    <strong>Product:</strong> {{ $product }}<br>
                    <strong>Invoice Number:</strong> {{ $id or $invoice->id }}<br>
                </p>
            </td>
        </tr>
    </table>
    <hr>
    <h4 style="color: #9446ed;">Invoice Summary</h4>
    <table width="100%" class="table" border="0" >
        <tr>
            <th align="left" style="text-align: left;">Description</th>
            <th align="right">Date</th>
            <th align="right" style="text-align: right;">Amount</th>
        </tr>

        <!-- Existing Balance -->
        <tr>
            <td style="text-align: left;">Starting Balance</td>
            <td>&nbsp;</td>
            <td style="text-align: right;">{{ $invoice->startingBalance() }}</td>
        </tr>

        <!-- Display The Invoice Items -->
        @foreach ($invoice->invoiceItems() as $item)
            <tr>
                <td colspan="2" style="text-align: left;">{{ $item->description }}</td>
                <td style="text-align: right;">{{ $item->total() }}</td>
            </tr>
        @endforeach

    <!-- Display The Subscriptions -->
        @foreach ($invoice->subscriptions() as $subscription)
            <tr>
                <td style="text-align: left;">Subscription ({{ $subscription->quantity }})</td>
                <td>
                    {{ $subscription->startDateAsCarbon()->formatLocalized('%B %e, %Y') }} -
                    {{ $subscription->endDateAsCarbon()->formatLocalized('%B %e, %Y') }}
                </td>
                <td style="text-align: right;">{{ $subscription->total() }}</td>
            </tr>
        @endforeach


        @if ($invoice->hasDiscount())
            <tr>
                @if ($invoice->discountIsPercentage())
                    <td style="text-align: left;">{{ $invoice->coupon() }} ({{ $invoice->percentOff() }}% Off)</td>
                @else
                    <td style="text-align: left;">{{ $invoice->coupon() }} ({{ $invoice->amountOff() }} Off)</td>
                @endif
                <td>&nbsp;</td>
                <td style="text-align: right;">-{{ $invoice->discount() }}</td>
            </tr>
        @endif

    <!-- Display The Tax Amount -->
        @if ($invoice->tax_percent)
            <tr>
                <td style="text-align: left;">Tax ({{ $invoice->tax_percent }}%)</td>
                <td>&nbsp;</td>
                <td style="text-align: right;">{{ Laravel\Cashier\Cashier::formatAmount($invoice->tax) }}</td>
            </tr>
        @endif

    <!-- Display The Final Total -->
        <tr style="border-top:2px solid #000;">
            <td>&nbsp;</td>
            <td style="text-align: right;"><strong>Total</strong></td>
            <td style="text-align: right;"><strong>{{ $invoice->total() }}</strong></td>
        </tr>

        <!-- Display The Tax Amount -->
        @if ($invoice->closed || $invoice->ending_balance > 0)
            <tr>
                <td style="text-align: left;"></td>
                <td></td>
                <td style="text-align: right;"><div class="paid">PAID</div> Thank You.</td>
            </tr>
        @endif

    </table>
</div>
<div class="footer">
    Kabooodle, LLC
</div>
</body>
</html>
