---
sidebar_position: 7
---

# HasApprovalStatuses (Approval Cases)

::::note
This will be refactored in an future release
::::

It allows the approval system to:

- Determine which Enum cases count as **approved**, **denied** or **pending**
- Retrieve a **human-readable label** for each case

Every approval status Enum must implement this interface.

---

## Creating an Approval Status Enum

Create a PHP Enum that implements the HasApprovalStatuses contract.
This Enum defines all possible approval states and maps them to approved, denied, or pending groups.

In addition, you can use the getCaseLabel() method to return a human-readable label for each case.
This is especially useful when working with translations (e.g. Laravel’s __() helper).

### Example

```php
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

enum MyApprovalStatus: string implements HasApprovalStatuses
{
    case APPROVED = 'approved';
    case INCOMPLETE = 'incomplete';
    case DENIED = 'denied';

    /**
     * Returns all statuses that are considered approved.
     */
    public static function getApprovedStatuses(): array
    {
        return [self::APPROVED];
    }

    /**
     * Returns all statuses that are considered denied.
     */
    public static function getDeniedStatuses(): array
    {
        return [self::DENIED];
    }

    /**
     * Returns all statuses that are considered pending.
     */
    public static function getPendingStatuses(): array
    {
        return [self::INCOMPLETE];
    }

    /**
     * Returns a human-readable label for the given enum case.
     */
    public static function getCaseLabel(self $case): string
    {
        return __('approvals.status.' . $case->value);
    }
}
```
