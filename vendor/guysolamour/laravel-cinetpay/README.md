# Cinetpay

[![Packagist](https://img.shields.io/packagist/v/guysolamour/laravel-cinetpay.svg)](https://packagist.org/packages/guysolamour/laravel-cinetpay)
[![Packagist](https://poser.pugx.org/guysolamour/laravel-cinetpay/d/total.svg)](https://packagist.org/packages/guysolamour/laravel-cinetpay)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/laravel-cinetpay.svg)](https://packagist.org/packages/guysolamour/laravel-cinetpay)

Ce package est un wrapper autour du  pour effectuer des paiements en ligne.  Vous pouvez visiter leur [site internet](https://cinetpay.com)  pour en savoir plus sur leurs différents services.

## Installation

Install via composer
```bash
composer require guysolamour/laravel-cinetpay
```

## Prerequisites

- PHP >= 8
- Laravel >= 8


### Publish package assets

```bash
php artisan vendor:publish --provider="Guysolamour\Cinetpay\ServiceProvider"
```

## Usage

### Etape 1

Ajoutez les clés api dans le fichier .env

```bash
CINETPAY_API_KEY="your api key"
CINETPAY_SITE_ID="your site id"
# Ces différentes routes seront automatiquement créés pour vous
CINETPAY_NOTIFY_URL="/cinetpay/notify"
CINETPAY_RETURN_URL="/cinetpay/return"
CINETPAY_CANCEL_URL="/cinetpay/cancel"
```

### Etape 2 : Création des routes de redirection



Vous devrez mettre le controller responsable de gérer ces routes dans le fichier de configuration *config/cinetpay.php*

```php
'controller' => \App\Http\Controllers\PaymentController::class,

```
Exemple de controller
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Guysolamour\Cinetpay\Cinetpay;
use Guysolamour\Cinetpay\Http\Controllers\PaymentController as CinetpayPaymentController;

class PaymentController extends CinetpayPaymentController
{
   public function cancel(Request $request)
    {
        // redirect the user where you want
        // return redirect('/'); // or redirect()->home();
    }

    public function return(Request $request, Cinetpay $cinetpay)
    {
        // $cinetpay->getTransactionBuyer();
        // $cinetpay->getTransactionDate()->toDateString();
        // $cinetpay->getTransactionCurrency();
        // $cinetpay->getTransactionPaymentMethod();
        // $cinetpay->getTransactionPaymentId();
        // $cinetpay->getTransactionPhoneNumber();
        // $cinetpay->getTransactionPhonePrefix();
        // $cinetpay->getTransactionLanguage();
        // $cinetpay->isValidPayment();


        if ($cinetpay->isValidPayment()) {
            // success
        } else {
            // fail
        }

        // redirect the user where you want
        // return redirect('/'); // or redirect()->home();
    }

    public function notify(Request $request, Cinetpay $cinetpay)
    {
        // $cinetpay->getTransactionBuyer();
        // $cinetpay->getTransactionDate()->toDateString();
        // $cinetpay->getTransactionCurrency();
        // $cinetpay->getTransactionPaymentMethod();
        // $cinetpay->getTransactionPaymentId();
        // $cinetpay->getTransactionPhoneNumber();
        // $cinetpay->getTransactionPhonePrefix();
        // $cinetpay->getTransactionLanguage();
        // $cinetpay->isValidPayment();


        if ($cinetpay->isValidPayment()){
            // success
        }else {
            // fail
        }

        // redirect the user where you want
        // return redirect('/'); // or redirect()->home();
    }
}

```

Vous pouvez manuellemet créer les routes de redirection depuis votre fichier de route web.php

```php
Route::get('/cinetpay/cancel', [PaymentController::class, 'cancel'])->name('cinetpay.cancel');
Route::post('/cinetpay/notify', [PaymentController::class, 'notify'])->name('cinetpay.notify');
Route::post('/cinetpay/return', [PaymentController::class, 'return'])->name('cinetpay.return')

```

ou uliser cette méthode , toujours depuis le fichier web.php


```php
/*
* Cette fonction enregistra les routes pour vous
*/
 \Guysolamour\Cinetpay\Facades\Utils::routes();

```

### Etape 3

Afficher le bouton de paiement sur une page.

Depuis le controller, créez une instance de la classe **\Guysolamour\Cinetpay\Cinetpay** en le personnalisant avec l'aide des différentes méthodes existantes sur la classe.

Lorsque la fonction init est appellée, elle récupera les cles apis et les routes de redirection dans le fichier .env et construira l'instance de la classe pour vous avec toutes ces valeurs.

```php
$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init();
```
Ajoutez l'identifiant de la transaction, cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement. Cette valeur est obligatoire et il existe plusieurs facons de le générer

```php
$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
;

```



Il existe une méthode statique au sein de la classe qui génère des identifiants de transaction.

```php
$transactionId= \Guysolamour\Cinetpay\Cinetpay::generateTransId(); // cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement
$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
;

```
Ajoutez l'identifiant de l'acheteur pour faire la liaison entre le l'acheteur et le paiement. L'identifiant doit etre unique à chaque acheteur.

```php
$transactionId= \Guysolamour\Cinetpay\Cinetpay::generateTransId(); // cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement
$user = \App\Models\User::first();

$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
  ->setBuyerIdentifiant($user->email ) # or $user->id
;

```

Cette partie n'est pas obligatoire mais vous pouvez ajouter le numéro qui sera utilisé pour pre remplir le formulaire du paiment.

```php
$transactionId= \Guysolamour\Cinetpay\Cinetpay::generateTransId(); // cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement
$user = \App\Models\User::first();

$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
  ->setBuyerIdentifiant($user->email ) # or $user->id
  ->setPhonePrefixe('225') // for ivory coast
  ->setCelPhoneNum('0102030405')
;
```

Ajouter la désignation ou nom ou description du produit à acheter


```php
$transactionId= \Guysolamour\Cinetpay\Cinetpay::generateTransId(); // cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement

$user = \App\Models\User::first();
$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
  ->setBuyerIdentifiant($user->email ) # or $user->id
  ->setPhonePrefix('225') // for ivory coast
  ->setCelPhoneNum('0102030405')
  ->setDesignation('Mackbook pro 2021 m1')
;
```

Ajouter le montant total de la transaction, c'est ce montant qui sera facturé au client.

```php
$transactionId= \Guysolamour\Cinetpay\Cinetpay::generateTransId(); // cette valeur devra etre stocke dans votre application afin d'identififier et vérfier le statut de chaque paiement

$user = \App\Models\User::first();

$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
  ->setBuyerIdentifiant($user->email ) # or $user->id
  ->setPhonePrefix('225') // for ivory coast
  ->setCelPhoneNum('0102030405')
  ->setDesignation('Mackbook pro 2021 m1')
  ->setAmount('950000')
;
```

Enfin passez la variabe à la vue qui sera chargé d'afficher le bouton de paiement.

```php

return view('shop.checkout', ['cinetpay' => $cinetpay]);

```

```php
$user = \App\Models\User::first();
$cinetpay = \Guysolamour\Cinetpay\Cinetpay::init()
  ->setTransactionId($transactionId) // must be unique
  ->setBuyerIdentifiant($user->email ) # or $user->id
  ->setPhonePrefix('225') // for ivory coast
  ->setCelPhoneNum('0102030405')
  ->setDesignation('Mackbook pro 2021 m1')
  ->setAmount('950000')
;
```
### Etape 4: Personnalisation du bouton

Dans le fichier
```php
{!! $cinetpay !!}

```
Pour changer le label du bouton

```php
{!! $cinetpay->show('buy quickly with mobile money') !!}

```
Pour changer l'apparence du boutn (css), il faudra retirer les styles par défaut

```php
# config/cinetpay.php

return [
  'button' => [
    'use_default_style' => false,
  ]
];
```
Ajouter des classes css au bouton

```php
# config/cinetpay.php

return [
  'button' => [
    'class' => 'btn btn-primary ben-block',
  ]
];
```

Ajoutez un identifiant au bouton
```php
# config/cinetpay.php

return [
  'button' => [
    'id' => 'paybutton',
  ]
];
```

Ajouter directement du css inline
```php
# config/cinetpay.php

return [
  'button' => [
    'style' => 'color: white; font-weight: bold',
  ]
];
```
Ajoutez des attributs html au bouton
```php
# config/cinetpay.php

return [
  'button' => [
      'attributes' => [
        'data-button' => 'pay'
      ],
  ]
];
```
ou changer carrement le html du bouton
```php
# config/cinetpay.php

return [
  'button' => [
    # :label pour le label du bouton
    # :id pour l'id du bouton
    # :class pour les classes du bouton
      'html' => "<button class='btn btn-success' :id> <img  src='https://aswebagency.com/img/logo.png'></button>",
  ]
];
```

## Security

If you discover any security related issues, please email
instead of using the issue tracker.

## Credits


- [All contributors](https://github.com/guysolamour/cinetpay/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
