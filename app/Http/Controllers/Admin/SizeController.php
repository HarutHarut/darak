<?php

namespace App\Http\Controllers\Admin;

use \Throwable;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Size\Store;
use App\Luglocker\Updaters\SizeUpdater;
use App\Http\Requests\Admin\Size\Update;

class SizeController extends ApiController
{
    use SizeUpdater;

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        try {
            $sizes = Size::query()
                ->with(['business','lockers'])
                ->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['sizes' => $sizes]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'Admin/SizeController index action', $admin->id);
            return $this->error(400, 'Could not get sizes');
        }
    }

    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();
        $data['width'] = str_replace('+', '', $data['width']);
        $data['height'] = str_replace('+', '', $data['height']);
        $data['length'] = str_replace('+', '', $data['length']);
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $size = Size::query()->create($data);

            DB::commit();

            return $this->success(200, ['size' => $size], "Size created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, $admin->id, 'Admin/SizeController create action');
            return $this->error(400, "Size create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $data['width'] = str_replace('+', '', $data['width']);
        $data['height'] = str_replace('+', '', $data['height']);
        $data['length'] = str_replace('+', '', $data['length']);
        $admin = $request->user();

        try {
            DB::beginTransaction();

            $size = Size::query()->find($data['id']);
            $size->update($data);

            DB::commit();
            return $this->success(200, ['size' => $size], "Size updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin/SizeController update action', $admin->id);
            return $this->error(400, "Size update failed.");
        }
    }
}
