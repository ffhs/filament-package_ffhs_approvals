---
sidebar_position: 2
---

# Setup & Installation

## Installation

You can install the package via composer:

```bash  
composer require ffhs/filament-package_ffhs_approvals  
```  

You can publish the config file with:

```bash  
php artisan vendor:publish --tag="filament-package_ffhs_approvals-config"  
```

You can publish and run the migrations with:

```bash  
php artisan vendor:publish --tag="filament-package_ffhs_approvals-migrations"  
php artisan migrate  
```

## Setup Make an Approver User

In order for a user to perform approvals, the `User` model must implement the `Approver` interface.

Open your `User` class (e.g., located in `app/Models/User.php`) and add the `Approver` interface to the class
declaration.

```php
use Ffhs\Approvals\Contracts\Approver;

class User extends Authenticatable implements Approver{
    ...
}
```

<br/>
