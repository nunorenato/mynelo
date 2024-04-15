<x-guest-layout>
    @isset($error)
       <x-mary-alert title="{{$error}}" icon="o-exclamation-triangle" class="alert-error"></x-mary-alert>
    @else
        <x-mary-alert :title="$validated?'Registration was validated successfully!':'Registration was canceled successfully!'" icon="o-check-circle" class="alert-success"></x-mary-alert>
    @endif
</x-guest-layout>
