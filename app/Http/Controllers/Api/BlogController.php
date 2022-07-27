<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use \Throwable;
use App\Models\Blog;
use Mockery\Exception;
use Illuminate\Http\Request;
use App\Models\BlogTranslations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Blog\Show;
use App\Luglocker\Media\MediaActions;
use App\Http\Requests\Api\Blog\Update;
use App\Http\Requests\Api\Blog\Create;
use App\Http\Controllers\ApiController;

class BlogController extends ApiController
{

    use MediaActions;

    public function all(Request $request): JsonResponse
    {
        try {

            $blogs = Blog::query()->paginate(config('constants.pagination.perPage'));

            return $this->success(200, ['blogs' => $blogs]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BlogController all action');
            return $this->error(400, "Blog all failed.");
        }
    }

    public function show(Show $request, $slug): JsonResponse
    {

        try {
            $blog = Blog::query()->where('slug', $slug)->first();

            return $this->success(200, ['blog' => $blog]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BlogController show action');
            return $this->error(400, "Blog show failed.");
        }
    }

    public function topBlogs(Request $request): JsonResponse
    {
        try {

            $blogs = Blog::query()->limit(4)->get();

            return $this->success(200, ['blogs' => $blogs]);

        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BlogController topBlogs action');
            return $this->error(400, "Blog top Blogs failed.");
        }
    }

    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();


        try {

            DB::beginTransaction();
            $logo = Storage::url(Storage::putFile('blogs/logo', $data['logo']));

            $blog = Blog::query()
                ->create([
                    'logo' => $logo,
                    'name' => $data['name'],
                    'desc' => $data['desc']
                ]);


            $this->addMedia($blog, $data, 'blog');

            DB::commit();

            return $this->success(200, [], "Blog created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e,  'BlogController create action',$user->id);
            return $this->error(400, "Blog create failed.");
        }
    }

    public function update(Update $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {

            DB::beginTransaction();

            $blog = Blog::query()
                ->where('id', '=', $data['blog_id'])
                ->first();

            if (!$blog) {
                throw new Exception('Blog not found.', 404);
            }

            $blog->update([
                'title' =>  $data['title'],
                'desc' =>  $data['desc'],
            ]);


            DB::commit();

            return $this->success(200, [], "Blog updated successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'BlogController update action',$user->id);
            return $this->error(400, "Blog update failed.");
        }
    }


    public function deleteBlog(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        try {

            DB::beginTransaction();

            $blog = Blog::query()
                ->where('id', '=', $id)
                ->where('creator_id', '=', $user->id)
                ->first();

            if (!$blog) {
                throw new Exception('Blog not found.', 404);
            }

            $blog->delete();

            DB::commit();

            return $this->success(200, [], "Blog  deleted successfully.");
        } catch (Exception $e) {
            DB::rollback();
            return $this->error($e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e,  'BlogController deleteBlog action',$user->id);
            return $this->error(400, "Blog  deleted failed.");
        }
    }

}
