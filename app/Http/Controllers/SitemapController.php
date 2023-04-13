<?php

namespace App\Http\Controllers;

class SitemapController extends ApiController
{
    public $LANGS = ['', 'ru/', 'am/', 'fr/', 'ch/'];
    public function branches(){
        $sitemap_contents = \Illuminate\Support\Facades\App::make("sitemap");

        $sitemap_contents->setCache('laravel.sitemap_contents_branches', 3600);
        $branches = \App\Models\Branch::where('status', 1)->where('working_status', 1)->orderBy('created_at', 'desc')->get();
        foreach ($branches as $branch)
        {
            foreach ($this->LANGS as $lang){
                $url = config('app.url'). $lang . 'branches/' . $branch->slug;
                $sitemap_contents->add($url, $branch->updated_at, '1.0', 'daily');
            }
        }
        return $sitemap_contents->render('xml');
    }

    public function cities(){
        $sitemap_contents = \Illuminate\Support\Facades\App::make("sitemap");

        $sitemap_contents->setCache('laravel.sitemap_contents_cities', 3600);
        $branches = \App\Models\City::where('name', '!=', 'null')->where('status', 1)->orderBy('created_at', 'desc')->get();
        foreach ($branches as $branch)
        {
            foreach ($this->LANGS as $lang) {
                $url = config('app.url') .$lang . 'cities/' . $branch->slug;
                $sitemap_contents->add($url, $branch->updated_at, '1.0', 'daily');
            }
        }
        return $sitemap_contents->render('xml');
    }

    public function blog(){
        $sitemap_contents = \Illuminate\Support\Facades\App::make("sitemap");

        $sitemap_contents->setCache('laravel.sitemap_contents_blog', 3600);
        $branches = \App\Models\Blog::get();
        foreach ($branches as $branch)
        {
            foreach ($this->LANGS as $lang) {
                $url = config('app.url') . $lang . 'blog/' . $branch->slug;
                $sitemap_contents->add($url, $branch->updated_at, '1.0', 'daily');
            }
        }
        return $sitemap_contents->render('xml');
    }
}
