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
            'subject' => 'Branch blocked',
            "blocked" => "branch has been blocked",
        ],
        'AddFeedback' => [
            'subject' => 'Leave feedback',
            'subjectLeave' => 'Mail Subject-Leave feedback',
            'youBookedAndStored' => 'You booked and stored your luggage in the :branchName storage',
            'leaveFeedback' => 'Leave feedback',
            'timeAndLeaveFeedback' => 'Can you take a few seconds of your time and leave feedback? It will help us to improve the service and make it more comfortable for you',
        ],
        'BusinessVerified' => [
            'subject' => 'Business registration',
            'businessVerify' => 'has been registered successfully',
        ],
        'BusinessUnBlocked' => [
            'subject' => 'Business Unblocked',
            'businessUnblocked' => 'has been unblocked',
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
            "description2" => "Please note that Service Fee the Partner must pay to LugLockers within",
            "description3" => " days of submitting the invoice. Payment for the service is made by transferring money to LugLockers bank account through from your business page in the Invoices field."
        ],
        'BookedBusinessOwner' => [
            'subject' => "New booking",
        ],
        'BookedUser' => [
            'subject' => "Booking confirmation",
        ],
        'BusinessBlocked' => [
            'subject' => 'Business blocked',
            'businessBlocked' => 'business has been blocked',
        ],
        'BranchVerified' => [
            'subject' => 'Branch verification',
            'storageSuccessfully' => 'branch has been verified successfully',
        ],
        'BookCanceledByBusinessOwner' => [
            'subject' => 'Book has been canceled by Business Owner',
        ],
        'BookCanceledByUser' => [
            'subject' => 'Booking cancelation',
        ],
        'BookCanceledByBranch' => [
            'subject' => 'Book has been canceled by branch',
        ],
        'BookCanceledByAdminNotificationToUser' => [
            'subject' => 'Booking cancelation',
            'subject-booking' => 'Subject-Booking cancelation',
            'YourBookingHasBeenCanceled' => 'Your booking has been canceled by LugLockers',
        ],
        'BookCanceledByAdminNotificationToBranch' => [
            'subject' => 'Booking cancelation',
            'subject-booking' => 'Subject-Booking cancelation',
            'YourBookingHasBeenCanceled' => 'has been canceled by LugLockers',
        ],
        'ResetPasswordToUser' => [
            'subject' => "New password",
            'passwordHasBeenReset' => 'Your password has been reset to',
        ],
        'ResetPassword' => [
            'subject' => "Reset your password",
            'title' => 'Email template reset | password',
            'reset' => 'Reset Your Password',
            'hey' => 'Hey',
            'requestedAPasswordReset' => 'This email is to confirm that you requested a password reset',
            'clickTheLinkBelow' => 'To complete the password reset process, click the link below.',
        ],
        'UserBlocked' => [
            'subject' => 'Account blocked',
            "accountBlocked" => "Your account has been blocked.",
        ],
        'UserActivity' => [
            'subject' => 'Account activation',
            "accountBlocked" => "Your account has been activated.",
        ],
        'UserRegistered' => [
            'subject' => 'User registration',
            'welcomeTo' => 'Welcome To Luglockers.',
            'description' => 'Please click the registration button in order to finish registration.'
        ],
        'VerifyAccount' => [
            'subject' => "Create account",
            'accountCreated' => 'Your account has been successfully created.',
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
        "bookingSuccessfullyCanceled" => "Your booking canceled.",
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
        "forQuestions" => "For questions, please contact the LugLockers Team",
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
    "bookingCancelFailedAdmin" => "booking Cancel Failed Admin",
    "updateBlog" => "Blog has been updated successfully",
    "lockerQuantityDoesNoteMatch" => "Quantity does note match",
    "openingTimeErrorMessage" => "Luggage storage Closed",
    "verifyYourEmail" => "Verify your email",
    "adminMastBeVerified" => "Branch created successfully. Admin must be verified your account",
    "businessUpdatedSuccessfully" => "Business updated successfully",
];
