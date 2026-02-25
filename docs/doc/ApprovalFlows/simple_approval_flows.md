---
sidebar_position: 2
---

# Use SimpleApprovalFlows

`SimpleApprovalFlow` provides an easy way to define approval flows  
without creating a dedicated class.  
It follows the typical fluent Filament-style configuration.

---

## Create a SimpleApprovalFlow

```php
SimpleApprovalFlow::make()
    ->approvalStatus(SGApprovalStatus::cases())
```

### approvalStatus()

Defines the available approval cases.

You can pass:

- An enum class (like `GApprovalStatus::class`)
- Or an array of enum cases (like `SGApprovalStatus::cases()`)

## Add A Category

The category key is used to group approval flows during the approval process
(e.g. when filtering flows by category).

> This field is optional.

See **Working with Approvals** for more details about filtering by categories.

```php
SimpleApprovalFlow::make()
    ->category('category_key')
```

## DisableFlow

You can disable a flow by calling:

```php
SimpleApprovalFlow::make()
    ->disable();
```

A disabled flow will not participate in the approval process.

## Define ApprovalBys

You can assign multiple `ApprovalBy` definitions using `approvalBy()`.

```php
SimpleApprovalFlow::make()
    ->approvalStatus(SGApprovalStatus::class)
    ->approvalBy([
        MyApprovalBy::make('manager'),
        SimpleApprovalBy::make('employee')->any(),
    ]);
```
