# about package
gateway payment integration for cashu Package created By PhpAnonymous Laravel 5.0 above ^ 
package version 1.0
# Installation
run on your composer or terminal supported by composer this command 
` composer require cashuanony/phpanonymous:dev-master `

after download this package you can add this line on your providers array in file app.php `config/app.php`

```php 
         CashUAony\Phpanonymous\PhpAnonymousCashUProviders::class,
```

and this aliases array from package 
```php 

        'CashU'     => CashUAony\Phpanonymous\CashU::class,


```

now run publish command for push file cashu.php in to your config folder  ` php artisan vendor:publish`

now you can sse this file on your path ` config/cashu.php ` 

#content file setup 

```php 
<?php
return [
	'sandbox'  =>'https://sandbox.cashu.com/secure/payment.wsdl',  
	'live'  =>'https://cashu.com/secure/payment.wsdl',  
	'follow_sandbox'  =>'https://sandbox.cashu.com/cgi-bin/payment/pcashu.cgi',  
	'follow_live'  =>'https://cashu.com/cgi-bin/payment/pcashu.cgi',  
	'trace'  =>true,  // true or false if default it "true"
	'_testmod'  =>'0',  // 0 for test mode or sandbox  1 for live mode
	'secure'  =>'default', // default for default encryption  | full to Full Encryption
	'_session_id'  =>'',   // any key for  6 char or above 
	'_encryption_key'=>'', //encryption  with Service Default or Other Service 
	'_merchant_id'  =>'',  // marchant name for cashu site | Account Name
];
```

make a new account for http://cashu.com

if you want sandbox account follow this link and make new account with test mode sandbox http://sandbox.cashu.com/Merchants/en/login


##simple example usage
```php

Route::get('cashu',function(){
	$data = [];
	$data['amount'] = '100'; // your amount here
	$data['currency'] = 'usd'; // currency type for lowercase 
	$data['display_text'] = 'PAy For Phpanonymous'; // addtional display text  like product name or discription anything  
	$data['lang'] = 'ar'; // language arabic or english ( ar , en ) for lowercase
	$data['item1'] = 'test test'; // item text start for one to five item withcashu
	$data['item2'] = '';
	$data['item3'] = '';
	$data['item4'] = '';
	$data['item5'] = '';
	$data['service_name'] = 'PaymentPhpAnonymous'; // service name with setup on your account
	return CashU::Go($data);
});


```

# simple callback 
```php
Route::get('cashu/callback',function(Request $request){
	return dd($request->all()); // after finshed proccess return all method status and session id
	// here you can return array and use key and values from it.
	//return array like 
	/*
	'netAmount'=>'',
	'currency'=>'',
	'amount'=>'',
	'trnDate'=>'',
	'session_id'=>'',
	'token'=>'',


	*/ 
});
```
now you can convert test mode to live with array from file cashu.php on your config folder 

return your call back with Request Illuminate Class Or Aliases Class Anyway... both you can work by it 

# Finally 

See The parameter callback and use it if you want any where 

package is auto submit redirect for cashu pay just use this parameters as  your account on cashu sandbox or live don't forget service encryption key 

if you have any questions  you can join us form group on https://www.facebook.com/groups/anonymouses.developers/


Enjoy !! 


Bye
