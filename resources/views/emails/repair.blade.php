<x-email-layout>
    <div class="btn">
        <h1>An user filled the repair form.</h1>
        <h2>User information</h2>
        <dl>
            <dt>Name</dt>
            <dd>{{ $user->name }}</dd>
            <dt>Email</dt>
            <dd>{{ $user->email }}</dd>
            <dt>Country</dt>
            <dd>{{ $user->country->name }}</dd>
        </dl>
        <h2>Boat</h2>
        <dl>
            <dt>OF</dt>
            <dd>{{ $boat->external_id }}</dd>
            <dt>OF</dt>
            <dd>{{ $boat->model }}</dd>
        </dl>
        <h2>Damage</h2>
        <p>{{ $description }}</p>
    </div>
</x-email-layout>
