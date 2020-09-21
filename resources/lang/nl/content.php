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

    // General
    'logged_in' => 'U bent ingelogd',
    'dashboard' => 'Dashboard',
    'copyright' => 'Copyright © Burger Participatie in Klimaat Adaptatie (BPIKA) 2020',

    // Dashboard
    'PET_explanation_title' => 'Gevoelstemperatuur volgens de PET-schaal',
    'PET_explanation' =>    'Hitte kan in steden zorgen voor overlast. De maat voor de ‘hittestress’
                            die mensen ervaren wordt in Nederland weergegeven met een index voor
                            gevoelstemperatuur: de PET (Physiological Equivalent Temperature).
                            Hittestress (en ook koudestress) ontstaan als er een onbalans is in van
                            aan- en afvoer van warmte vanuit het menselijk lichaam. Bij de berekening
                            van de PET wordt deze onbalans uitgedrukt in een temperatuurschaal. De
                            meteorologische variabelen die daarbij een rol spelen zijn luchttemperatuur,
                            luchtvochtigheid, windsnelheid en straling (directe en diffuse zonnestraling).
                            Daarnaast spelen de kleding die iemand draagt (kledingisolatie) en de
                            lichamelijke inspanning (metabolisme) een rol. Hoewel de gevoelstemperatuur
                            dus mede afhangt van kleding, lichaamskenmerken en gedrag, wordt bij het
                            berekenen van de PET gevoelstemperatuur vaak uitgegaan van een ‘standaard
                            persoon’ met een specifieke kledingisolatie en metabolisme. PET heeft een
                            temperatuurschaal gelijk aan de luchttemperatuur (hier in Celsius). De PET
                            is per definitie gelijk aan hoe de luchttemperatuur binnenshuis gevoeld wordt
                            waar er geen straling en windinvloeden zijn. De PET kan worden uitgedrukt in
                            klassen van thermische perceptie en het stressniveau.',
    'PET_explanation_source' => 'Bron: RIVM Briefrapport 2019-0008',
    'chart_title' => 'Gevoelstemperatuur (PET hittestress) in de afgelopen week  in de living labs
                        van het project BPiKA',
    'chart_x_axis' => 'Tijd in uren',
    'chart_y_axis' => 'PET in °C',
    'temperature_stress' => [
        'columns' => [
            'PET' => 'PET (ºC)',
            'perception' => 'Perceptie',
            'stress_level' => 'Fysiologisch stressniveau'
        ],
        'rows' => [
            '0-4' => [
                'PET' => '0-4',
                'perception' => 'Heel koud',
                'stress_level' => 'Extreme koudestress'
            ],
            '4-8' => [
                'PET' => '4-8',
                'perception' => 'Koud',
                'stress_level' => 'Sterke koudestress'
            ],
            '8-13' => [
                'PET' => '8-13',
                'perception' => 'Koel',
                'stress_level' => 'Matige koudestress'
            ],
            '13-18' => [
                'PET' => '13-18',
                'perception' => 'Fris',
                'stress_level' => 'Lichte koudestress'
            ],
            '18-23' => [
                'PET' => '18-23',
                'perception' => 'Comfortabel',
                'stress_level' => 'Geen stress'
            ],
            '23-29' => [
                'PET' => '23-29',
                'perception' => 'Beetje warm',
                'stress_level' => 'Lichte warmtestress'
            ],
            '29-35' => [
                'PET' => '29-35',
                'perception' => 'Warm',
                'stress_level' => 'Matige warmtestress'
            ],
            '35-41' => [
                'PET' => '35-41',
                'perception' => 'Heet',
                'stress_level' => 'Grote warmtestress'
            ],
            '>41' => [
                'PET' => '>41',
                'perception' => 'Zeer heet',
                'stress_level' => 'Extreme warmtestress'
            ],
        ]
    ]
];
