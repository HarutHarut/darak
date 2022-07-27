<?php


namespace App\Luglocker\Updaters;


use App\Models\Booking;

trait BookingUpdater
{
    public function bookingUpdate(Booking $booking, array $data): Booking
    {
        if(isset($data['status'])){
            $booking->status = $data['status'];
        }

        return $booking->fresh();
    }
}
