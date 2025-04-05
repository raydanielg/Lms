<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cette valeur est disponible dans votre tableau de bord cinetpay
    |--------------------------------------------------------------------------
    */
    'api_key'    => env('CINETPAY_API_KEY'),
    /*
    |--------------------------------------------------------------------------
    | Cette valeur est disponible dans votre tableau de bord cinetpay
    |--------------------------------------------------------------------------
    */
    'site_id'    => env('CINETPAY_SITE_ID'),

    'urls' => [
        /*
        |--------------------------------------------------------------------------
        | L'url qui sera appelé lorsque l'utilisateur effectue un paiement
        | Cet url eutilisé pour effectuer vos traitements en back office
        |--------------------------------------------------------------------------
        */
        'notify' => env('CINETPAY_NOTIFY_URL'),
        /*
        |--------------------------------------------------------------------------
        | L'url qui sera appelé lorsque l'utilisateur effectue un paiement
        |--------------------------------------------------------------------------
        */
        'return' => env('CINETPAY_RETURN_URL'),
        /*
        |--------------------------------------------------------------------------
        | L'url qui sera appelé lorsque l'utilisateur clique sur le bouton annuler le paiment
        | Vous pouvez utiliser ce bouton pour afficher d'autres moyens de paiement ou récupérer le motif de l'annulation
        |--------------------------------------------------------------------------
        */
        'cancel' => env('CINETPAY_CANCEL_URL'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Vous devez créer des routes pour répondres aux différents urls
    | Ci-dessous un exemple de route
    |--------------------------------------------------------------------------
    | Route::get('/cinetpay/cancel', [PaymentController::class, 'cancel'])->name('cinetpay.cancel')
    | Route::post('/cinetpay/notify', [PaymentController::class, 'notify'])->name('cinetpay.notify')
    | Route::post('/cinetpay/return', [PaymentController::class, 'return'])->name('cinetpay.return')
    |
    | Vous pouvez ajouter cette méthode dans votre fichier de routes pour générer
    | automatiquement ces routes
    |
    | \Guysolamour\Cinetpay\Facades\Utils::routes()
    |
    | L'astuce est de créer une class qui extends celle ci et de la renseigner ici
    */
    'controller' => \Guysolamour\Cinetpay\Http\Controllers\PaymentController::class,

    'button' => [
        /*
        |--------------------------------------------------------------------------
        | Utiliser le style par défaut de cinetpay
        |--------------------------------------------------------------------------
        */
        'use_default_style' => true,
        /*
        |--------------------------------------------------------------------------
        | Pour appliquer votre propre classe au bouton. Il faudra passer le use_default_style à false
        |--------------------------------------------------------------------------
        */
        'class' => 'btn btn-danger',
        /*
        |--------------------------------------------------------------------------
        | Pour appliquer votre propre id au bouton. Il faudra passer le use_default_style à false
        |--------------------------------------------------------------------------
        */
        'id'    => 'paybutton',
        /*
        |--------------------------------------------------------------------------
        | Vous pouvez utiliser votre propre style css pour customiser le bouton
        |--------------------------------------------------------------------------
        */
        'style' => "color: white; font-weight: bold",
        /*
        |--------------------------------------------------------------------------
        | Vous pouvez passser des attributs html au bouton.
        |--------------------------------------------------------------------------
        */
        'attributes' => [
            'data-button' => 'pay'
        ],
        /*
        |--------------------------------------------------------------------------
        | Vous pouvez utiliser votre html pour le boutont
        | Le placeholder :label est utilisé pour injecter le texte du bouton. Vous pouvez choisir de l'ignorer
        | et mettre une valeur en dur
        |--------------------------------------------------------------------------
        */
        'html' => "<button class='btn btn-success' :id> <img src='https://aswebagency.com/img/logo.png'></button>",
    ]

];
