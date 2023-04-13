<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticPages\CreateUpdateRequest;
use App\Models\StaticPages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\DataService;
use Illuminate\Support\Facades\DB;


class StaticPageController extends ApiController
{
    protected $dataService;

    /**
     * StaticPageController constructor.
     * @param DataService $dataService
     */
    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $admin = $request->user();
        try {
            $statics = StaticPages::query();
            if (isset($data['search']) && $data['search'] != null) {
                $statics = $statics->where('title', 'like', '%' . $data['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $data['search'] . '%');
            }
            $statics = $statics->get();

            return $this->success(200, ['statics' => $statics]);
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, 'StaticPageController index action', $admin->id);
            return $this->error(400, 'Static not get sizes');
        }
    }

    /**
     * @param CreateUpdateRequest $request
     * @param $slug
     * @return JsonResponse
     */
    public function update(CreateUpdateRequest $request, $slug): JsonResponse
    {
        $data = $request->all();
//        return response()->json( $data['description']);

        try {
            DB::beginTransaction();
            $static = StaticPages::query()->where('slug', $slug)->first();
            $static->title = $data['title'];

            $static->description_am =  $data['description_am'];
            $static->description_ru =  $data['description_ru'];
            $static->description_en =  $data['description_en'];
            $static->description_sp =  $data['description_sp'];
            $static->description_ch =  $data['description_ch'];
            $static->description_de =  $data['description_de'];
            $static->description_fr =  $data['description_fr'];

//            $static->description = $data['description'];
            $static->meta_title = $data['meta_title'];
            $static->meta_description = $data['meta_description'];
            $static->meta_keywords = $data['meta_keywords'];
            $static->save();
            DB::commit();

            return $this->success(200, ['static' => $static], "Static page updated successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'StaticPageController update action');
            return $this->error(400, "Static page update failed.");
        }
    }
}
