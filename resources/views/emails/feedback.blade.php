<x-email-layout>
    <div class="btn">
        <h1>An user sent feedback.</h1>
        <h2>User information</h2>
        <dl>
            <dt>Name</dt>
            <dd>{{ $user->name }}</dd>
            <dt>Country</dt>
            <dd>{{ $user->country->name }}</dd>
        </dl>
        <h2>Feedback</h2>
        <dl>
            <dt></dt>
            <dd>{{ $feedback }}</dd>
            <dt>Rating</dt>
            <dd>{{ $rating }} stars</dd>
            @isset($why)
            <dt>Why?</dt>
            <dd>{{ $why }}</dd>
            @endisset
        </dl>
    </div>
</x-email-layout>
