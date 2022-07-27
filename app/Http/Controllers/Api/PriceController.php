<?php

namespace App\Http\Controllers\Api;

use \Throwable;
use App\Models\Price;
use Mockery\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Price\Update;
use App\Http\Requests\Api\Price\Create;
use App\Http\Controllers\ApiController;
use App\Luglocker\Updaters\PriceUpdater;
use Illuminate\Database\Eloquent\Builder;


class PriceController extends ApiController
{
    use PriceUpdater;

    public function all(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->all();

        try {

            $prices = Price::query()
                ->whereHas('locker', function (Builder $query) use ($data) {
                    $query->where('branch_id', '=', $data['branch_id']);
                })
                ->paginate($request->get('perPage'));

            return $this->success(200, $prices);

        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $user->id, 'BranchController all action');
            return $this->error(400, "Branch all failed.");
        }
    }

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['price'] as $value) {

                Price::query()
                    ->create([
                        'locker_id' => $data['locker_id'],
                        'range_start' => $value['range_start'],
                        'range_end' => $value['range_end'],
                        'price' => $value['price'],
                    ]);
            }

            DB::commit();
            return $this->success(200, [], "Price created successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'PriceController create action');
            return $this->error(400, "Price create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['price'] as $value) {

                $price = Price::query()
                    ->where('locker_id', '=', $data['locker_id'])
                    ->find($value['id']);

                if ($price == null) {
                    throw new Exception('Price not found.', 404);
                }

                /**
                 * @var $price Price
                 */

                $this->priceUpdate($price, $value);
            }

            DB::commit();
            return $this->success(200, [], "Price updated successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'PriceController update action');
            return $this->error(400, "Price update failed.");
        }
    }
}
