<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\Business\BusinessUpdate;
use App\Models\Booking;
use Illuminate\Http\Request;
use stdClass;
use \Throwable;
use App\Models\Branch;
use Mockery\Exception;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Storage};
use App\Http\Requests\Api\Business\Create;
use App\Http\Requests\Api\Business\Update;
use App\Luglocker\Updaters\BusinessUpdater;
use App\Http\Controllers\ApiController;


class BusinessController extends ApiController
{

    use BusinessUpdater;

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $name = new stdClass();
        $name->ru = null;
        $name->sp = null;
        $name->ch = null;
        $name->am = null;
        $name->de = null;
        $name->fr = null;
        $name->en = $data['name'];
        $data['name'] = $name;
        try {

            DB::beginTransaction();

            $business = new Business([
                'user_id' => $user->id,
                'name' => $data['name'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            if (isset($data['logo'])){
                $business->logo = Storage::url(Storage::putFile('business/logo', $data['logo']));
            }

            $business->save();

            Branch::query()
                ->create([
                    'business_id' => $business->id,
                    'name' => $data['name'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                    'phone' => $data['phone'],
                    'city_id' => $data['city_id'],
                    'address' => $data['address'],
                    'currency_id' => $data['currency_id'],
                ]);

            DB::commit();

            return $this->success(200, [], "Business created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'BusinessController create action');
            return $this->error(400, "Business create failed.");
        }
    }

    public function update(BusinessUpdate $request): JsonResponse
    {
        $data = $request->validated();
//        return response()->json($request->file('logo'));
        $user = $request->user();
        $business = $user->business;
        if(isset($data['id'])){
            $business = Business::find($data['id']);
        }
        if(gettype($data['name']) == 'string') {
            $name = new stdClass();
            $name->ru = null;
            $name->sp = null;
            $name->ch = null;
            $name->am = null;
            $name->de = null;
            $name->fr = null;
            $name->en = $data['name'];
            $data['name'] = $name;
        }
        try {
            if ($business == null) {
                throw new Exception('Business not found.', 404);
            }
            $this->businessUpdate($business, $data, $request->file('logo'));

            return $this->success(200, [], __('businessUpdatedSuccessfully'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'BusinessController update action');
            return $this->error(400, "Business create failed.");
        }
    }

    public function get() {
        $user = Auth::user();
        $business = Business::with('city')->where('user_id', '=', $user['id'])->first();
        return $business;
    }

    public function list() {
        $business = Business::query()->select('id', 'name')->get();
        return $business;
    }

    public function getBusiness(Request $request, $id){
        $business = Business::find($id);
        return response()->json($business);
    }
}
