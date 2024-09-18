<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\GenderEnum;
use App\Enums\MembershipEnum;
use App\Models\Magento\CustomerEntity;
use App\Models\Magento\PaddleLabSalesOrder;
use App\Notifications\MembershipNotification;
use App\Services\MagentoApiClient;
use App\Services\NeloApiClient;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity, HasRoles;

    protected $connection = 'mysql';

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
        'extras->paddle_lab_discount',
        'external_id',
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
        'membership_id' => MembershipEnum::class,
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

    public function paddleLabCustomer():HasOne
    {
        return $this->hasOne(CustomerEntity::class, 'email', 'email');
            // \App\Models\Magento\CustomerEntity::firstWhere('email', $this->email);
    }

    public function membership():BelongsTo{
        return $this->belongsTo(Membership::class);
    }

    public function allRegisteredBoats():HasMany{
        return $this->hasMany(BoatRegistration::class)
            ->whereIn('status', [\App\Enums\StatusEnum::VALIDATED, \App\Enums\StatusEnum::COMPLETE])
            ->withTrashed();
    }

    public function paddleLabOrders():Collection
    {
        return PaddleLabSalesOrder::allOrders($this);
    }

    /**
     * Determines the current membership for the user and if it has changed, creates the discounts and notifies him/her
     *
     * @return void
     */
    public function evaluateMembership():void{

        Log::info("Evaluating membership status for user {$this->name}. Current membership is {$this->membership?->name}");

        $old = $this->membership_id;

        $this->membership()->associate(Membership::evaluate($this));
        $this->save();

        if($old != $this->membership_id){
            Log::info("Membership updated. New membership is {$this->membership->name}");
            $magento = new MagentoApiClient();
            if($this->membership_id->discountRule() != null){
                $voucher = $magento->generateCoupon($this->membership_id->discountRule());
                $this->update(['extras->paddle_lab_discount' => $voucher]);
            }
            elseif(!empty($this->extras['paddle_lab_discount'])){ // remove coupon
                $coupon = $magento->searchCouponByCode($this->extras['paddle_lab_discount']);
                if(!empty($coupon)){
                    $magento->deleteCoupon($coupon->coupon_id);
                }
                $this->update(['extras->paddle_lab_discount' => null]);
            }

            $nelo = new NeloApiClient();
            $nelo->setDiscount($this, $this->membership_id->boatDiscount());

            if(!empty($this->membership)){
                $this->notify(new MembershipNotification($this->membership));
            }

            // afetar desconto barcos

        }

    }
}
