<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::get(config('nelo.nelo_api_url')."/products/attribute");
        if($response->ok()){
            foreach($response->json() as $attr){
                \App\Models\Attribute::create([
                    'name' => $attr['name'],
                    'external_id' => $attr['id'],
                ]);
            }
        }
    }
}
