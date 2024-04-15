<x-email-layout>
<div class="btn">
    <h1>An user is trying to register a boat.</h1>
    <h2>User information</h2>
    <dl>
        <dt>Name</dt>
        <dd>{{ $registration->user->name }}</dd>
        <dt>Country</dt>
        <dd>{{ $registration->user->country->name }}</dd>
    </dl>
    <h2>Boat registration information</h2>
    <dl>
        <dt>OF</dt>
        <dd>{{ $registration->boat->external_id }}</dd>
        <dt>Model</dt>
        <dd>{{ $registration->boat->model }}</dd>
        <dt>Supposed seller</dt>
        <dd>{{ $registration->seller }}</dd>
    </dl>
    <table>
        <tr>
            <td class="btn-success"><a href="{{ action([\App\Http\Controllers\BoatRegistrationController::class, 'validateRegistration'], ['boatregistration' => $registration->id, 'hash' => $registration->hash]) }}" class="">Validate</a></td>
            <td><a href="{{ action([\App\Http\Controllers\BoatRegistrationController::class, 'cancelRegistration'], ['boatregistration' => $registration->id, 'hash' => $registration->hash]) }}">Cancel registration</a></td>
        </tr>
    </table>
</div>
</x-email-layout>
