<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * @return Builder
     */
    public function user(): Builder
    {
        return User::query()->with(['role'])
            ->withCount('bookings');
    }

    /**
     * @param $data
     * @return LengthAwarePaginator
     */
    public function searchUser($data): LengthAwarePaginator
    {
        return $this->user()
            ->where('name', 'like', "%" . $data['search'] . "%")
            ->orWhere('email', 'like', "%" . $data['search'] . "%")
            ->paginate(config('constants.pagination.perPage'));
    }
}
