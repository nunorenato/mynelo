<div>
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
        <dd>{{ $registration->seller->name }}</dd>
    </dl>
    <p><a href="#">Validate</a></p>
    <p><a href="#">Cancel registration</a></p>
</div>
