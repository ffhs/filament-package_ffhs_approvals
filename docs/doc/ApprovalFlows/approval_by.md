---
sidebar_position: 5
---

# ApprovalBy

:::warning
For most use cases, SimpleApprovalBy is enough.
But the interface gives you full extensibility when required.
:::

The `ApprovalBy` interface defines **who can approve** and  
**how approvals are resolved per approval context**.

An `ApprovalBy` is responsible for:

- Determining its own approval result
- Returning related approval records
- Providing its identifier and label
- Resolving its parent `ApprovalFlow`
- Checking permission-based and custom approval logic

---

## Responsibilities of an ApprovalBy

### Approval State Resolution

```php
public function approved(Model|Approvable $approvable, string $key): ApprovalState;
```

This method determines:
> What is the `ApprovalState` for this specific approval-by context?

### Identification & Labeling

```php
public function getName(): string;

public function getLabel(): ?string;
```

- getName() → Returns the internal identifier (e.g. manager, qa)
- getLabel() → Returns a human-readable label (optional)
    - On `null` it falls back to getName()

The label is typically used in UI components such as `ApprovalActions`.

### Final Approval Authorization

```php
public function canApprove(Approver|Model $approver, Approvable $approvable): bool;
```

This is the final approval authorization check.
