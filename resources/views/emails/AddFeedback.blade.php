<html>
<head>
    <title>Email Template</title>
    <style>
        table tr:not(.sm-space) td {
            padding-bottom: 12px;
        }
        table tbody tr td {
            color: #555;
            vertical-align: top;
        }
        table thead tr td {
            font-size: 18px;
        }
        .cancel-booking-btn {
            display: block;
            width: fit-content;
            color: #fff !important;
            background-color: #ff5a4f;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            padding: 10px 20px;
            margin: 40px auto;
            border-radius: 5px;
        }
        .cancel-booking-btn:hover {
            background-color: #f2483c;
        }
        .red-text {
            color: #ff0000;
        }
        .blue-text {
            color: #0070c0;
        }
    </style>
</head>
<body>
<div style="max-width: 600px; width: 100%; padding: 30px;">
    <div style="display: flex; align-items: end;">
        <img src="{{ config('app.beck_url') . '/img/logo-square.jpg' }}" style="max-width: 85px; width: 100%; margin-right: 20px;">
        <p style="color: #0070c0; font-weight: 600; font-size: 50px; margin: 0px;">LugLockers<span style="color: #ff0000;">.com</span></p>
    </div>
    <h2 style="color: #000000; margin-bottom: 0px; margin-top: 30px; font-style: italic;">{{ __('general.emails.dear') . ' ' . $order->user->name }},</h2>
    <p>{{ __('general.emails.AddFeedback.youBookedAndStored', ['branchName' => $branch->name['en']]) }}.</p>
    <h4 style="color: #555; font-weight: 600; margin-bottom: 10px; font-size: 18px;">{{ __('general.emails.bookingNumber') }}--{{ $order->booking_number }}</h4>
    <table style="width: 100%;">
        <thead>
        <tr>
            <td class="blue-text">{{ __('general.emails.DropOffDate') }}:</td>
            <td class="blue-text">{{ $order->check_in }}</td>
        </tr>
        <tr>
            <td class="red-text">{{ __('general.emails.PickUpDate') }}:</td>
            <td class="red-text">{{ $order->check_out }}</td>
        </tr>
        </thead>
        <tbody>
            <tr class="sm-space">
                <td>{{ __('general.emails.NumberOfLuggage') }}.</td>
                <td>{{ $bookingCount }}</td>
            </tr>
            <tr class="sm-space">
                <td>{{ __('general.emails.sizes') }}</td>
                <td></td>
            </tr>

            @foreach($sizeArr as $item)
                <tr class="sm-space">
                    <td>{{ $item->locker->size->name['en'] }}</td>
                    <td>{{ $item->count }}</td>
                </tr>
            @endforeach

            <tr>
                <td>{{ __('general.emails.Price') }}</td>
                <td>{{ $order->price . ' ' . $order->currency }}</td>
            </tr>
            <tr>
                <td>{{ __('general.emails.storageName') }}.</td>
                <td>{{ $branch->name['en'] }}</td>
            </tr>
            <tr>
                <td>{{ __('general.emails.Address') }}.</td>
                <td>{{ $branch->address }}
{{--                    <a href="{{ 'https://www.google.ru/maps/@' . $branch->lat . ',' . $branch->lng . ',10z' }}" style="display: block; color: #40c4f4; text-decoration: none; margin-top: 4px;">{{ __('general.emails.googleMaps') }}</a>--}}
                </td>
            </tr>
            <tr>
                <td>{{ __('general.emails.email') }}.</td>
                <td>{{ $branch->email }}</td>
            </tr>
        </tbody>
    </table>

    <p>{{ __('general.emails.AddFeedback.timeAndLeaveFeedback') }}.</p>

    <a target="_blank" href="{{ config('app.url') . 'account/orders/' . $order->booking_number }}" class="cancel-booking-btn">
        {{ __('general.emails.AddFeedback.leaveFeedback') }}
    </a>
    <h2 style="color: #0070c0; margin-top: 80px; font-style: italic;">
        {{ __('general.emails.sincerely') }},<br>
        {{ __('general.emails.lugLockersTeam') }}
    </h2>
</div>
</body>
</html>
