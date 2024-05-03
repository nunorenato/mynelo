<?php
/*
use Livewire\Volt\Component;

new class extends Component {

    public \App\Models\Content $content;
    public ?bool $noTitle = false;

}*/
?>
<div>
    @empty($noTitle)
    <h4>{{ $content->title }}</h4>
    @endif
    {!! $content->content !!}
</div>
