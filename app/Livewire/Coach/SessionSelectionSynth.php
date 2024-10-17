<?php

namespace App\Livewire\Coach;

use App\Models\Coach\Session;
use App\Models\Coach\SessionSelection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportModels\ModelSynth;

class SessionSelectionSynth extends ModelSynth
{
    public static $key = 'ssmdl';

    static function match($target) {
        return $target instanceof SessionSelection;
    }

    function dehydrate($target)
    {
        $dehydrated = parent::dehydrate($target);
        $dehydrated[1]['start_time'] = $target->start_time;
        $dehydrated[1]['end_time'] = $target->end_time;
        // caching so we don't have to crop
        $dehydrated[1]['distance'] = $target->distance;
        $dehydrated[1]['avg_speed'] = $target->avg_speed;
        $dehydrated[1]['max_speed'] = $target->max_speed;
        $dehydrated[1]['max_spm'] = $target->max_spm;
        $dehydrated[1]['avg_spm'] = $target->avg_spm;
        $dehydrated[1]['avg_heart'] = $target->avg_heart;
        $dehydrated[1]['max_heart'] = $target->max_heart;

        return $dehydrated;
    }

    function hydrate($data, $meta) {
        $model = parent::hydrate($data, $meta);
        //$model->crop($meta['start_time'], $meta['end_time']);
        $model->distance = $meta['distance'];
        $model->avg_speed = $meta['avg_speed'];
        $model->max_speed = $meta['max_speed'];
        $model->max_spm = $meta['max_spm'];
        $model->avg_spm = $meta['avg_spm'];
        $model->avg_heart = $meta['avg_heart'];
        $model->max_heart = $meta['max_heart'];

        return $model;
    }
}
