<?php

namespace App\Repositories;

use App\Models\Locker;
use Illuminate\Database\Eloquent\Builder;
class LockerRepository {

    public function locker(): Builder
    {
        return Locker::query();
    }
    public function businessLockers($user_id): Builder
    {
        return $this->locker()
            ->where('businesses.user_id', $user_id)
            ->join('branches', 'branches.id', '=', 'lockers.branch_id')
            ->join('businesses', 'businesses.id', '=', 'branches.business_id');
    }
    public function businessLockerCount($business_id): int
    {
        return $this->businessLockers($business_id)->count();
    }
    public function adminLockerCount(): int
    {
        return $this->locker()->count();
    }
}
