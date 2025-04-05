# Laravel Payku

📦 A simple implementation, and ready to use package for Payku.

### Requirements

- Laravel 8 or above

### Installation

```bash
composer require sebacarrasco93/laravel-payku
```

#### Adding .env keys

You need to create a public and private token. You can create and get it from:

- [Payku Development environment](https://des.payku.cl/usuarios/tokenintegracion) 
- [Payku Production environment](https://app.payku.cl/usuarios/tokenintegracion)

```dotenv
PAYKU_PUBLIC_TOKEN={your_public_token}
PAYKU_PRIVATE_TOKEN={your_private_token}
```

#### Important:

Laravel Payku automatically will set the environment URL based in your `APP_ENV` key, in your `.env` project's file

For example, if you set your `APP_ENV` to `local`, it will use `https://des.payku.cl/api`

```dotenv
APP_ENV=local # will set https://des.payku.cl/api
```

Otherwise, if your `APP_ENV` is  on `production`, it will use `https://app.payku.cl/api`

```dotenv
APP_ENV=production # will set https://app.payku.cl/api
```

If you want to force a specific environment, you can set it

```dotenv
PAYKU_ENV=local # or production
```

If you want to force a specific API URL in another environment, you can [learn how to do it](docs/readme-changing-api-url.md)

### Usage

Create a order

```php
// In your controller, web.php or equivalent

$order = 'unique-order-'.rand(11111,99999);
$subject = 'Your order';
$amount = 1000; 
$email = 'test@example.com';

return LaravelPayku::create($order, $subject, $amount, $email);
```

Easy Peasy!

### Extra

If you want more control, you can publish the migrations and configuration

```bash
php artisan vendor:publish --provider="SebaCarrasco93\LaravelPayku\LaravelPaykuServiceProvider"
```

### Notes

I didn't realize how many people were using `Laravel Payku`. So, I decided to take it back, and make an "TODO" list

### Future improvements

I'm working on improving the package, but I have very limited time due to my work.

- [x] Allowing Webpay payment (2024/03/04)
- [x] Update dependencies (2024/03/04)
- [x] Fix method names and tests (2024/03/04)
- [x] Add custom `.env` key `PAYKU_ENV` (2024/03/04)
- [x] Add support to Webpay (again)
- [ ] Add support to Multicaja Efectivo
- [ ]...

### Testing

```bash
./vendor/bin/pest
```
