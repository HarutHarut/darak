<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Str;

class BlogService
{

    public function getSetSlug($name): string
    {
        $slug = Str::slug($name);
        $rows = Blog::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get();
        $count = count($rows) + 1;
        return ($count > 1) ? "{$slug}-{$count}" : $slug;
    }
}
