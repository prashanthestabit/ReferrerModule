# Referral Module laravel

If you think to create a referral module. don't think, just clone and use. the Referral module is a pre-built and maintained module that provides all basic and necessary functionality for referral management in a Laravel project. The module includes features such as user retrieves the referral ID associated with the currently authenticated user, creates a new referral record, retrieves information about a user and the number of referrals they have made, updates an existing referral record in the database, delete a referral record ,update a referral status. By using this module, developers can save time and effort in implementing these common referral management features in their projects, while promoting consistency and standardization in module design and implementation.

# Requirement

1. [Laravel freamwork](https://laravel.com/) 
2. [nWidart/laravel-modules package](https://nwidart.com/laravel-modules/v6/installation-and-setup)
3. [JWT authentication](https://jwt-auth.readthedocs.io/en/develop/)

## Steps to use this module

#### Step 1:- Install Module Package Libraray


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
Tip: don't forget to run <b>composer dump-autoload</b> afterwards

Step 1.2: clone the code in modules folder

Tip: don't forget to run 

``` bash
    php artisan module:enable ReferrerModule
```

#### Step 2:- install JWT Authentication [official documentation](https://jwt-auth.readthedocs.io/en/develop/)

#### Step 3:- Run php artisan migrate

#### Step 4:- Update user model 

``` bash
    use Modules\ReferrerModule\Traits\ReferrerTraits;

    class User extends Authenticatable implements JWTSubject
    {
        use HasApiTokens, HasFactory, Notifiable, ReferrerTraits;
    }
```

#### Step 5:- update Console/Kernal.php 

``` bash
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update:referrer-ids')->daily();
    }
```

#### For update referrer-ids in user table run command

``` bash
    php artisan update:referrer-ids
```
## Features

1) [Retrieves the referral ID associated with the currently authenticated user](#1-retrievesthereferralidassociatedwiththecurrentlyauthenticateduser)
2) [creates a new referral record in the database](#2-createsanewreferralrecordinthedatabase)
3) [updates an existing referral record in the database](#3-updatesanexistingreferralrecordinthedatabase)
4) [delete a referral record](#4-deleteareferralrecord)
5) [update a referral status](#5-updateareferralstatus)
6) [retrieves information about a user](#6-retrievesinformationaboutauser)

## EndPoints

#### 1. RetrievesthereferralIDassociatedwiththecurrentlyauthenticateduser

```bash
URL:- /api/get-referrer-id
Method:- GET
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

#### 2. createsanewreferralrecordinthedatabase

```bash
URL:- /api/referrers/store
Method:- POST
Request Body:- token (required),referrer_name (required), referrer_email (required , email)
referred_name (required, string), referred_email (required , email),
referred_code(required, valid)
```

Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     referrer_name   |     string         |       Yes           |      Referrer Name             |
|     referrer_email  |     email          |       Yes           |      Referrer Email            |
|     referred_name   |     string         |       Yes           |      Referred Name             |
|     referred_email  |     email          |       Yes           |      Referred Email            |
|     referred_code   |     string         |       Yes           |      Referred Code             |

#### 3. updatesanexistingreferralrecordinthedatabase

```bash
URL:- /api/referrers/update/{id}

Method:- PUT
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     referrer_name   |     string         |       Yes           |      Referrer Name             |
|     referrer_email  |     email          |       Yes           |      Referrer Email            |
|     referred_name   |     string         |       Yes           |      Referred Name             |
|     referred_email  |     email          |       Yes           |      Referred Email            |
|     referred_code   |     string         |       Yes           |      Referred Code             |

#### 4. deleteareferralrecord

```bash
URL:- /api/referrers/{id}

Method:- DELETE
```

Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

#### 5. updateareferralstatus

```bash
URL:- /api/update/referrer/status

Method:- POST
```

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     id              |     integer        |       Yes           |      referral Id               |
|     status          |     string         |       Yes           |      referral Status           |

#### 6. retrievesinformationaboutauser.

```bash
URL:- /api/user/referrers/{userId}
Method:- POST
```

Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

## NOTE:- For testing the api you can run the following command


```bash
    php artisan test Modules/ReferrerModule/Tests/Unit/ReferrerModuleControllerTest.php
```
