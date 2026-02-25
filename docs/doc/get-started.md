---
sidebar_position: 3
---

# Get Started

## 1. Define Approval Status Enum

Create a PHP Enum that implements the `HasApprovalStatuses` contract.  
This Enum defines all possible approval states and maps them to approved, denied, or pending groups.

In addition, you can use the `getCaseLabel()` method to return a human-readable label for each case.  
This is especially useful when working with translations (e.g. Laravel `__()` helper).

#### Example:

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
     *
     * This method can be used for translations.
     * Example translation key:
     * approvals.status.approved
     */
    public static function getCaseLabel(self $case): string
    {
        return __('approvals.status.' . $case->value);
    }
}
```

<br/>
---

## 2. Define an Approval Flow in the Model

To enable approvals for a model, implement the `Approvable` interface and add the `HasApprovals` trait.  
Then define one or more approval flows inside `getApprovalFlows()`.

Each flow consists of:

- **A flow key** (e.g. `management_approved`) used to reference the flow
- **Allowed approval statuses** (usually your Enum)
- **Approval rules** (`approvalBy`) defining *who* may approve and *how many* approvals are required
- **A category** to group flows in the evaluation. **This is optional.**

#### Example:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Traits\HasApprovals;
use Ffhs\Approvals\Flows\SimpleApprovalFlow;
use Ffhs\Approvals\Flows\SimpleApprovalBy;

class MyModel extends Model implements Approvable
{
    use HasApprovals;

    public function getApprovalFlows(): array
    {
        return [
            'management_approved' => SimpleApprovalFlow::make()
                ->approvalStatus(ApplicationApprovalStatus::class)
                ->category('any_category')
                ->approvalBy([
                    // Any employee is allowed to approve
                    SimpleApprovalBy::make('employee')->any(),

                    // Managers need a specific permission to approve
                    SimpleApprovalBy::make('manager')
                        ->permission('can_approve_for_manager'),

                    // HR approvers must have a role and at least 2 approvals are required
                    SimpleApprovalBy::make('hr')
                        ->role('hr_role')
                        ->atLeast(2),
                ]),
        ];
    }
}
```

#### Notes

- approvalStatus(...) should usually reference your enum class (e.g. MyApprovalStatus::class()).

- approvalBy([...]) defines approval steps/groups. Depending on your package logic, these can act as alternative
  approver
  groups or sequential rules (check your package behavior). You can find more details under the Approvelflows tab.

- The flow key (management_approved) should be unique per model and descriptive, because you will likely use it in code,
  UI components, or for the evaluation.

<br/>

---

## 3. Using Approval Actions in Filament

You can render approval actions inside your Filament resource page or custom view by using the `ApprovalActions`
component.

> **Important:**  
> `ApprovalActions` is a **Filament component**, not a Filament Action.  
> Therefore, it **cannot** be used in places where only Actions are allowed (e.g. inside `->actions([...])`).  
> Instead, use it within your page layout, form schema, infolist, or custom view where components are supported.
> The string passed to make() must match the approval flow key defined in your model (e.g. 'management_approved' in
> getApprovalFlows()).

All available configuration options can be found in the **`ApprovalActions`** tab.

#### Example

```php
// MyModelView.php

use Ffhs\Approvals\Filament\Components\ApprovalActions;

ApprovalActions::make('management_approved');

```

This will render the approval controls for the specified flow directly in your Filament schema.

<br/>
