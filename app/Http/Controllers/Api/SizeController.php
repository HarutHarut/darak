<?php

namespace App\Http\Controllers\Api;

use App\Models\Locker;
use \Throwable;
use App\Models\Size;
use Mockery\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Size\Update;
use App\Luglocker\Updaters\SizeUpdater;
use App\Http\Requests\Api\Size\Create;
use App\Http\Controllers\ApiController;

class SizeController extends ApiController
{
    use SizeUpdater;

    public function all(Request $request): JsonResponse
    {
        $user = $request->user();
        $business = $user->business;
        try {

            $sizes = Size::query()
                ->where('verified', 1)
                ->orWhere('business_id', $business->id)
                ->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['sizes' => $sizes]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController all action',$user->id);
            return $this->error(400, "Branch all failed.");
        }
    }

    public function list(Request $request)
    {
        $data = $request->input("business_id");
        $branch_id = $request->input("branch_id");
        $user = $request->user();
        $business = $user->business;
        if (isset($business["id"])) {
            $businessId = $business->id;
        }else {
            $businessId = $data;
        }
        try {
            $sizes = Size::query()
                ->select('id','name')
                ->where('verified', '=', true);
                if($request->get('allSizes') == 'false'){
                    $locker_ids = Locker::where('branch_id', $branch_id)->pluck('size_id');
                    $sizes = $sizes->whereNotIn('id' , $locker_ids);
                }
                $sizes = $sizes->orWhere('business_id', '=', $businessId)
                ->get();

            return $this->success(200, ['sizes' => $sizes]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BranchController list action', $user->id);
            return $this->error(400, "Branch list failed.");
        }
    }

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $business = $user->business;
        $data['width'] = str_replace('+', '', $data['width']);
        $data['height'] = str_replace('+', '', $data['height']);
        $data['length'] = str_replace('+', '', $data['length']);

        try {
            DB::beginTransaction();

            $size = Size::query()->create([
                'business_id' => $business->id,
                'name' => $data['name'],
                'width' => $data['width'],
                'height' => $data['height'],
                'length' => $data['length'],
                'desc' => $data['desc'],
            ]);

            DB::commit();

            return $this->success(200, ['size' => $size], "Size created successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'SizeController create action',$user->id);
            return $this->error(400, "Size create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['size'] as $value) {

                /**
                 * @var $size Size
                 */

                $size = Size::query()
                    ->find($value['id']);

                $this->sizeUpdate($size, $value);
            }

            DB::commit();
            return $this->success(200, [], "Size updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'SizeController update action',$user->id);
            return $this->error(400, "Size update failed.");
        }
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $business = $user->business;

        try {
            DB::beginTransaction();

            $size = Size::query()
                ->withCount('lockers')
                ->where('business_id','=',$business->id)
                ->where('id','=',$id)
                ->first();

            if ($size == null) {
                throw new Exception('Size not found.', 404);
            }

            if ($size->lockers_count != 0) {
                throw new Exception("You can't delete this size because this is connected to lockers.",400);
            }

            $size->delete();

            DB::commit();
            return $this->success(200, [], "Size deleted successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'SizeController delete action',$user->id);
            return $this->error(400, "Size delete failed.");
        }
    }
}
