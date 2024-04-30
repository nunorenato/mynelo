<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\Product;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Product::all() as $product){
            if(!empty($product->image)) {
                try{
                    $product->addMediaFromDisk(str_replace('/storage', '', $product->image->path), 'public')->toMediaCollection('products');
                    $product->image()->disassociate();
                }
                catch (FileDoesNotExist $fdne){
                    dump($fdne->getMessage());
                }
            }

        }

        foreach (Worker::all() as $worker){
            if(!empty($worker->photo)) {
                try{
                    $worker->addMediaFromDisk(str_replace('/storage', '', $worker->photo->path), 'public')->toMediaCollection('people');
                    $worker->photo()->disassociate();
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
