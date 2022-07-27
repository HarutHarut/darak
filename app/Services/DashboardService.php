<?php

namespace App\Services;

class DashboardService
{
    public function getBranchesByDate($user, $date) {
        if($user->role_id === 3) {
            dd($date);
        }
    }
}
