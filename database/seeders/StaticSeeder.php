<?php

namespace Database\Seeders;

use App\Services\DataService;
use Illuminate\Database\Seeder;

class StaticSeeder extends Seeder
{
    protected $dataService;

    /**
     * StaticSeeder constructor.
     * @param DataService $dataService
     */
    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('Who We Are'),
            'title' => ["en" =>"Who We Are","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en" =>"Who We Are","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_description' => ["en" =>"Who We Are","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_keywords' => ["en" =>"Who We Are","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],

        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('How Works'),
            'title' => ["en"=>"How Works","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en"=>"How Works","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_description' => ["en"=>"How Works","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_keywords' => ["en"=>"How Works","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],

        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('Privacy Policy'),
            'title' => ["en"=>"Privacy Policy","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en"=>"Privacy Policy","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_description' => ["en"=>"Privacy Policy","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_keywords' => ["en"=>"Privacy Policy","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('FAQ'),
            'title' => ["en"=>"FAQ","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en"=>"FAQ","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_description' => ["en"=>"FAQ","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_keywords' => ["en"=>"FAQ","ru"=>"test Title Ru","sp"=>"test Title","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('Privacy Policy for business'),
            'title' => ["en"=>"Privacy Policy for business","ru"=>"Privacy Policy for business Ru","sp"=>"Privacy Policy for business sp","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en"=>"Privacy Policy for business","ru"=>"Privacy Policy for business Ru","sp"=>"Privacy Policy for business sp","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_description' => ["en"=>"Privacy Policy for business","ru"=>"Privacy Policy for business Ru","sp"=>"Privacy Policy for business sp","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
            'meta_keywords' => ["en"=>"Privacy Policy for business","ru"=>"Privacy Policy for business Ru","sp"=>"Privacy Policy for business sp","ch"=>"test Title","am"=>"testTitle","de"=>"test Title","fr"=>"test Title"],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('Terms And Conditions'),
            'title' => ["en"=>"Terms And Conditions","ru"=>"Terms And Conditions Ru","sp"=>"Terms And Conditions sp","ch"=>"Terms And Conditions","am"=>"Terms And Conditions","de"=>"Terms And Conditions","fr"=>"Terms And Conditions"],
            'description_am' => "testDescription",
            'description_ru' => "testDescription",
            'description_en' => "testDescription",
            'description_sp' => "testDescription",
            'description_ch' => "testDescription",
            'description_de' => "testDescription",
            'description_fr' => "testDescription",
            'meta_title' => ["en"=>"Terms And Conditions","ru"=>"Privacy Terms And Conditions Ru","sp"=>"Terms And Conditions sp","ch"=>"Terms And Conditions","am"=>"Terms And Conditions","de"=>"Terms And Conditions","fr"=>"Terms And Conditions"],
            'meta_description' => ["en"=>"Terms And Conditions","ru"=>"Terms And Conditions Ru","sp"=>"Terms And Conditions sp","ch"=>"Terms And Conditions","am"=>"Terms And Conditions","de"=>"Terms And Conditions","fr"=>"Terms And Conditions"],
            'meta_keywords' => ["en"=>"Terms And Conditions","ru"=>"Terms And Conditions Ru","sp"=>"Privacy Terms And Conditions sp","ch"=>"Terms And Conditions","am"=>"Terms And Conditions","de"=>"Terms And Conditions","fr"=>"Terms And Conditions"],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('home'),
            'title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'description_am' => "",
            'description_ru' => "",
            'description_en' => "",
            'description_sp' => "",
            'description_ch' => "",
            'description_de' => "",
            'description_fr' => "",
            'meta_title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_description' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_keywords' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('contact-us'),
            'title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'description_am' => "",
            'description_ru' => "",
            'description_en' => "",
            'description_sp' => "",
            'description_ch' => "",
            'description_de' => "",
            'description_fr' => "",
            'meta_title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_description' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_keywords' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
        ]);
        \App\Models\StaticPages::query()->create([
            'slug' => $this->dataService->getSlug('become-a-partner'),
            'title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'description_am' => "",
            'description_ru' => "",
            'description_en' => "",
            'description_sp' => "",
            'description_ch' => "",
            'description_de' => "",
            'description_fr' => "",
            'meta_title' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_description' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
            'meta_keywords' => ["en"=>"","ru"=>"","sp"=>"","ch"=>"","am"=>"","de"=>"","fr"=>""],
        ]);
    }
}
