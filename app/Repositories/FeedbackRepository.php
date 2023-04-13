<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Builder;
use phpDocumentor\Reflection\Types\Mixed_;
use PhpParser\Node\Expr\Cast\Int_;

class FeedbackRepository {

    /**
     * @return Builder
     */
    public function feedback(): Builder
    {
        return Feedback::query();
    }

    /**
     * @param $user_id
     * @param $data
     * @return Int_
     */
    public function businessFeedback($user_id, $data): int
    {
        return $this->feedback()
            ->with('branch.business.user')
            ->whereHas('branch.business.user', function ($q) use($user_id) {
                $q->where('user_id', $user_id);
            })->count();
//            ->where('businesses.user_id', $user_id)
//            ->join('orders', 'orders.id', '=', 'feedbacks.order_id')
//            ->join('businesses', 'businesses.id', '=', 'orders.business_id')->count();
    }

    /**
     * @param $data
     * @return int
     */
    public function adminFeedback($data): int
    {
        return $this->feedback()->count();
    }

    /**
     * @param $data
     * @return Builder
     */
    public function byDate( $data): Builder
    {
        return $this->feedback()->where(function ($query) use ($data) {
            $query
                ->where(function ($q) use ($data) {
                    $q->where('check_in', '<=', $data['startDate'])
                        ->where('check_out', '>=', $data['startDate']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('check_in', '<=', $data['endDate'])
                        ->where('check_out', '>=', $data['endDate']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('check_in', '>=', $data['startDate'])
                        ->where('check_out', '<=', $data['endDate']);
                });
        })->join('orders', 'orders.id', '=', 'feedbacks.order_id');

    }

}
