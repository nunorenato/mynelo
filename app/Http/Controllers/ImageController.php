<?php

namespace App\Http\Controllers;

use App\Models\Image;
use ErrorException;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public static function fromURL(string $url, string $name = null, string $folder = ''):Image|null
    {
        try{
            $imageContent = file_get_contents(str_replace(' ', '%20', $url));
            $info = pathinfo($url);
            $filename = "$folder/".uniqid().'.'.$info['extension'];

            Log::info("Saving image $filename");

            if (! Storage::disk('public')->put($filename, $imageContent)) {
                Log::error("Error saving image $filename");
                return null;
            }

            return Image::create([
                'name' => $name??$info['basename'],
                'path' => Storage::url($filename),
            ]);

        }
        catch (ErrorException $ee){
            Log::error("Error fetchin image $$url");
            return null;
        }


    }
}
