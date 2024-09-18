<?php

namespace App\Enums;

enum MembershipEnum: int{

    case Bronze = 1;
    case Silver = 2;
    case Gold = 3;
    case Platinum = 4;

    public function color():string{
        return match($this){
            self::Bronze => 'orange-700',
            self::Silver => 'slate-400',
            self::Gold => 'yellow-500',
            self::Platinum => 'stone-300'
        };
    }

    public function discountRule():int|null{
        return match($this){
            self::Bronze => config('nelo.magento.discount_rules.5pct'),
            self::Silver => config('nelo.magento.discount_rules.10pct'),
            self::Gold => config('nelo.magento.discount_rules.15pct'),
            self::Platinum => config('nelo.magento.discount_rules.20pct')
        };
    }

    public function boatDiscount():int|null{
        return match($this){
            self::Bronze, self::Silver => 0,
            self::Gold, self::Platinum => 0.1,
        };
    }


}
