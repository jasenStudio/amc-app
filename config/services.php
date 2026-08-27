<?php

return [
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    ],

    'admin' => [
        'seed_password' => env('ADMIN_SEED_PASSWORD', 'password'),
    ],

    'items' => [
        [
            'slug' => 'puntos-de-anclaje',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCoD6vkYigllqaAJGHHAZRYQywGZl1auAqvPQwJigRqm1dngNhBCbxi_O2GviDoPimxMnkm_7wgvLeFpRbo9VGVMa74hHAM4DMYmCiYBmUP7ADfYNEmMZkGduB_3O6_M2h1sUEZTvtoDaUo3v4TQwBsBdpFAtlkWhUc9Lo71kgku2e_64_nrkIdgbOLS-omB1mxNyamdofXX3Z5mmcjP0ZkIg4KPYSED9Ld5zRmFUV9EMLkT40eqUQfSQ',
            'imageAlt' => 'Instalación de puntos de anclaje',
            'title' => 'Instalación de Puntos de Anclaje',
            'description' => 'Instalación de puntos de anclaje certificados para trabajo seguro en alturas.',
        ],
        [
            'slug' => 'lineas-de-vida-certificadas',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDNDp8Hab55kkvb2ypm2ow71SBOVAl9WgURnbK8UeSfx2XAU8BANvh8dOkk3_EteZ60TyN8rpidBWZ_YNUqJW2pLn_bbvJC22m9hCw9nEFO1xMZCXdr3l35MMxOUzZcBQy1A56TIBP2hjbh7k7JWylASqZmajV4ziH8peFstwkjQ3BxGaxFcw5CaSJeAVOKSntEdlA7CGlOra-BKFElWGZZTVZ9gee_QkfD7E4DS4dkbPOHv34BBIP_Jw',
            'imageAlt' => 'Líneas de vida certificadas',
            'title' => 'Líneas de Vida Certificadas',
            'description' => 'Diseño e instalación de líneas de vida certificadas para proteger cada desplazamiento.',
        ],
        [
            'slug' => 'capacitacion-y-entrenamiento',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCw3tZh6sDXS9rxNt025MIZ_3w40GDirLEw3I8uMQLS5Im68RP9KC2O-_avkvcRczLC5VJ4L-aRVZ42NvoP_WgULu2Nth_9z0_fXII8bsKLjCHZ5S_aBjbalhh5sQvloHxdeRO8KlRsmbexD0ALQ5ah1p4iIvLY5q__GV9tmcRrDiumFdzatcH9gc4ieBF58Srz9RK-CZ1l3ygO-9Hwr0lyMVxcdEWVA0OnmbG2lwCdwvaE0UKGeh7s6w',
            'imageAlt' => 'Capacitación y entrenamiento en trabajo seguro',
            'title' => 'Capacitación y Entrenamiento',
            'description' => 'Formación práctica y certificada para equipos que trabajan en alturas.',
        ],
        [
            'slug' => 'asesoria-en-prevencion-de-riesgos',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBZjmjHX8d_yo3OXGtlbzFkkdO2aKOTt6vkJi3BHqZtIkAwMQ3WbcxRcHpIz9nkXzzudAvKGRD9cVhm-MSFiX51-DQ7c5k7Dh0VLfV-J57jrbPBmuHay9clhNjk9nm8tZYfeUPKp6ybVXfpm_rVA0oubSgnWnfVScftnSMNJJI-O_H2q6s9wWNhnn8uFR3Ag3ZIwkieeD6Rv9v3mZq05-96BMqLaRZtRnKI2K0CqJZtRZlbwhwKATCZxQ',
            'imageAlt' => 'Asesoría para prevención de riesgos laborales',
            'title' => 'Asesoría en Prevención de Riesgos',
            'description' => 'Acompañamiento especializado para fortalecer la prevención y el cumplimiento legal.',
        ],
        [
            'slug' => 'mantenimiento-y-recertificacion',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlNlmiaNEEJFY01gRadWiJnuXsCbyQsvwE3l3CiqCv-rsK1_2wSxXTQ6-G2fDYmTL7lBG36gdubsfiXMY9kNfYOnujn1xJhOkHMTkRmkU2fIFwVDFo5dafUbJ7JNCYdYGtp080oeB65_DV6l-QfSy372SmCF_3U9gWxue-_CWF0XxdMC2gOuMpRjtcZBnMStKhvJDoJIVtYUFg8ijhyuYr_8GESBIXSrQPK2HC5Sc_5r5YK-3RFfQZCA',
            'imageAlt' => 'Mantenimiento y recertificación de sistemas',
            'title' => 'Mantenimiento y Recertificación',
            'description' => 'Inspección, mantenimiento y recertificación para conservar sus sistemas operativos.',
        ],
        [
            'slug' => 'asesoria-en-gestion-de-riesgos',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDx1ZVAy0pyzG4I-gYx1EBbp5Y444_EI60KxTTWIQO5oYc2EP4tz-HT3JF0TjquLiA1h5-Ix_ZWiphUCpnBFoZhOqMFoif3WRVZIi5GqKk0mI7khEG3pGmGknzObf1jPv4WhgpBMsdzJEz5TFZ9nhNWDOLGOpNVdOwbjW7ZfE1aY9vHRfqFA-zCBx5bXTNmWB7TjlDKgmhg-uA9fRsHskTvra-_Syt8kmHcBPoGr4HleuRqCameg0ee2g',
            'imageAlt' => 'Asesoría en gestión de riesgos',
            'title' => 'Asesoría en Gestión de Riesgos',
            'description' => 'Soluciones de gestión para identificar, controlar y reducir los riesgos de su operación.',
        ],
    ],
];
