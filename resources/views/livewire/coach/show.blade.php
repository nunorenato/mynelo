<?php

use App\Enums\UnitsEnum;
use App\Helpers\SessionDataConvertible;
use \App\Models\Coach\Session;
use App\Models\Coach\SessionSelection;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Livewire\Volt\Component;
use UnitConverter\Unit\Length\Metre;

new class extends Component {

    final protected const SELECTION_TYPE_TIME = 1;
    final protected const SELECTION_TYPE_DISTANCE = 2;

    public Session $session;
    public SessionSelection $sessionSelection;
    public SessionDataConvertible $fullSessionConvertible;

    public string $selectionStartTime;
    public string $selectionEndTime;
    public int $selectionStartDistance;
    public int $selectionEndDistance;
    public int $selectionStartTimestamp;
    public int $selectionEndTimestamp;
    public int $selectionType = 1;

    public UnitsEnum $units;

    public bool $showDescription = false;
    public ?string $description;

    public function mount(Session $session)
    {

        $this->authorize('view', [$session]);

        $this->session = $session;
        $this->units = UnitsEnum::Kilometers;

        $this->selectionStartTime = '00:00:00';
        $this->selectionEndTime = date('H:i:s', $session->end_time - $session->start_time);
        $this->selectionStartDistance = 0;
        $this->selectionEndDistance = floor($this->session->distance);

        $this->description = $this->session->details;

    }

    #[\Livewire\Attributes\Computed]
    public function gpsCoords()
    {
        return \App\Http\Resources\GpsCoordsResource::collection($this->session->sessionData()
            ->whereNot('gpsx', 0)->whereNot('gpsx', -1)
            ->whereNot('gpxy', 0)->whereNot('gpxy', -1)
            ->get()
        )->collection;
    }

    /**
     * Start and end point are positions from the data array
     *
     * @param int $startPoint
     * @param int $endPoint
     * @return void
     */
    public function selectFromChart(int $startPoint, int $endPoint)
    {

        //$startTime = $this->session->sessionData[$startPoint];
        //$endTime = $this->session->sessionData[$endPoint];

        $this->sessionSelection = $this->session->selectionBuilder($this->session->sessionData[$startPoint]->tagtimestamp, $this->session->sessionData[$endPoint]->tagtimestamp);
        $this->selectionStartTime = date('H:i:s', $this->sessionSelection->start_time - $this->session->start_time);
        $this->selectionEndTime = date('H:i:s', $this->sessionSelection->end_time - $this->session->start_time);
        $this->selectionStartDistance = $this->session->timeToDistance($this->sessionSelection->start_time);
        $this->selectionEndDistance = $this->session->timeToDistance($this->sessionSelection->end_time);
        $this->selectionStartTimestamp = $this->sessionSelection->start_time;
        $this->selectionEndTimestamp = $this->sessionSelection->end_time;
    }

    public function selectFromInput()
    {
        $validated = $this->validate([
            'selectionType' => 'numeric',
            'selectionStartTime' => ['sometimes', 'regex:/^\d+:\d+:\d\.?\d*$/', 'required_if:selectionType,' . self::SELECTION_TYPE_TIME],
            'selectionEndTime' => ['sometimes', 'regex:/^\d+:\d+:\d\.?\d*$/', 'required_if:selectionType,' . self::SELECTION_TYPE_TIME],
            'selectionStartDistance' => ['sometimes', 'numeric', 'gte:0', 'lt:selectionEndDistance', 'required_if:selectionType,' . self::SELECTION_TYPE_DISTANCE],
            'selectionEndDistance' => ['sometimes', 'numeric', 'gt:selectionStartDistance', 'lt:' . $this->session->distance, 'required_if:selectionType,' . self::SELECTION_TYPE_DISTANCE],
        ]);

        if ($validated['selectionType'] == 1) {

            // Convert formated time into seconds
            if (Str::contains($validated['selectionStartTime'], '.')) {
                $start = CarbonInterval::createFromFormat('H:i:s.v', $validated['selectionStartTime'])->total('seconds');
            } else {
                $start = CarbonInterval::createFromFormat('H:i:s', $validated['selectionStartTime'])->total('seconds');
            }
            if (Str::contains($validated['selectionEndTime'], '.')) {
                $end = CarbonInterval::createFromFormat('H:i:s.v', $validated['selectionEndTime'])->total('seconds');
            } else {
                $end = CarbonInterval::createFromFormat('H:i:s', $validated['selectionEndTime'])->total('seconds');
            }

            // additional validations
            \Illuminate\Support\Facades\Validator::make(
                data: ['selectionStartTime' => $start, 'selectionEndTime' => $end],
                rules: [
                    'selectionStartTime' => ['lt:selectionEndTime', 'gte:0'],
                    'selectionEndTime' => ["lte:" . ($this->session->end_time - $this->session->start_time), 'gt:0'],
                ],
                messages: [
                    'selectionStartTime.lte' => 'The selection start time must be less than the end time',
                    'selectionEndTime.lte' => 'The selection end time must be less than the full length',
                ]
            )->validate();

            // add offset timestamp
            $start += $this->session->start_time;
            $end += $this->session->start_time;

            // adjust selection
            $this->sessionSelection = $this->session->selectionBuilder($start, $end);

        } else {
            $this->sessionSelection = $this->session->selectionDistanceBuilder($validated['selectionStartDistance'], $validated['selectionEndDistance']);
        }

        $this->selectionStartTimestamp = $this->sessionSelection->start_time;
        $this->selectionEndTimestamp = $this->sessionSelection->end_time;

        $this->dispatch('input-selection');
    }

    public function resetFromChart()
    {
        unset($this->sessionSelection);
        $this->selectionStartTime = '00:00:00';
        $this->selectionEndTime = date('H:i:s', $this->session->end_time - $this->session->start_time);
        $this->selectionStartDistance = 0;
        $this->selectionEndDistance = $this->session->distance;
    }

    public function resetFromInput()
    {
        $this->resetFromChart();
        $this->dispatch('reset-selection');
    }

    public function getChart():array
    {

        $workSession = $this->sessionSelection ?? $this->session;

        $data = $workSession->sessionData;

        if(count($data) == 0){
            return [
                "chart" => [
                    "events" => [],
                    "zoomType" => 'x',
                ],
                'title' => ['text' => 'No data'],
            ];
        }


        $startTime = new Carbon($data->first()->tagtime);
        $spmMax = $workSession->sessionData()->max('spm');

        // Todo
        $unit = $this->units->printableUnits()['speed'];

        $xValues = [];
        $speedValues = [];
        $spmValues = [];
        $dpsValues = [];
        $heartValues = [];
        $accDistance = 0;
        foreach ($data as $row) {
            $convertible = new SessionDataConvertible($row, $this->units);
            if ($this->selectionType == self::SELECTION_TYPE_TIME) {
                $xValues[] = Carbon::createFromTimestamp($row->relativeTimestamp)->format('H:i:s');//date('H:i:s', $row->relativeTimestamp);
            } else {
                $accDistance += $row->speed;
                $xValues[] = Number::format((new Metre($accDistance))->as($this->units->toUnitConverter()['distance']), $this->units==UnitsEnum::Meters?0:2);
            }
            $speedValues[] = round($convertible->getSpeed(), 1);
            $spmValues[] = round($row->spm, 0);
            $dpsValues[] = round($row->dps, 1);
            $heartValues[] = $row->heart;
        }

        //$this->chartData = $data;

        return [
            "chart" => [
                "events" => [],
                "zoomType" => 'x',
            ],
            'title' => ['text' => ''],
            'xAxis' => [
                "categories" => $xValues,
                "title" => [
                    "text" => $this->selectionType == self::SELECTION_TYPE_TIME ? 'Time' : 'Distance',
                ],
                "minTickInterval" => count($xValues) / 10,
            ],
            "yAxis" => [
                [
                    "labels" => [
                        "format" => "{value}"
                    ],
                    "min" => 0,
                    "title" => [
                        "text" => "Speed " . $unit
                    ],
                ],
                [
                    "labels" => [
                        "format" => "{value}"
                    ],
                    "opposite" => "true",
                    "min" => 0,
                    "max" => ceil($spmMax / 5) * 5,
                    "title" => [
                        "text" => "SPM"
                    ],
                ],
                [
                    "labels" => [
                        "format" => "{value}"
                    ],
                    "opposite" => "true",
                    "min" => 0,
                    "title" => [
                        "text" => "DPS"
                    ],
                ],
                [
                    "labels" => [
                        "format" => "{value}"
                    ],
                    "opposite" => "true",
                    "min" => 0,
                    "title" => [
                        "text" => "HR"
                    ],
                ],
            ],
            "tooltip" => [
                "valueSuffix" => "",
                "shared" => true,
            ],
            "legend" => [
                "title" => [
                    "text" => 'Values <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>'
                ],
                "align" => "center"
            ],
            'rangeSelector' => [
                'enabled' => true,
            ],
            "series" => [
                [
                    "name" => "Speed",
                    "lineWidth" => 1,
                    "color" => "#FF0000",
                    "data" => $speedValues,
                    "marker" => [
                        "enabled" => false,
                    ],
                ],
                [
                    "name" => "SPM",
                    "lineWidth" => 1,
                    "color" => "#000000",
                    "data" => $spmValues,
                    "yAxis" => 1,
                    "marker" => [
                        "enabled" => false,
                    ],
                ],
                [
                    "name" => "DPS",
                    "lineWidth" => 1,
                    "color" => "#9900FF",
                    "data" => $dpsValues,
                    "yAxis" => 2,
                    "marker" => [
                        "enabled" => false,
                    ],
                    "visible" => false,
                ],
                [
                    "name" => "HR",
                    "lineWidth" => 1,
                    "color" => "#0099FF",
                    "data" => $heartValues,
                    "yAxis" => 3,
                    "marker" => [
                        "enabled" => false,
                    ],
                    "visible" => false,
                ],
            ]

        ];
    }

    public function saveDescription(){
        $this->session->details = $this->description;
        $this->session->save();

        $this->showDescription = false;
    }
}
?>
<div>
    <x-mary-header title="Your training on {{ $session->createdon }}" subtitle="{{ $description }}" separator>
        <x-slot:actions>
            <x-mary-button icon="o-pencil-square" @click="$wire.showDescription = true" />
            <x-mary-select wire:model.change="units" wire:loading.attr="disabled" :options="UnitsEnum::cases()" option-label="name" option-value="value" x-on:change="$dispatch('units-changed')" />
        </x-slot:actions>
    </x-mary-header>
    <x-mary-card title="Full Session Summary" class="mb-5">
        <livewire:coach.summary :session="$session" :units="$units" />
    </x-mary-card>
    <x-mary-card class="mb-5" title="Analysis">
        @if(!empty($sessionSelection))
            <livewire:coach.summary :session="$sessionSelection" :units="$units"
                                    :key="$sessionSelection->start_time.'-'.$sessionSelection->end_time" />
        @else
            Make a selection on the chart or the start and end fields to analyse just a portion of the session.
        @endif
        <x-mary-progress class="progress-accent h-0.5 my-5" indeterminate wire:loading></x-mary-progress>
        <hr class="my-5" wire:loading.remove />
        <div class="flex gap-5">
            <div class="basis-1/6 flex-none">
                <x-mary-form wire:submit="selectFromInput" no-separator>
                    <x-mary-select
                        wire:model="selectionType"
                        label="Show by"
                        :options="[['id' => self::SELECTION_TYPE_TIME, 'name' => 'Time'], ['id' => self::SELECTION_TYPE_DISTANCE, 'name' => 'Distance']]"
                        wire:change="$set('selectionType', $event.target.value)"
                        wire:loading.attr="disabled"
                        inline></x-mary-select>
                    @if($selectionType == self::SELECTION_TYPE_TIME)
                        <div wire:transition>
                            <x-mary-input label="Start time" wire:model="selectionStartTime" wire:loading.attr="disabled"
                                          class="mb-5"
                                          inline></x-mary-input>
                            <x-mary-input label="End time" wire:model="selectionEndTime" wire:loading.attr="disabled"
                                          inline></x-mary-input>
                        </div>
                    @else
                        <div wire:transition>
                            <x-mary-input label="Start point" wire:model="selectionStartDistance" wire:loading.attr="disabled"
                                          class="mb-5"
                                          inline></x-mary-input>
                            <x-mary-input label="End point" wire:model="selectionEndDistance" wire:loading.attr="disabled"
                                          inline></x-mary-input>
                        </div>
                    @endif
                    <x-mary-button label="Apply" type="submit" spinner="selectFromInput" wire:loading.attr="disabled" ></x-mary-button>
                    <x-mary-button label="Reset" wire:click="resetFromInput()" wire:loading.attr="disabled" class="btn-neutral" spinner></x-mary-button>
                </x-mary-form>
            </div>
            <div class="basis-5/6">
                <div id="graphContainer" wire:ignore></div>
            </div>
        </div>
    </x-mary-card>
    <x-mary-card class="">
        <div wire:ignore id="map" class="w-full h-96"></div>
    </x-mary-card>

    <x-mary-modal title="Edit session description" wire:model="showDescription">
        <x-mary-form wire:submit="saveDescription">
            <x-mary-textarea
                placeholder="Notes on this session"
                wire:model="description"
                rows="3"
                inline
            />

            <x-slot:actions>
                <x-mary-button label="Save" class="btn-success" type="submit" spinner />
                <x-mary-button label="Cancel" @click="$wire.showDescription = false" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
@assets
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__",
            m = document, b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
    })({
        key: "{{ config('nelo.youtube.api_key') }}",
        v: "weekly",
        // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
        // Add other bootstrap parameters as needed, using camel case.
    });
</script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
@endassets
@script
<script>

    let map;
    let highcharts = null;
    let forcedReset = false;

    async function initMap() {

        const {Map} = await google.maps.importLibrary("maps");
        const {AdvancedMarkerElement} = await google.maps.importLibrary("marker");
        const {PinElement} = await google.maps.importLibrary("marker");

        map = new Map(document.getElementById("map"), {
            zoom: 3,
            center: {lat: 0, lng: -180},
            mapTypeId: "terrain",
            gestureHandling: "cooperative",
            mapId: '26e438e7e3510f42',
        });
        const flightPlanCoordinates = {!! $this->gpsCoords !!};
        const flightPath = new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
        });

        flightPath.setMap(map);
        zoomToObject(flightPath);

        @if(count($this->gpsCoords) > 0)

        const start = new AdvancedMarkerElement({
            map,
            position: {lat: {{$this->gpsCoords->first()->gpsx}}, lng: {{ $this->gpsCoords->first()->gpxy }}},
            title: 'Start',
            content: new PinElement({
                glyphColor: 'white',
                background: "#0bae35",
            }).element,
        });
        const end = new AdvancedMarkerElement({
            map,
            position: {lat: {{$this->gpsCoords->last()->gpsx}}, lng: {{ $this->gpsCoords->last()->gpxy }}},
            title: 'Finish',
            content: new PinElement({
                glyphColor: 'white',
                background: "#cc0000",
            }).element,
        });

        @endif
    }

    function zoomToObject(obj) {
        var bounds = new google.maps.LatLngBounds();
        var points = obj.getPath().getArray();
        for (var n = 0; n < points.length; n++) {
            bounds.extend(points[n]);
        }
        map.fitBounds(bounds);
    }

    function initChart() {

        let chartData = {!! json_encode($this->getChart()) !!};
        chartData.chart.events.selection = function (event) {
            if (event.resetSelection) {
                $wire.resetFromChart();
                if(forcedReset){
                    forcedReset = false;
                    loadChart();
                }
            } else {
                //console.log(event.xAxis[0].min, event.xAxis[0].max);
                $wire.selectFromChart(event.xAxis[0].min, event.xAxis[0].max);
            }
        };

        highcharts = Highcharts.chart('graphContainer', chartData);


    }

    function reloadChart() {

        if(highcharts == null){
            console.log('Highcharts not loaded');
            return false;
        }

        highcharts.showLoading();
        $wire.getChart().then(chartData => {
            //console.log('Returned Data:', result);
            highcharts.update(chartData);
            highcharts.hideLoading();
        });
    }

    document.addEventListener('livewire:initialized', function () {
        // init google maps
        initMap();

        // init  chart
        initChart();

        $wire.on('input-selection', function (event) {
            console.log('input selection');
            if(highcharts != null){
                highcharts.showResetZoom();
                forcedReset = true;
            }
            reloadChart();
        });

        $wire.on('reset-selection', function (event) {
            console.log('reset selection');
            if(highcharts != null) {
                highcharts.zoomOut();
                forcedReset = false;
            }
            reloadChart();
        });

        $wire.on('units-change', function (event) {
            console.log('changed units');
            reloadChart();
        });
    });
</script>
@endscript
