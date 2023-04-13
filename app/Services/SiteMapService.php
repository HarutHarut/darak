<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Branch;
use App\Models\City;
use Illuminate\Support\Str;

class SiteMapService
{

    const SITEMAP = 'app/public/sitemap';

    /**
     * @return string
     */
    public function pagesSitemap()
    {
        $pages = [
            'sitemap/statics.xml',
            'sitemap/branches.xml',
            'sitemap/cities.xml',
            'sitemap/blog.xml'
        ];
        $url = storage_path(self::SITEMAP . '/sitemap.xml');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex  xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($pages as $value) {

            $xml .= '<sitemap>';
            $xml .= ('<loc>' . env('APP_FRONT_URL') . $value . '</loc>');
            $xml .= ('<lastmod>' . date('c', time()) . '</lastmod>');
            $xml .= '</sitemap >';
        }
        $xml .= '</sitemapindex >';

        file_put_contents($url, $xml);
        return $xml;
    }

    /**
     * @return string
     */
    public function staticSitemap()
    {
        $statics = [
            '',
            'who-we-are',
            'how-works',
            'faq',
            'contact-us'
        ];
        $url = storage_path(self::SITEMAP . '/statics.xml');
        $data = $this->sitemap($statics);
        file_put_contents($url, $data);
        return $data;
    }

    /**
     * @return string
     */
    public function branchesSitemap()
    {
        $branches = Branch::query()
            ->where('working_status', 1)
            ->where('is_bookable', 1)
            ->where('status', 1)
            ->pluck('slug');
        $url = storage_path(self::SITEMAP . '/branches.xml');
        $data = $this->sitemap($branches, 'branches/');

        file_put_contents($url, $data);
        return $data;
    }

    /**
     * @return string
     */
    public function citiesSitemap()
    {
        $cities = City::query()
            ->where('name', '!=', 'null')
            ->pluck('slug');
        $url = storage_path(self::SITEMAP . '/cities.xml');
        $data = $this->sitemap($cities, 'cities/');

        file_put_contents($url, $data);
        return $data;
    }

    /**
     * @return string
     */
    public function blogSitemap()
    {
        $blog = Blog::query()->pluck('slug');
        $url = storage_path(self::SITEMAP . '/blog.xml');
        $data = $this->sitemap($blog,  'blog/');

        file_put_contents($url, $data);
        return $data;
    }


    /**
     * @param $data
     * @param string $sub
     * @return string
     */
    protected function sitemap($data, $sub = '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($data as $key) {
            if (isset($key->slug)) {
                $slug = $key->slug;
            } else $slug = $key;
            foreach (['', 'ru/', 'am/', 'fr/', 'ch/'] as $lang) {
                $xml .= '<url>';
                $xml .= ('<loc>' . env('APP_FRONT_URL') . $lang . $sub . str_replace('&', '&amp;', $slug) . '</loc>');
                $xml .= ('<lastmod>' . date('c', time()) . '</lastmod>');
                $xml .= ('<changefreq>daily</changefreq>');
                $xml .= '</url >';
            }
        }
        $xml .= '</urlset>';
        return $xml;
    }
}
