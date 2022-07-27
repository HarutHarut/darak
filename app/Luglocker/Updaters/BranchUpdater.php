<?php


namespace App\Luglocker\Updaters;


use App\Models\Branch;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

trait BranchUpdater
{
    protected ImageService $imageService;
    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function branchUpdate(Branch $branch, array $data): Branch
    {
        if (isset($data['business_id'])) {
            $branch->business_id = $data['business_id'];
        }

        if (isset($data['city_id'])) {
            $branch->city_id = $data['city_id'];
        }

        if (isset($data['name'])) {
            $branch->name = $data['name'];
        }

        if (isset($data['description'])) {
            $branch->description = $data['description'];
        }

        if (isset($data['lat'])) {
            $branch->lat = $data['lat'];
        }

        if (isset($data['lng'])) {
            $branch->lng = $data['lng'];
        }

        if (isset($data['phone'])) {
            $branch->phone = $data['phone'];
        }

        if (isset($data['email'])) {
            $branch['email'] = $data['email'];
        }

        if (isset($data['address'])) {
            $branch->address = $data['address'];
        }

        if (isset($data['status'])) {
            $branch->status = $data['status'];
        }

        if (isset($data['working_status'])) {
            $branch->working_status = $data['working_status'];
        }

        if (isset($data['card_payment'])) {
            $branch->card_payment = $data['card_payment'];
        }

        if (isset($data['meta_title'])) {
            $branch->meta_title = $data['meta_title'];
        }

        if (isset($data['meta_description'])) {
            $branch->meta_description = $data['meta_description'];
        }

        if (isset($data['meta_keywords'])) {
            $branch->meta_keywords = $data['meta_keywords'];
        }

        $branch->save();

        return $branch->fresh();
    }

    public function branchMediaUpdate(Branch $branch, array $data): Branch
    {

        if (isset($data['logo'])) {
            $uniqFilename = $randomString = \Illuminate\Support\Str::random(10) . "_" . Carbon::now()->timestamp;
            if ($branch->logo) {
                Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $branch->logo));
            }
            $fileName = $this->imageService->compressImage($data['logo'], storage_path('app/public/branches/logo/') . $uniqFilename, config('constants.compressImage'));
            $branch['logo'] = env("APP_URL") . '/storage/branches/logo/' . $fileName;
            $branch->save();
        }

        if (isset($data['media'])) {

            $mediaIds = array_column($data['media'], 'id');

            $deleteMedias = Media::query()
                ->where('related_id', '=', $branch->id)
                ->where('related_type', '=', Branch::class)
                ->whereNotIn('id', $mediaIds)
                ->get();

            foreach ($deleteMedias as $deleteMedia) {
                Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $deleteMedia->url));
                $deleteMedia->delete();
            }

//            return response()->json($data['mediaKey']);


            foreach ($data['media'] as $key => $media) {

                if (!$media){
                    continue;
                }

                if (gettype($media) === 'array') {

                    if (!isset($media['id'])) {

                        $newMedia = new Media([
                            'url' => $media['url'],
                            'type' => 0
                        ]);

                        $branch->media()->save($newMedia);
                    }
                } else {
                    $newMedia = new Media([
                        'url' => env("APP_URL") . Storage::url(Storage::putFile('branches', $media)),
                        'type' => 0
                    ]);

                    $branch->media()->save($newMedia);
                }
                if ($data['mediaKey'][$key] == 1){
                    $branch['preview'] = $media['url'];
                    $branch->save();
                }
            }
        } else {
            $deleteMedias = Media::query()
                ->where('related_id', '=', $branch->id)
                ->where('related_type', '=', Branch::class)
                ->get();

            if ($deleteMedias) {
                foreach ($deleteMedias as $deleteMedia) {
                    Storage::delete(str_replace(env('APP_URL') . '/storage/', '', $deleteMedia->url));
                    $deleteMedia->delete();
                }
            }
        }

        return $branch->fresh();
    }
}
