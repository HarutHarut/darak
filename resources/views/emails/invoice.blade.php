<html>
<head>
    <title>{{ __('general.emails.invoice.title') }}</title>
    <style>
        table.order tr th {
            text-align: center;
            padding: 12px;
            background-color: #015cba;
            color: #fff;
            border: 1px solid #015cba;
        }
        table.order tr th:not(:last-child) {
            border-right: 1px solid #dee2e6;
        }
        table.order thead {
            border: 1px solid #dee2e6;
        }
        table.order tbody tr td {
            border: 1px solid #dee2e6;
            padding: 12px;
            color: #212529;
        }
        table.total tr td {
            padding: 6px 12px;
            font-size: 20px;
            text-align: right;
        }
        ul li {
            font-size: 18px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div style="padding: 30px 50px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
        <img src="img/logo.jpg" style="max-width: 300px; margin-bottom: 30px;">
        <div>
            <ul style="list-style-type: none; padding: 0px; float: right">
                <li>{{ config('app.admin.address') }}</li>
                <li>{{ config('app.admin.city') }}</li>
            </ul>
        </div>
    </div>
    <ul style="list-style-type: none; padding: 0px; margin-top: 30px; margin-bottom: 40px;">
        <li>{{ $business->user->name ?? ""}}</li>
        <li>{{ $business->address }}</li>
        <li>{{ $business->city->name ?? ""}}</li>
    </ul>
    <table style="width: 300px; margin-bottom: 10px;">
        <tr>
            <td>{{ __('general.emails.Number') }}:</td>
            <td style="text-align: right;">#N {{ $invoice_number }}</td>
        </tr>
        <tr>
            <td>{{ __('general.emails.Date') }}:</td>
            <td style="text-align: right;">{{ date('Y-M-d') }}</td>
        </tr>
        <tr>
            <td>{{ __('general.emails.DatePeriod') }}:</td>
            <td style="text-align: right;">{{ $first_day_last_mount . ' - ' . $last_day_last_mount }}</td>
        </tr>
    </table>
    <h1 style="margin-bottom: 15px; font-size: 50px; text-transform: uppercase; text-align: center;">{{ __('general.emails.invoice.title') }}</h1>
    <table class="order" style="width: 100%; margin-top: 50px; border-collapse:collapse;">
        <thead>
        <tr>
            <th>#</th>
            <th>{{ __('general.emails.Price') }}</th>
            <th>{{ __('general.emails.Commission') }}</th>
        </tr>
        </thead>
        <tbody style="text-align: center;">
            <tr>
                <td>{{ __('general.emails.invoice.onlineBooking') }}</td>
                <td>{{ $order->sum }} {{ $order->currency ?? 'EUR' }}</td>
                <td>{{ $order->sum * config('app.admin.commission') / 100}} {{ $order->currency ?? 'EUR' }}</td>
            </tr>
            <tr>
                <td>{{ __('general.emails.invoice.totalForPayment') }}</td>
                <td></td>
                <td>{{ $order->sum * config('app.admin.commission') / 100 }} {{ $order->currency ?? 'EUR' }}</td>
            </tr>
            <tr>
                <td>{{ __('general.emails.invoice.totalAmountToBePaid') }} {{ config('app.admin.currency') }}</td>
                <td></td>
                <td>{{ $amount * config('app.admin.commission') / 100 }} {{ config('app.admin.currency') }}</td>
            </tr>
        </tbody>
    </table>
    <div style="clear: both"></div>
    <p style="margin-top: 40px; line-height: 1.5;">
        {{ __('general.emails.invoice.description1') . '?' . __('general.emails.invoice.description2') . ' ' . $daysToAdd . ' ' . __('general.emails.invoice.description3') }}.
    </p>
</div>
</body>
</html>
