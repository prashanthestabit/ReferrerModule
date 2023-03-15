# Referral Module laravel

If you think to create a referral module. don't think, just clone and use. the Referral module is a pre-built and maintained module that provides all basic and necessary functionality for referral management in a Laravel project. The module includes features such as user retrieves the referral ID associated with the currently authenticated user, creates a new referral record, retrieves information about a user and the number of referrals they have made, updates an existing referral record in the database, delete a referral record ,update a referral status. By using this module, developers can save time and effort in implementing these common referral management features in their projects, while promoting consistency and standardization in module design and implementation.

# Requirement

Laravel freamwork -nWidart/laravel-modules package, php 7.2 or higher. for setup nWiart read on official site 
https://nwidart.com/laravel-modules/v6/introduction

# Tip
For this Referral Module we are using the JWT Authentication . for setup jwt read on official site
https://jwt-auth.readthedocs.io/en/develop/

## Steps to use this module

Step 1:- Install Module Package Libraray


```bash
composer require nwidart/laravel-modules
```
Step 1.1: Create Modules folder on root laravel project also register in composer.json

``` bash
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "Modules/"
    }
  }
}
```
Tip: don't forget to run composer dump-autoload afterwards

Step 1.2: clone the code in modules folder

Tip: don't forget to run 

``` bash
php artisan module:enable ReferrerModule
```

Step 2:- install JWT Authentication - for installation read https://jwt-auth.readthedocs.io/en/develop/

Step 3:- Run php artisan migrate

Step 4:- Update user model 

``` bash
use Modules\ReferrerModule\Traits\ReferrerTraits;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, ReferrerTraits;
}
```

Step 5:- update Console/Kernal.php 

``` bash
protected function schedule(Schedule $schedule): void
{
    $schedule->command('update:referrer-ids')->daily();
}
```

For update referrer-ids in user table run command

``` bash
php artisan update:referrer-ids
```

## EndPoints

1. Retrieves the referral ID associated with the currently authenticated user

```bash
URL:- /api/get-referrer-id
Method:- GET
Request Body:- token (required)
```

2. creates a new referral record in the database

```bash
URL:- /api/referrers/store
Method:- POST
Request Body:- token (required),referrer_name (required), referrer_email (required , email)
referred_name (required, string), referred_email (required , email),referred_code(required, valid)
```

3. updates an existing referral record in the database

```bash
URL:- /api/referrers/update/{id}
Method:- PUT
Request Body:- token (required),referrer_name (required), referrer_email (required , email)
referred_name (required, string), referred_email (required , email),referred_code(required, valid)
```

4. delete a referral record from the database.

```bash
URL:- /api/referrers/{id}
Method:- DELETE
Request Body:- token (required)
```

5. update a referral status from the database

```bash
URL:- /api/update/referrer/status
Method:- POST
Request Body:- token (required),id (required, referrer_id), status ('approved','reject'(string))
```

6. retrieves information about a user and the number of referrals they have made.

```bash
URL:- /api/user/referrers/{userId}
Method:- POST
Request Body:- token (required)
```

## NOTE:- For testing the api you can run the following command


```bash
    php artisan test Modules/ReferrerModule/Tests/Unit/ReferrerModuleControllerTest.php
```
