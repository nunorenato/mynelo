<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;
use App\Services\ApiClient;

class YoutubeApiClient extends ApiClient
{

    public function __construct()
    {
        parent::__construct(config('nelo.youtube.base_url'));
        $this->setAuth(AuthTypeEnum::Key, [
            'key' => config('nelo.youtube.api_key'),
            'name' => config('nelo.youtube.api_key_name'),
        ]);
        $this->useCache = true;
    }

    public function getPlaylistItems(string $playlistId):array
    {
        $list = $this->getArray('/playlistItems', [
            'part' => 'snippet',
            'playlistId' => $playlistId,
        ]);

        return $list['items']??[];


    }
}
