# Approvals Overview

This package allows you to easily implement approval workflows in your **Filament-powered** Laravel application. You can
define approval logic per model, specify who can approve (based on roles, permissions, or user logic), and expose
powerful UI actions using Filament’s `Infolist` components.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/ffhs/filament-package-ffhs_approvals)
[![Total Downloads](https://img.shields.io/packagist/dt/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/ffhs/filament-package-ffhs_approvals)

## Features:

- ✅ Native PHP Enums for status handling
- 🔁 Define multiple approval flows per model
- 👥 Role-, user-, and permission-based approval logic
- 🧩 Seamless integration with Filament Actions and Forms
- 🎨 Customize icons, labels, tooltips, colors per status
- 🛡️ Control button visibility and approval flow states based on business logic
- 🔔 Built-in confirmation prompts and notifications
- 🧱 Fully expandable

---

## Versions

| Filament Version | Package Version |
|:----------------:|:---------------:|
|       3.x        |     ^1.0.0      |
|       4.x        |     ^2.0.0      |
|       5.x        |       ---       |

---

## Documentation

**You can find the full documentation [here](https://ffhs.github.io/filament-package_ffhs_approvals/).**

---

## Preview

(The lower section are the Approvals)

![](images/preview-image-1.png)

![](images/preview-image-2.png)

---

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

---

## Usage

### 1. Define Approval Status Enum

Create a PHP Enum implementing `HasApprovalStatuses`:

```php
use Ffhs\Approvals\Contracts\HasApprovalStatuses;  
  
enum MyApprovalStatus: string  implements HasApprovalStatuses  
{  
    case APPROVED = 'approved';  
    case INCOMPLETE = 'incomplete';  
    case DENIED = 'denied';  
  
    public static function getApprovedStatuses(): array  
    {  
        return [self::APPROVED];  
    }  
  
    public static function getDeniedStatuses(): array  
    {  
        return [self::DENIED];  
    }  
  
    public static function getPendingStatuses(): array  
    {  
        return [self::INCOMPLETE];  
    }  
}
```

---

### 2. Define Approval Flow in Model

Implement `Approvable` and use the `HasApprovals` trait:

```php
namespace App\Models;

class MyModel extends Model implements Approvable{
	use HasApprovals;

	public function getApprovalFlows(): array  
	{
		return [
			'management_approved' => SimpleApprovalFlow::make()
				->approvalStatus(ApplicationApprovalStatus::cases())
				->aprovalBy([
					SimpleApprovalBy::make('employee')
						->any(),
						
					SimpleApprovalBy::make('manager')
						->permission('can_approve_for_manager'),
						
					SimpleApprovalBy::make('hr')
						->role('hr_role')
						->atLeast(2)
					
				])
		];
	}
}
```

---

### 3. Basic Filament Action Usage

Render the approval action in your Filament resource or view:

```php
// MyModelView.php

ApprovalActions::make('managment_aproved')
```

---

## Testing

```bash  
composer install  
./vendor/bin/testbench vendor:publish --tag="filament-package_ffhs_custom_forms-migrations"    
./vendor/bin/testbench workbench:build  
./vendor/bin/pest test    
```

---

## Credits

- [Kirschbaum Development Group](https://github.com/kirschbaum-development)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
