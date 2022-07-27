<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Repositories\FeedbackRepository;
use App\Repositories\LockerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SalesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected object $orderRepository;
    protected object $salesRepository;
    protected object $lockerRepository;
    protected object $feedbackRepository;

    public function __construct(
        OrderRepository $orderRepository,
        SalesRepository $salesRepository,
        LockerRepository $lockerRepository,
        FeedbackRepository $feedbackRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->salesRepository = $salesRepository;
        $this->lockerRepository = $lockerRepository;
        $this->feedbackRepository = $feedbackRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
//        return response()->json($data);
        $user = $request->user();
        $response = null;
        if ($user->isBusiness()) {
            $response['orders'] = $this->orderRepository->businessOrder($user->id, $data);
            $response['sales'] = $this->salesRepository->businessSalesSum($user->id, $data);
            $response['locker'] = $this->lockerRepository->businessLockerCount($user->id);
            $response['feedback'] = $this->feedbackRepository->businessFeedback($user->id, $data);
        } else {
            $response['orders'] = $this->orderRepository->adminOrders($data);
            $response['sales'] = $this->salesRepository->adminSalesSum($data);
            $response['locker'] = $this->lockerRepository->adminLockerCount();
            $response['feedback'] = $this->feedbackRepository->adminFeedback($data);
//            return response()->json($response['feedback']);
        }

        return response()->json($response);
    }

    public function orders(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = $request->user();
        if ($user->isBusiness()) {
            $data = $this->orderRepository->lastOrdersByBusiness($data['count'], $user->id);
        } else {
            $data = $this->orderRepository->lastOrdersByAdmin($data['count'], $user->id);
        }
        return response()->json($data);
    }

    public function invoices(Request $request)
    {
        $data = $request->all();
//        return response()->json($data);
        $invoices = Invoices::with('business.city');

        if (isset($data['search']) && $data['search'] !== null) {
            $invoices = $invoices->where(function($q) use($data) {
                $q->where('invoice_number', $data['search'])
                ->orWhereHas('business', function ($query) use($data) {
                    $query->where('name', 'like', '%' . $data['search'] . '%');
//                    ->orWhere();
                });
            });
        }

        if (isset($data['business_id']) && $data['business_id'] !== null) {
            $invoices = $invoices->where('business_id', $data['business_id']);
        }

        if (isset($data['check_in']) && $data['check_in'] !== null && isset($data['check_out']) && $data['check_out'] !== null) {
            $invoices = $invoices
                ->where('due_by', '>=', $data['check_in'])
                ->where('due_by', '<=', $data['check_out']);
        }
        $filterYear = [];
        if (isset($data['year']) && $data['year'] !== null) {
            $invoicesGet = $invoices->get();
            foreach ($invoicesGet as $item) {
                if ($item->created_at->format('Y') == $data['year']) {
                    $filterYear[] = $item->id;
                }
            }
            $invoices = $invoices->whereIn('id', $filterYear);
        }
        if (isset($data['month']) && $data['month'] !== null) {
            $invoices = $invoices->where('month', $data['month']);
        }



        $invoices = $invoices->get();

        return response()->json($invoices);
    }
}
