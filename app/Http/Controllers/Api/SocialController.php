<?php

namespace App\Http\Controllers\Api;

use \Throwable;
use Mockery\Exception;
use App\Models\SocialNetworkUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Social\Create;
use App\Http\Requests\Api\Social\Update;
use App\Luglocker\Updaters\SocialUrlsUpdater;
use App\Http\Controllers\ApiController;


class SocialController extends ApiController
{
    use SocialUrlsUpdater;

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            DB::beginTransaction();

            $urls = [];
            foreach ($data['urls'] as $value){
                array_push($urls,[
                    'branch_id' => $value['branch_id'] ?? null,
                    'business_id' => $value['business_id'] ?? null,
                    'type' => $value['type'],
                    'url' => $value['url']
                ]);
            }

            SocialNetworkUrl::query()
                ->insert($urls);


            DB::commit();

            return $this->success(200, [], "Social network url created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'SocialController create action');
            return $this->error(400, "Social network url create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            DB::beginTransaction();

            foreach ($data['urls'] as $value){

                $socialNetworkUrl = SocialNetworkUrl::query()
                    ->find($value['id']);

                if ($socialNetworkUrl == null){
                    throw new Exception('Social network url not found.',404);
                }

                /**
                 * @var $socialNetworkUrl SocialNetworkUrl
                 */

                $this->socialUrlsUpdate($socialNetworkUrl, $value);
            }

            DB::commit();

            return $this->success(200, [], "Social network url updated successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $user->id, 'SocialController update action');
            return $this->error(400, "Social network url update failed.");
        }
    }
}
