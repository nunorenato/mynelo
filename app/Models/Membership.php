<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Membership extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'rules',
    ];

    protected $casts = [
        'rules' => 'json',
    //    'rules->boat_registrations' => 'integer',
     //   'rules->paddle_lab_value' => 'integer',
    ];

    /**
     * Returns the membership level for the current user in the current state
     * @param User $user
     * @return self|null
     */
    public static function evaluate(User $user):self|null{

        $boatRegistrations = $user->allRegisteredBoats()->count();
        $paddleLabValue = intval($user->paddleLabOrders()->sum('total_invoiced'));

       /* return self::where(function (Builder $query) use ($boatRegistrations) {
                        $query->whereJsonDoesntContainKey('rules->boat_registrations')
                            ->orWhere('rules->boat_registrations', '<=', $boatRegistrations);
                    })
            ->where(function (Builder $query) use ($paddleLabValue){
                $query->whereJsonDoesntContainKey('rules->paddle_lab_value')
                    ->orWhere('rules->paddle_lab_value', '<=', $paddleLabValue);
            })
            ->orderByDesc('sort')
            ->first();
*/

        /*$results = DB::select("SELECT *
                        FROM memberships, JSON_TABLE(rules, '$[*]' COLUMNS( boats integer PATH '$.boat_registrations', sales integer path '$.paddle_lab_value')) as rls
                        WHERE boats <= $boatRegistrations AND sales <= $paddleLabValue
                        ORDER BY sort DESC
                        LIMIT 1");*/

        return self::select('memberships.*')->join(DB::raw("JSON_TABLE(
        memberships.rules,
        '$[*]' COLUMNS(
            boats INTEGER PATH '$.boat_registrations',
            sales INTEGER PATH '$.paddle_lab_value'
        )
    ) as rls"), function($join) use ($boatRegistrations, $paddleLabValue) {
                $join->where('rls.boats', '<=', $boatRegistrations)
                    ->where('rls.sales', '<=', $paddleLabValue);
            })
            ->orderBy('sort', 'desc')
            ->first();
    }
}
