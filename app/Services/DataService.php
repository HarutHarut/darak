<?php

namespace App\Services;

use App\Models\StaticPages;
use Illuminate\Support\Str;

class DataService
{
    public function getSlug($name): string
    {
        $slug = Str::slug($name);
        $rows = StaticPages::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get();
        $count = count($rows) + 1;
        return ($count > 1) ? "{$slug}-{$count}" : $slug;
    }

}
