<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'timeIsNotValid' => 'Time is not valid minimum time interval is 1 hour',

    'emails' => [
        'BranchBlocked' => [
            'subject' => 'The branch has been blocked',
            "blocked" => "branch has been blocked",
        ],
        'AddFeedback' => [
            'subjectLeave' => 'Mail Subject-Leave feedback',
            'youBookedAndStored' => 'You booked and stored your luggage in the Crazy Hostel storage',
            'leaveFeedback' => 'Leave feedback',
        ],
        'BusinessVerified' => [
            'subject' => 'The business has been verified',
            'businessVerify' => 'business successfully verified',
            'leaveFeedback' => 'Leave feedback',
            'timeAndLeaveFeedback' => 'Can you take a few seconds of your time and leave feedback? It will help us to improve the service and make it more comfortable for you',
        ],
        'invoice' => [
            'subject' => "Your Invoice for :date",
            'title' => 'Invoice',
            'onlineBooking' => 'Online booking services',
            'totalForPayment' => 'Total for payment',
            'totalAmountToBePaid' => 'The total amount to be paid',
            'PaymentMustBe' => 'Payment must be made before',
            "weWouldLike" => "We would like to inform you that a new invoice has been issued today",
            "pleaseSeeAttachedFile" => "Please see attached file",
            "description1" => 'Why do we use it',
            "description2" => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like)"
        ],
        'BookedBusinessOwner' => [
            'subject' => "BookedBusinessOwner",
        ],
        'BookedUser' => [
            'subject' => "The locker has been booked - BookedUser",
        ],
        'BusinessBlocked' => [
            'subject' => 'The business has been blocked',
            'businessBlocked' => 'business has been blocked',
        ],
        'BranchVerified' => [
            'subject' => 'The branch is verified',
            'storageSuccessfully' => 'branch successfully verified',
        ],
        'BookCanceledByBusinessOwner' => [
            'subject' => 'Book has been canceled by Business Owner',
        ],
        'BookCanceledByUser' => [
            'subject' => 'Book has been canceled by user',
        ],
        'BookCanceledByBranch' => [
            'subject' => 'Book has been canceled by branch',
        ],
        'ResetPasswordToUser' => [
            'subject' => "Your new password",
            'passwordHasBeenReset' => 'Your password has been reset to',
        ],
        'ResetPassword' => [
            'subject' => "Reset your password",
            'title' => 'Email template reset | password',
            'reset' => 'Reset Your Password',
            'hey' => 'Hey',
            'requestedAPasswordReset' => 'This email is to confirm that you requested a password reset',
            'clickTheLinkBelow' => 'To complete the password reset process, click the link below',
        ],
        'UserBlocked' => [
            'subject' => 'The user has been blocked',
            "accountBlocked" => "Your account has been blocked",
        ],
        'UserActivity' => [
            'subject' => 'The user has been activity',
            "accountBlocked" => "Your account has been activity",
        ],
        'UserRegistered' => [
            'subject' => 'User has been registered successfully',
            'welcomeTo' => 'Welcome To Luglockers',
            'description' => 'description'
        ],
        'VerifyAccount' => [
            'subject' => "verify your account",
            'accountCreated' => 'Your account successfully created',
            "bookAndStore" => "Now you can book and store your luggage in the city of your choice"
        ],
        'contactForm' => [
            'subject' => 'Contact Form submit for :date',
            'name' => 'Name',
            'lastName' => 'Last Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'email' => 'Email',
            'message' => 'Message',
        ],



        'login' => 'Login',
        'sizes' => 'Sizes',
        'cancelBooking' => 'Cancel booking',
        'email' => 'Email',
        'workingHours' => 'Working hours',
        'checkIn' => 'Check in',
        'checkOut' => 'Check out',
        'bookingCount' => 'Booking count',
        'order' => 'order',
        'Locker' => 'Locker',
        'Address' => 'Address',
        'Quantity' => 'Quantity',
        'Price' => 'Price',
        'phone' => 'Phone',
        'branchName' => 'Branch name',
        'Date' => 'Date',
        "stayInTouch" => "stay in touch",
        "Commission" => "Commission",
        "Number" => "Number",
        "DatePeriod" => "Date period",
        "resetPassword" => "Reset password",
        "Registration" => "Registration",
        "bookingNumber" => "Booking number",
        "bookingSuccessfullyCanceled" => "You booking successfully canceled",
        "bookingCanceledFailed" => "Booking start time out",
        "googleMaps" => "View in Google Maps",
        "dear" => "Dear",
        "bookingIsConfirmed" => "Your booking is confirmed",
        "DropOffDate" => "Drop-off date and time",
        "PickUpDate" => "Pick-up date and time",
        "NumberOfLuggage" => "Number of luggage",
        "storageName" => "Storage Name",
        "businessName" => "Business Name",
        "sincerely" => "Sincerely",
        "lugLockersTeam" => "LugLockers Team",
        "niceTrip" => "Have a nice trip",
        "cancelBookingAfterOne" => "You can cancel booking after one hour drop-off time",
        "goodLuck" => "Good Luck",
        "clientEmail" => "Client email",
        "clientName" => "Client name",
        "clientTel" => "Client tel",
        "haveANewBooking" => "You have a new booking",
        "bookingHasBeenCancelled" => "Your booking has been cancelled",
        "bookingFrom" => "Booking from ",
        "hasBeenCancelled" => " has been cancelled",
        "yourBookingHasBeenCancelledBy" => "Your booking has been cancelled by ",
        "becauseYouWereLate" => ", because you were late",
        "forQuestions" => "For questions, please contact us",
        "your" => "Your",
        "tel" => "Tel",
        'Jan' => 'Jan',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Apr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Aug',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dec',

    ],
    "endAfterStart" => "The end time must be a date after start time",
    "adminCreateUser" => "User account has been created",

    "locker" => [
        "add" => [
            "repeatSize" => 'repeat size, please change size',
        ]
    ],
    "bookingCancelFailedUser" => "booking Cancel Failed User",
    "bookingCancelFailedBusiness" => "booking Cancel Failed Business",
    "updateBlog" => "Blog has been updated successfully",
    "lockerQuantityDoesNoteMatch" => "Quantity does note match",
    "openingTimeErrorMessage" => "Opening time error",
    "verifyYourEmail" => "Verify your email",
    "adminMastBeVerified" => "Branch created successfully. Admin must be verified your account",
    "businessUpdatedSuccessfully" => "Business updated successfully",

];
