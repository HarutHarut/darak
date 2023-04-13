<?php

namespace App\Console\Commands;

use App\Services\SiteMapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var SiteMapService
     */
    private $siteMapService;

    /**
     * Create a new command instance.
     *
     * @param SiteMapService $siteMapService
     */
    public function __construct(SiteMapService $siteMapService)
    {
        $this->siteMapService = $siteMapService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->siteMapService->pagesSitemap();
        $this->siteMapService->staticSitemap();
        $this->siteMapService->branchesSitemap();
        $this->siteMapService->citiesSitemap();
        $this->siteMapService->blogSitemap();
    }
}
