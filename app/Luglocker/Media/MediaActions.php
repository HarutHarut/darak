<?php

namespace App\Luglocker\Media;

use App\Models\Media;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use \Throwable;

trait MediaActions
{
    /**
     * @param Model $model
     * @param array $data
     * @param string $folder
     * @return void
     * @throws Exception
     */
    public function addMedia(Model $model, array $data, string $folder): void
    {
        try {
            if (isset($data['media'])) {
                foreach ($data['media'] as $item) {
                    $this->addSingleMedia($model, $item['file'], $item['type'], $folder);
                }
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function addSingleMedia(Model $model, $file, $type, string $folder, $mediaKey = null)
    {
//        return $file;
        try {
            $url = env("APP_URL") . Storage::url(Storage::putFile($folder, $file));
            $media = new Media([
                'url' => $url,
                'type' => $type
            ]);
            $model->media()->save($media);
            if($mediaKey){
                $model['preview'] = $url;
                $model->save();
            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete the media
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function deleteMedia(int $id)
    {
        try {
            $media = Media::query()
                ->find($id);

            Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $media->url));

            $media->delete();

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deleteModelMedia(Model $model)
    {
        if ($model->media && $model->media->count()) {
            foreach ($model->media as $media) {
                Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $media->url));
                $media->delete();
            }
        }
        $model->media()->delete();
    }

}
