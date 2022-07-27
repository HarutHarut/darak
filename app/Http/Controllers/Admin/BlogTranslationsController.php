<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\BlogTranslation\Destroy;
use App\Models\{BlogTranslations};
use Illuminate\Http\{JsonResponse};
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use \Throwable;

class BlogTranslationsController extends ApiController
{

    public function destroy(Destroy $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $blogTranslation = BlogTranslations::query()->find($id);
            $blogTranslation->delete();

            DB::commit();
            return $this->success(200, [], "Blog translation deleted successfully");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin\BlogTranslationsController deleteTranslation action');
            return $this->error(400, "Blog translation deleting failed");
        }
    }
}
