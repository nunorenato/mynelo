<?php

namespace App\Models;

use App\Enums\PersonTypeEnum;
use App\Models\Person;

class Worker extends Person
{

    protected $attributes = [
        'person_type_id' => PersonTypeEnum::Worker,
    ];
    public function newQuery(){
        return parent::newQuery()->where('person_type_id', '=', PersonTypeEnum::Worker);
    }

}
