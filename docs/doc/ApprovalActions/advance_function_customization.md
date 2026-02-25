---
sidebar_position: 3
---

# Advanced: Action Customization

You can customize the underlying Filament actions used by `ApprovalActions`.  
This is useful if you want to change labels, icons, colors, confirmation dialogs, visibility/disabled state, or add
extra logic.

> All callbacks receive the already prepared action instance, you just modify it and return it, but you can still use
> the filament injections

## Reset Action

The **reset action** is the button that resets the current approval for the selected flow (and approval-by, if
applicable).

Use `modifyResetApprovalActionUsing()` to customize it:

```php
ApprovalActions::make('key')
    ->modifyResetApprovalActionUsing(
        fn (ApprovalByResetAction $action) => $action
            // examples
            ->label('Reset approval')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->requiresConfirmation()
    );

```

## Case Actions (Single State Action)

Each case button (e.g. Approved / Denied / Pending / Open or your custom cases) is rendered as an action.
You can customize these actions using modifyApprovalSingleStateAction().

```php
ApprovalActions::make('key')
->modifyApprovalSingleStateAction(
        fn (
            ApprovalSingleStateAction $action,
            ApprovalBy $approvalBy,
            HasApprovalStatuses $state
        ) => $action
            // examples
            ->tooltip("Approval by: {$approvalBy->key}")
            ->requiresConfirmation()
    );
```

#### Parameters explained

- `ApprovalSingleStateAction $action`
  The Filament action instance for the current case button.
- `ApprovalBy $approvalBy`
  The current approval-by context (e.g. manager, owner, qa).
- `HasApprovalStatuses $state`
  The current approval state container / model context for this approval-by (useful if you want to check what’s
  currently set).
