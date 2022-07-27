<?php


namespace App\Luglocker\Updaters;


use App\Models\SocialNetworkUrl;

trait SocialUrlsUpdater
{
    public function socialUrlsUpdate(SocialNetworkUrl $socialNetworkUrl, array $data): void
    {
        if (isset($data['type'])) {
            $socialNetworkUrl->type = $data['type'];
        }

        if (isset($data['url'])) {
            $socialNetworkUrl->url = $data['url'];
        }

        $socialNetworkUrl->save();
    }
}
