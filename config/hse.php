<?php

return [

    'timezone' => 'Asia/Jakarta',

    'tensi_normal' => [
        'sistol_min' => 90,
        'sistol_max' => 120,
        'diastol_min' => 60,
        'diastol_max' => 80,
        'suhu_min' => 36.5,
        'suhu_max' => 37.5,
    ],

    'foto_max_kb' => 2048,

    'mandor_per_site' => [
        '01' => ['Mandor Sutrisno', 'Subkon Berkah Jaya'],
        '02' => ['Mandor Hambali', 'Subkon Sinar Baru'],
        '03' => ['Mandor Puryanto', 'Subkon Tunas Karya'],
        '04' => ['Mandor Saiful', 'Subkon Lancar Abadi'],
        '05' => ['Mandor Wagiman', 'Subkon Sumber Rezeki'],
    ],

    'id_patterns' => [
        'admin' => '/^ADM-\d{3}$/',
        'inspector' => '/^INS-\d{2}-\d{3}$/',
        'worker' => '/^\d{2}-\d{3}$/',
    ],

];
