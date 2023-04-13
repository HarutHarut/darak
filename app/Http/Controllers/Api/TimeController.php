<?php

namespace App\Http\Controllers\Api;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;
use \Throwable;
use App\Models\ClosingTime;
use App\Models\OpeningTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\OpenTime\Create;
use App\Http\Requests\Api\OpenTime\Update;
use App\Http\Requests\Api\CloseTime\CreateTime;
use App\Http\Requests\Api\CloseTime\UpdateTime;
use App\Luglocker\Updaters\ClosingTimeUpdater;
use App\Luglocker\Updaters\OpeningTimeUpdater;
use App\Http\Controllers\ApiController;



class TimeController extends ApiController
{
    use  ClosingTimeUpdater;
    public function createOpenTime(Create $request) :JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $business = $user->business;

        try {
            DB::beginTransaction();

            $times = [];

            foreach ($data['time'] as $value){

                $branch = Branch::query()
                    ->where('id','=',$data['branch_id'])
                    ->where('business_id','=',$business->id)
                    ->first();

                if ($branch == null){
                    throw new Exception('Branch not found.',404);
                }

                OpeningTime::query()
                    ->create([
                        'branch_id' => $data['branch_id'],
                        'weekday' => $value['weekday'],
                        'start' => $value['start'],
                        'end' => $value['end'] == '24:00:00' ? '23:59:00' : $value['end'],
                        'status' => $value['status'],
                    ]);

            }
            $openingTimes = OpeningTime::query()
                ->where('branch_id','=',$data['branch_id'])
                ->get();

            DB::commit();

            return $this->success(200, ['openingTimes' => $openingTimes], "Open time created successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'TimeController createOpenTime action', $user->id);
            return $this->error(400, "Open time create failed.");
        }
    }

    public function updateOpenTime(Update $request) :JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
//        return response()->json($data);

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value){
//                return response()->json(Carbon::parse($value['end']) , Carbon::parse($value['start']));

                if($value['status'] == true && Carbon::parse($value['end']) <= Carbon::parse($value['start'])){
                    return $this->error(422, __("general.endAfterStart"));
                }
//                return response()->json($value['end']);
                OpeningTime::query()
                    ->where('id','=',$value['id'])
                    ->update([
                        'status' => $value['status'],
                        'start' => $value['start'],
                        'end' => $value['end'] == '24:00:00' ? '23:59:00' : $value['end'],
                    ]);
            }
            $openingTimes = OpeningTime::query()
                ->where('branch_id','=',$data['branch_id'])
                ->get();
            DB::commit();

            return $this->success(200,['openingTimes' => $openingTimes], "Open time updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'TimeController updateOpenTime action',$user->id);
            return $this->error(400, "Open time update failed.");
        }
    }

    public function createCloseTime(CreateTime $request) :JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $business = $user->business;
        try {
            DB::beginTransaction();

            $times = [];

            foreach ($data['time'] as $value){

                $branch = Branch::query()
                    ->where('id','=',$value['branch_id'])
                    ->where('business_id','=',$business->id)
                    ->first();

                if ($branch == null){
                    throw new Exception('Branch not found.',404);
                }

                array_push($times, [
                    'branch_id' => $branch->id,
                    'start' => $value['start'],
                    'end' =>  $value['end']
                ]);
            }

            ClosingTime::query()->insert($times);

            DB::commit();

            return $this->success(200, [], "Close time created successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'TimeController createCloseTime action');
            return $this->error(400, "Close time create failed.");
        }
    }

    public function updateCloseTime(UpdateTime $request) :JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data['time'] as $value){

                $closingTime = ClosingTime::query()
                    ->find($value['id']);

                /**
                 * @var $closingTime ClosingTime
                 */
                $this->closingTimeUpdate($closingTime, $value);
            }

            DB::commit();

            return $this->success(200, [], "Close time updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'TimeController updateCloseTime action');
            return $this->error(400, "Close time update failed.");
        }
    }
}
