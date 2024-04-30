<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Person extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'people';
    protected $fillable = [
        'name',
        'external_id',
        'image_id',
    ];

    public function personType(): BelongsTo
    {
        return $this->belongsTo(PersonType::class);
    }

  /*  public function photo():BelongsTo
    {
        return $this->BelongsTo(Image::class, 'image_id');
    }*/

    public function __get($key){
        if($key == 'photo')
            return $this->getFirstMediaUrl('*');
        else
            return parent::__get($key);
    }
}
