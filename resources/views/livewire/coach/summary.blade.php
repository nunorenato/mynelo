<div class="flex flex-wrap">
    <x-mary-stat
        title="Distance"
        value="{{ $sessionDataConvertible->getDistanceWithUnits() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Duration"
        value="{{ $sessionDataConvertible->getDuration() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Avg Speed"
        value="{{ $sessionDataConvertible->getAvgSpeedWithUnits() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Max Speed"
        value="{{ $sessionDataConvertible->getMaxSpeedWithUnits() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Avg SPM"
        value="{{ $sessionDataConvertible->getAvgSpm() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Max SPM"
        value="{{ $sessionDataConvertible->getMaxSpm() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Avg DPS"
        value="{{ $sessionDataConvertible->getAvgDpsWithUnits() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Avg Heart rate"
        value="{{ $sessionDataConvertible->getAvgHeart() }}"
        class="flex-1"
    />
    <x-mary-stat
        title="Max Heart rate"
        value="{{ $sessionDataConvertible->getMaxHeart() }}"
        class="flex-1"
    />
</div>
