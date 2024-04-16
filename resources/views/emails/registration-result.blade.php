<x-email-layout>
    @if($registration->status == \App\Enums\StatusEnum::VALIDATED)
        <h1>Your boat registration has been validated</h1>
        <p>Great! We have just confirmed your registration request for your {{ $registration->boat->model }} ({{ $registration->boat->external_id }}). We just need some more details from you to finish the registration process.</p>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn">
        <tbody>
        <tr>
            <td> <a href="#" target="_blank">Finish registration</a> </td>
        </tr>
        </tbody>
        </table>
    @else
        <h1>Your boat registration has been canceled</h1>
        <p>It seems we found some issues when validating your {{ $registration->boat->model }} ({{ $registration->boat->external_id }}).</p>
        <p>If you think we made a wrong assessment or you want to know more, please contact us at <a href="mailto:nelo@nelo.eu">nelo@nelo.eu</a> </p>
    @endif
</x-email-layout>
