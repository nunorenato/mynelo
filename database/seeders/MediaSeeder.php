<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\Product;
use App\Models\Worker;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;

class MediaSeeder extends Seeder
{
    public function run(): void
    {

        dump('here');

        foreach (Product::all() as $product){
            if(!empty($product->image_id)) {
                try{
                    $product->addMediaFromDisk(str_replace('/storage', '', Image::find($product->image_id)->path), 'public')->toMediaCollection('products');
                    $product->image_id = null;
                    $product->save();
                }
                catch (FileDoesNotExist $fdne){
                    dump($fdne->getMessage());
                }
            }

        }

        foreach (Worker::all() as $worker){
            if(!empty($worker->image_id)) {
                try{
                    $worker->addMediaFromDisk(str_replace('/storage', '', Image::find($worker->image_id)->path), 'public')->toMediaCollection('people');
                    $worker->image_id = null;
                    $worker->save();
                }
                catch (FileDoesNotExist $fdne){
                    dump($fdne->getMessage());
                }
            }

        }

        foreach (Boat::all() as $boat){
            foreach($boat->images as $image){
                try{
                    $boat->addMediaFromDisk(str_replace('/storage', '', $image->path), 'public')->toMediaCollection('boats');
                }
                catch (FileDoesNotExist $fdne){
                    dump($fdne->getMessage());
                }
            }
            $boat->images()->detach();

        }
    }
}
