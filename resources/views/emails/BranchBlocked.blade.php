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
    <h2 style="color: #000000; margin-bottom: 0px; margin-top: 30px; font-style: italic;">{{ __('general.emails.dear') . ' ' . $branch->name['en'] }},</h2>
    <h2 style="color: #ff0000; margin-top: 15px;">{{ __('general.emails.your') }} {{ $branch->name['en'] }} {{ __('general.emails.BranchBlocked.blocked') }}.</h2>
    <p>{{ __('general.emails.forQuestions') }}.</p>
    <p>{{ __('general.emails.email') }} -
        <a href="{{ env('MAIL_USERNAME') }}">{{ env('MAIL_USERNAME') }}</a>
    </p>
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td>{{ __('general.emails.storageName') }}:</td>
            <td>{{ $branch->name['en'] }}</td>
        </tr>
        <tr>
            <td>{{ __('general.emails.Address') }}.</td>
            <td>{{ $branch->address }} <a href="{{ 'https://www.google.ru/maps/@' . $branch->lat . ',' . $branch->lng . ',10z' }}" style="display: block; color: #40c4f4; text-decoration: none; margin-top: 4px;">{{ __('general.emails.googleMaps') }}</a></td>
        </tr>
        <tr>
            <td>{{ __('general.emails.email') }}.</td>
            <td>{{ $branch->email }}</td>
        </tr>
        <tr>
            <td>{{ __('general.emails.workingHours') }}.</td>

            <td>
                @foreach($branch->openingTimes as $item)
                    {{ config("constants.week_of_days.$item->weekday") . ': ' . substr($item->start,0,-3) . ' - ' .  substr($item->end,0,-3) }}<br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>{{ __('general.emails.tel') }}.</td>
            <td>{{ $branch->phone }}</td>
        </tr>

        @if(isset($branch->socialNetworkUrls))
            @foreach($branch->socialNetworkUrls as $item)
                <tr>
                    <td>{{ $item->type }}.</td>
                    <td>{{ $item->url }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <h2 style="color: #0070c0; margin-top: 80px; font-style: italic;">
        {{ __('general.emails.sincerely') }},<br>
        {{ __('general.emails.lugLockersTeam') }}
    </h2>
</div>
</body>
</html>
