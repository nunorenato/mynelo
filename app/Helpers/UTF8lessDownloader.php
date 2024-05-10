<?php

namespace App\Helpers;

use Spatie\MediaLibrary\Downloaders\Downloader;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

/**
 * This class was made because the regular downloader wasn't bing able to handle file names with special characters like 'à'
 * We need to remove the ut8 encoding for the url
 */
class UTF8lessDownloader implements Downloader
{

    public function getTempFile(string $url): string
    {
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: Spatie MediaLibrary',
            ],
        ]);

        if (! $stream = @fopen(utf8_decode($url), 'r', false, $context)) {
            throw UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($temporaryFile, $stream);

        fclose($stream);

        return $temporaryFile;
    }
}
