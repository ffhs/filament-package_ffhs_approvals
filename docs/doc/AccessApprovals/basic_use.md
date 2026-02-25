---
sidebar_position: 1
---

# Basic: Accessing the Approval State

To check whether a model is **open**, **pending**, **approved**, or **denied**, you can use the following helper
methods:

- `isOpen()`
- `isPending()`
- `isApproved()`
- `isDenied()`

Alternatively, you can retrieve the current state via `approved()` and handle it using a `match` expression.

```php
<?php

use App\Enums\ApprovalState;
use App\Contracts\Approvable;

/** @var Approvable $myModel */
$myModel = MyModel::query()->first();

// Using helper methods
if ($myModel->isOpen()) {
    return 'Approval state is open';
}

if ($myModel->isDenied()) {
    return 'Approval state is denied';
}

if ($myModel->isPending()) {
    return 'Approval state is pending';
}

if ($myModel->isApproved()) {
    return 'Approval state is approved';
}

// Alternatively: Handle the state via Enum
return match ($myModel->approved()) {
    ApprovalState::OPEN => 'Approval state is open',
    ApprovalState::DENIED => 'Approval state is denied',
    ApprovalState::PENDING => 'Approval state is pending',
    ApprovalState::APPROVED => 'Approval state is approved',
};
