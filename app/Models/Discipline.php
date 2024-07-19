<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;

    class Discipline extends Model {
        protected $fillable = [
        'name',
            'playlist',
        ];

        public function fields():BelongsToMany
        {
            return $this->belongsToMany(Field::class)->withPivot('required');
        }
    }
