<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the main text shown in the application.
    |
    */

    // 401/403
    'Unauthorized' => [
        'title' => 'Onbevoegd',
        'explanation' => 'U heeft geen toegang tot deze pagina.',
        'notLoggedIn' => [
            'text1' => 'U bent niet ingelogd. U moet waarschijnlijk eerst ',
            'link' => 'inloggen',
            'text2' => ' voordat u de pagina mag zien.',
        ],
        'advice1' => [
            'text1' => 'Als dit niet klopt, neem dan ',
            'link' => 'contact',
            'text2' => ' met ons op.',
        ],
        'advice2' => [
            'text1' => 'Of ga terug naar het ',
            'link' => 'dashboard',
            'text2' => '.',
        ]
    ],

    // 404
    'notFound' => [
        'title' => 'Pagina niet gevonden',
        'explanation' => 'De pagina die u wil bezoeken kan niet gevonden worden.',
        'advice1' => [
            'text1' => 'Probeer het later opnieuw en ga terug naar het ',
            'link' => 'dashboard',
            'text2' => '.',
        ],
        'advice2' => [
            'text1' => 'Of neem ',
            'link' => 'contact',
            'text2' => ' met ons op.',
        ]
    ],

    // 500
    'internalServerError' => [
        'title' => 'Oeps',
        'explanation' => 'Er ging iets fout aan onze kant. Excuses hiervoor.',
        'advice1' => [
            'text1' => 'Probeer het later opnieuw en ga terug naar het ',
            'link' => 'dashboard',
            'text2' => '.',
        ],
        'advice2' => [
            'text1' => 'Of neem ',
            'link' => 'contact',
            'text2' => ' met ons op.',
        ]
    ]
];
