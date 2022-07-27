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
    <h2 style="color: #000000; margin-bottom: 0px; margin-top: 30px; font-style: italic;">{{ __('general.emails.dear') . ' ' . $user->name }},</h2>
    <h2 style="color: #ff0000; margin-top: 15px;">{{ __('general.emails.your') }} {{ $business->name['en'] }} {{ __('general.emails.BusinessBlocked.businessBlocked') }}.</h2>
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td>{{ __('general.emails.businessName') }}:</td>
            <td>{{ $business->name['en'] }}</td>
        </tr>
        <tr>
            <td>{{ __('general.emails.Address') }}.</td>
            <td>{{ $business->address }} <a href="{{ 'https://www.google.ru/maps/@' . $business->lat . ',' . $business->lng . ',10z' }}" style="display: block; color: #40c4f4; text-decoration: none; margin-top: 4px;">{{ __('general.emails.googleMaps') }}</a></td>
        </tr>
        <tr>
            <td>{{ __('general.emails.email') }}.</td>
            <td>{{ $user->email }}</td>
{{--            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>--}}
        </tr>

        <tr>
            <td>
                <p>
                    {{ __('general.emails.forQuestions') }}.
                </p>
                {{ __('general.emails.email') }} - <a href="mailto:partners@luglockers.com">partners@luglockers.com</a></td>
        </tr>

        </tbody>
    </table>
    <h2 style="color: #0070c0; margin-top: 80px; font-style: italic;">
        {{ __('general.emails.sincerely') }},<br>
        {{ __('general.emails.lugLockersTeam') }}
    </h2>
</div>
</body>
</html>
