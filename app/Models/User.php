<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\GenderEnum;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'date_of_birth',
        'photo',
        'height',
        'weight',
        'gender',
        'club',
        'weekly_trainings',
        'discipline_id',
        'competition',
        'time_500',
        'time_1000',
        'alert_fill',
        'extras',
        'extras->coupon',
        'extras->coupon_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'gender' => GenderEnum::class,
        'competition' => 'boolean',
        'alert_fill' => 'boolean',
        'extras' => 'json',
        'extras->coupon_used' => 'boolean',
    ];

    public function country():BelongsTo{
        return $this->belongsTo(Country::class);
    }

    public function discipline():BelongsTo{
        return $this->belongsTo(Discipline::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
        // Chain fluent methods for configuration options
    }

    public function goals():BelongsToMany{
        return $this->belongsToMany(Goal::class, 'user_goal');
    }

    public function boats():HasMany{
        return $this->hasMany(BoatRegistration::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin():bool{
        return $this->hasRole(Role::findByName('Admin'));
    }

    public function paddleLabCustomer():\App\Models\Magento\CustomerEntity|null
    {
        return \App\Models\Magento\CustomerEntity::firstWhere('email', $this->email);
    }
}
