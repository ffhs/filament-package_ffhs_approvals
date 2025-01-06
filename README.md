# FFHS Approvals


## Installation

You can install the package via composer:

```bash
composer require ffhs/filament-package_ffhs_approvals
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-package_ffhs_approvals-config"
```


This is necessary before running our migrations. The contents of the published config file are:

```php
return [
    'models' => [
        'approver' => '\App\Models\User',
        'approval' => Ffhs\Approvals\Models\Approval::class,
    ],
    'tables' => [
        'approvers' => 'users',
        'approvals' => 'approvals',
    ],
];
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-package_ffhs_approvals-migrations"
php artisan migrate
```

## Usage

```php

// ...

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Ffhs\Approvals\Infolists\Actions\ApprovalActions;

// ...

public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('id'),
                TextEntry::make('name'),
                ApprovalActions::make(ReviewStatus::cases())
                    ->category('intake_records')
                    ->scope('initial_review'),
            ]);
    }

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Kirschbaum Development Group](https://github.com/kirschbaum-development)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
