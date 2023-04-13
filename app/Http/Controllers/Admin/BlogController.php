<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Services\BlogService;
use App\Services\ImageService;
use Carbon\Carbon;
use App\Http\Requests\Admin\Blog\{Destroy, Store, Update};
use App\Luglocker\Media\MediaActions;
use App\Models\{Blog, BlogTranslations};
use http\Env\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{JsonResponse};
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use \Throwable;

class BlogController extends ApiController
{

    use MediaActions;

    /**
     * @var ImageService
     */
    private $imageService;
    private $blogService;

    /**
     * BlogController constructor.
     * @param ImageService $imageService
     * @param BlogService $blogService
     */
    public function __construct(
        ImageService $imageService,
        BlogService $blogService
    )
    {
        $this->imageService = $imageService;
        $this->blogService = $blogService;

    }

    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $meta = array('en' => null, 'ru' => null, 'ch' => null, 'am' => null, 'fr' => null);
            $data['meta_title'] = $data['meta_title'] ?? $meta;
            $data['meta_description' ]= $data['meta_description'] ?? $meta;
            $data['meta_keywords'] = $data['meta_keywords'] ?? $meta;

            $blog = Blog::query()
                ->create([
                    "title" => $data['title'],
                    "slug" => $this->blogService->getSetSlug($data['title']['en']),
                    "desc" => $data['desc'],
                    "meta_title" => $data['meta_title'],
                    "meta_description" => $data['meta_description'],
                    "meta_keywords" => $data['meta_keywords'],
                ]);

            if ($request->file('logo')) {
                $path = Storage::url(Storage::putFile('blog/logo', $data['logo']));
                $blog['logo'] = config("app.beck_url") . $path;
                $blog->save();
            }

            DB::commit();

            return $this->success(200, [], "Blog created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BlogController create action');
            return $this->error(400, "Blog create failed.");
        }
    }

    public function update(Update $request, $id): JsonResponse
    {
        $data = $request->validated();

        try {

            DB::beginTransaction();

            $blog = Blog::find($id);
//            return response()->json($data['logo']);
            $blog->update($data);
            if ($request->file('logo')) {
                if($blog->logo){
                    $delete_path = str_replace('/storage', '', $blog->logo);
                    Storage::delete($delete_path);
                }
                $path = Storage::url(Storage::putFile('blog/logo', $request->file('logo')));
                $blog['logo'] = config("app.beck_url") . $path;
                $blog->save();
            }

            DB::commit();
            return $this->success(200, [], __('general.updateBlog'));
        } catch (Exception $e) {
//            dd($e->getMessage());
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'Admin\BlogController update action');
            return $this->error(400, "Blog update failed.");
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Blog::destroy($id);

            return $this->success(200, [], "Blog deleted successfully");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog([], $e, 'Admin\BlogController delete action');
            return $this->error(400, "Blog deleted failed");
        }
    }

}

