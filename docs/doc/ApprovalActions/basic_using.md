---
sidebar_position: 1
---

# Basic Usage

The **Approval Actions** component allows you to connect Filament resources to the approval system.

Each `ApprovalActions` component is always bound to a specific approval flow.  
The flow key is defined when creating the component and can later be modified using:

```php
approvalKey(string|Closure $key)
```

At all places where closures can be passed, the default Filament injections are available.

```php
ApprovalActions::make('approval_flow_key')
```

ApprovalActions is a component

:::info[ApprovalActions is a component]
`ApprovalActions` is a component, not a Filament Action class.
Therefore, it cannot be used in places where only Filament Action classes are allowed.
:::

## needResetApprovalBeforeChange

```php
public function needResetApprovalBeforeChange(Closure|bool $needResetApprovalBeforeChange = true): static
```

Adds a reset button that must be triggered before the approval status can be changed.

This ensures that the approval state is first reset to null instead of directly switching to the next status.

#### Example Code

```php
ApprovalActions::make('approval_flow_key')
    ->needResetApprovalBeforeChange();
```

#### Example UI

![ApprovalResetAction](img/reset_action.png)

## recordUsing

By default, the action operates on the current record it has access to
(usually the record of the parent schema).

If you need to customize the record being used, you can override it with:

```php
public function recordUsing(Closure|null|Model|Approvable $record): static
```

#### Example

```php
ApprovalActions::make('approval_flow_key')
    ->recordUsing(fn ($record) => $record);
```
