---
sidebar_position: 4
---

# Approval Flows

::::warning
For most use cases, SimpleApprovalFlow is enough.
But the interface gives you full extensibility when required.
::::

The `ApprovalFlow` interface provides full control over how approvals behave in your system.

While `SimpleApprovalFlow` covers most common use cases, implementing the `ApprovalFlow` interface yourself allows you
to build **highly customized approval logic**.

An approval flow is responsible for:

- Managing its **disabled state**
- Defining available **approval cases (statuses)**
- Defining the available **ApprovalBys**
- Determining when a record is considered `approved`
- Assigning the flow to a **category**
- Returning the aggregated `ApprovalState`

---

## Responsibilities of an ApprovalFlow

### Disabled State

A flow can be disabled statically or dynamically (via `Closure`).

If a flow is disabled:

- It does not participate in approval evaluation
- It does not affect aggregated approval results

```php
public function disabled(bool|Closure $approvalDisabled): static;
public function isDisabled(): bool;
```

### Category

Flows can be grouped using categories.
This is especially useful when filtering flows during approval checks.

````php
public function getCategory(): string;
````

### Approval Status Definitions (Cases)

The flow defines which approval cases (statuses) exist.

```php
/**
 * @return null|class-string<HasApprovalStatuses>
 */
public function getStatusEnumClass(): ?string;

/**
 * @return HasApprovalStatuses[]
 */
public function getApprovalStatus(): array;

```

### ApprovalBys

The flow defines who is allowed to approve.

```php
/**
 * @return array<ApprovalBy>
 */
public function getApprovalBys(): array;
```

### Approval Resolution Logic

This is the most important part of a custom flow.

```php
public function approved(Model|Approvable $approvable, string $key): ApprovalState;
```

This method determines:
> What is the final ApprovalState of this flow for the given model?

You have complete control here.

For example:

- You may define that all ApprovalBys must approve
- Or that at least one approval is enough
- Or that OPEN should already count as APPROVED
- Or create completely custom state aggregation logic
