<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Media\Delete;
use App\Luglocker\Media\MediaActions;
use Illuminate\Http\JsonResponse;
use \Throwable;


class MediaController extends ApiController
{
    use MediaActions;

    public function delete(Delete $request): JsonResponse
    {
        try {
            $this->deleteMedia($request->get('id'));

            return $this->success(200, [], 'Media deleted successfully');
        } catch (\Throwable $e) {
            $this->errorLog($request, $e, $request->user()->id, 'MediaController delete action');
            return $this->error(400, "Media deleting has been failed");
        }
    }

}

