<?php

return [
    'nelo_api_url' => env('NELO_API_URL', 'https://api.nelo.eu'),

    'emails' => [
      'internal_from' => 'fabrica@nelo.eu',
      'from_name' => 'My Nelo',
      'admins' => ['nuno.rammos@gmail.com', /*'nuno.ramos@nelo.eu', 'andre.santos@nelo.eu'*/],
      'external_from' => 'noreply@nelo.eu',
    ],
];
