---
sidebar_position: 1
---

# Basic: Using ApprovalFlows & ApprovalBys

Approval flows are defined directly on the models that require approvals.  
They define:

- which **approval cases/statuses** exist (via an enum),
- how those cases are mapped/handled by the flow,
- and which **ApprovalBys** (who is allowed to approve) are available.

### Basic Example

```php

class MyModel extends Model implements Approvable
{
    use HasApprovals;

    public function getApprovalFlows(): array
    {
        return [
            'management_approved' => SimpleApprovalFlow::make()
                ->approvalStatus(ApplicationApprovalStatus::class)
                ->approvalBy([
                    SimpleApprovalBy::make('employee')->any(),
                ]),
        ];
    }
}
```
