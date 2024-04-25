<?php
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component{
    use Toast;

    public string $feedbackText;
    public int $rating = 3;
    public string $whyText;

    // slots
    public ?string $extraactions = null;

    public function sendFeedback():void{

        $validated = $this->validate([
            'feedbackText' => ['required','string'],
            'rating' => ['required'],
            'whyText' => ['nullable'],
        ]);

        Mail::to(config('nelo.emails.admins'))
            ->send(new \App\Mail\FeedbackMail(Auth::user(),
                $validated['feedbackText'],
                $validated['rating'],
                $validated['whyText']
            ));

        activity()
            ->by(Auth::user())
            ->event('message')
            ->withProperties($validated)
            ->log('feedback');

        $this->info('Thanks for your feedback!');
       //dump($validated);
        $this->dispatch('feedback-sent');
    }

}
?>
<div>
    <x-mary-form wire:submit="sendFeedback">
        <x-mary-textarea wire:model="feedbackText" label="Feedback" rows="5" placeholder="Tell us about your experience with this site, with Nelo, our products and services"></x-mary-textarea>
        <div class="rating">
            @for($i=1;$i<6;$i++)
                <input type="radio" name="rating" value="{{ $i }}" wire:model="rating" class="mask mask-star bg-orange-400" {{ $i==$rating?'checked':'' }} />
            @endfor
            @error('rating')
                <div class="text-red-500 label-text-alt p-1">{{ $message }}</div>
            @enderror
        </div>
        <x-mary-textarea wire:model="whyText" label="Tell us why" rows="3" placeholder=""></x-mary-textarea>
        <x-slot:actions>
            <x-mary-button label="Send" type="submit" class="btn-primary" spinner></x-mary-button>

                {{ $extraactions }}

        </x-slot:actions>
        <x-mary-radio></x-mary-radio>
    </x-mary-form>
</div>
