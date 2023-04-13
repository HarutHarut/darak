<?php

return [
    "booking_status" => [
        'active' => 1,
        'canceled_by_booker' => 2,
        'canceled_by_business' => 3,
        'canceled_by_admin' => 4,
        'completed' => 5,
    ],
    "branch_status" => [
        "not_verified" => 0,
        "verified" => 1,
        'blocked' => 2
    ],
    "locker_size" => [
        "not_verified" => 0,
        "verified" => 1
    ],
    "branch_working_status" => [
        "out_of_service" => 0,
        "serviced" => 1,
    ],
    "locker_working_status" => [
        "out_of_service" => 0,
        "serviced" => 1,
    ],
    "user_status" => [
        "blocked" => 0,
        "not_verified" => 1,
        "verified" => 2,
    ],
    "business_status" => [
        "not_verified" => 0,
        "verified" => 1,
        'blocked' => 2
    ],
    "business_publish" => [
        "published" => 0,
        "unpublished" => 1,
    ],
    "days_of_week" => [
        "monday" => 1,
        "tuesday" => 2,
        "wednesday" => 3,
        "thursday" => 4,
        "friday" => 5,
        "saturday" => 6,
        "sunday" => 7,
    ],
    "week_of_days" => [
        1 => "Mon",
        2 => "Tue",
        3 => "Wed",
        4 => "Thu",
        5 => "Fri",
        6 => "Sat",
        7 => "Sun",
    ],
    "branch_open_status" => [
        "open" => 1,
        "close" => 0,
    ],

    "email_status" => [
        "is_not_sent" => 0,
        "sent" => 1,
    ],

    "email_type" => [
        "account_verify" => 0,
        "user_block" => 1,
        "reset_password" => 2,
        "business_block" => 3,
        "business_verify" => 4,
        "branch_block" => 5,
        "book_user" => 6,
        "book_cancel_by_user" => 7,
        "book_cancel_by_business_owner" => 8,
        "book_business_owner" => 9,
        "contact_form" => 10,
    ],
    "book_cancel_time" => [
        "user" => 30,
        "business" => 30
    ],
    'media_types' => [
        'image' => 1,
    ],
    'user_languages' => ['en', 'ru', 'am', 'fr', 'zh'],
    'city_status' => [
        'active' => 1,
        'inactive' => 0
    ],
    'city_top_status' => [
        "top" => 1,
        "not_top" => 0
    ],
    'feedback_status' => [
        'published' => 0,
        'unpublished' => 1
    ],
    'months' => [
        '1' => 'Jan',
        '2' => 'Feb',
        '3' => 'Mar',
        '4' => 'Apr',
        '5' => 'May',
        '6' => 'Jun',
        '7' => 'Jul',
        '8' => 'Aug',
        '9' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
    ],

    'pagination' => [
        'perPage' => 15
    ],

    'compressImage' => 90

];
