<x-email-layout>
    <h1>Welcome to MyNelo</h1>
    <p>MyNelo is your home for all Nelo related. A single place for all your Nelo products and services.</p>
    <p>Since you were already a registered customer, we have automatically transferred your registration to this new app.</p>
    <hr>
    <p>
        <i>Username:</i> {{ $user->email }}<br>
        <i>Password:</i> {{ $password }} <small>(Please change your password as soon as possible)</small>
    </p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn">
        <tbody>
        <tr>
            <td> <a href="{{ config('app.url') }}" target="_blank">Discover MyNelo</a> </td>
        </tr>
        </tbody>
    </table>
</x-email-layout>
