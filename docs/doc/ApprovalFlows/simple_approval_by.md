---
sidebar_position: 3
---

# Use SimpleApprovalBy

`SimpleApprovalBy` provides a simple way to define **who is allowed to approve** within an approval flow.  
It follows the typical fluent Filament-style configuration.

Each `ApprovalBy` represents a specific approval context  
(e.g. `manager`, `employee`, `qa`, etc.).

---

## Create a SimpleApprovalBy

```php
SimpleApprovalBy::make('manager');
```

## Approval Restrictions

All `ApprovalBy` definitions are validated through Laravel's Gate system,
except when using `canApproveUsing()`.

This means:

- If a user passes the Gate (e.g. a Superadmin),
- they are also allowed to approve, depending on your authorization setup.

## Restriction Methods

You can define who is allowed to approve using the following methods:

### Restriction by Permission

Allow approval only if the user has a specific permission.

```php
SimpleApprovalBy::make('manager')
    ->permission('permission_xyz');
```

### Restriction by Role

Allow approval only if the user has a specific role.

```php
SimpleApprovalBy::make('manager')
    ->role('my_role');
```

### Any

Allow any authenticated user to approve.

```php
SimpleApprovalBy::make('employee')
    ->any();
```

### Custom Restriction

Add a fully custom approval check.

> ⚠️ This bypasses the default Gate-based restriction logic.

```php
SimpleApprovalBy::make('manager')
    ->canApproveUsing(
        fn ($approver, $approvable) => $approver->isAdmin()
    );
```

### Labeling

You can add a label to the approval context so that it automatically shows up in the `ApprovalFlow` so you havnt
configure it for each action.

```php
    SimpleApprovalBy::make('key')
        ->label('Manager');
```
