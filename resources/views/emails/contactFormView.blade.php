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
    <div>
        <p style="margin-bottom: 10px;">
            <span>
                <strong>{{ __('general.emails.contactForm.name') }}: </strong>
            </span>
            <span>{{ $name }}</span>
        </p>
        <p style="margin-bottom: 10px;">
            <span>
                <strong>{{ __('general.emails.contactForm.lastName') }}: </strong>
            </span>
            <span>{{ $lastName }}</span>
        </p>
        @if(isset($phone))
            <p style="margin-bottom: 10px;">
                <span>
                    <strong>{{ __('general.emails.contactForm.phone') }}: </strong>
                </span>
                <span><a href="tel:{{ $phone }}">{{ $phone }}</a></span>
            </p>
        @endif
        <p style="margin-bottom: 10px;">
            <span>
                <strong>{{ __('general.emails.contactForm.address') }}: </strong>
            </span>
            <span>{{ $address }}</span>
        </p>
        <p style="margin-bottom: 10px;">
            <span>
                <strong>{{ __('general.emails.contactForm.email') }}: </strong>
            </span>
            <span><a href="mailto:{{ $email }}">{{ $email }}</a></span>
        </p>
        <p style="margin-bottom: 10px;">
            <span>
                <strong>{{ __('general.emails.contactForm.message') }}: </strong>
            </span>
            <span>{{ $message }}</span>
        </p>
    </div>
    <h2 style="color: #0070c0; margin-top: 80px; font-style: italic;">
        {{ __('general.emails.sincerely') }},<br>
        {{ __('general.emails.lugLockersTeam') }}
    </h2>
</div>
</body>
</html>
