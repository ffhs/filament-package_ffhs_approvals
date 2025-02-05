<?php

namespace Ffhs\Approvals\Concerns;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

trait HandlesApprovals
{
    protected ?ApprovalFlow $approvalFlow = null;
    protected ?Collection $cachedApprovals = null;



    public function canApprove(): bool
    {
       return $this->getApprovalBy()->canApprove(Auth::user(), $this->approvable());
    }


    public function approvalFlow(ApprovalFlow $approvalFlow): static
    {
        $this->approvalFlow = $approvalFlow;

        return $this;
    }

    public function getApprovalFlow(): ?ApprovalFlow
    {
        return $this->approvalFlow;
    }




    public function getBoundApprovals(): ?Collection
    {
        if($this->cachedApprovals) return $this->cachedApprovals;
        return $this->cachedApprovals = $this->approvable()->approvals
            ->where(fn (Approval $approval) =>
                    $approval->key == $this->getApprovalKey() &&
                    $approval->approval_by == $this->getApprovalBy()->getName()
            );
    }

    public function approvable(): Approvable|Model
    {
        return $this->getRecord();
    }
    public function approvalBy(ApprovalBy $approvalBy):static
    {
        $this->approvalBy = $approvalBy;
        return $this;
    }
    public function getApprovalBy(): ApprovalBy
    {
        return $this->approvalBy;
    }


    public function approvalKey(string|\Closure $approvalKey): static
    {
        $this->approvalKey = $approvalKey;
        return $this;
    }

    public function getApprovalKey(): string
    {
        return $this->evaluate($this->approvalKey);
    }

    protected function hasCurrentApprovalStatus(): bool
    {
        return $this->getBoundApprovals()->count() > 0;
    }










    // -----------------------------------------------





//    public function process() //:Closure
//    {
//        return function (): void {
//
//            if ($this->isPending()) {
//
//                if (PendingApproval::userHasResponded($this->getRecord(), $this->category)) {
//                    $this->removePendingApproval();
//
//                    return;
//                }
//
//                $this->createPendingApproval();
//
//                return;
//            }
//
//            if (! $this->approvalActionTaken()) {
//                $this->createDefaultApprovals();
//            }
//
//            if (Approval::userHasResponded($this->getRecord(), $this->category) && $this->actionHasCurrentApprovalStatus()) {
//                $this->resetApproval();
//
//                return;
//            }
//
//            $this->setApproval();
//
//        };
//    }



//    public function setApproval(): void
//    {
//        $record = $this->getRecord();
//        $recordName = strtolower(class_basename($record));
//        assert($this->status instanceof BackedEnum);
//
//        $nextApproval = Approval::where([
//            'category' => $this->category ?? "approvals-$recordName",
//            'approvable_type' => get_class($record),
//            'approvable_id' => $record->id ?? null,
//            'approver_id' => null,
//        ])
//            ->whereJsonContains('config->order', $this->getConfig()['order'])
//            ->whereJsonContains('config->access', $this->getConfig()['access'])
//            ->first();
//
//        $nextApproval->update([
//            'approver_id' => auth()->id(),
//            'status' => $this->status->value,
//            'status_at' => now(),
//        ]);
//
//        Notification::make()
//            ->title("Successfully marked as $this->name!")
//            ->success()
//            ->send();
//    }

//    public function resetApproval(): void
//    {
//        $record = $this->getRecord();
//        $recordName = strtolower(class_basename($record));
//        assert($this->status instanceof BackedEnum);
//
//        $approval = Approval::where([
//            'category' => $this->category ?? "approvals-$recordName",
//            'approvable_type' => get_class($record),
//            'approvable_id' => $record->id ?? null,
//            'status' => $this->status->value,
//            'approver_id' => auth()->id(),
//        ])->first();
//
//        Approval::create([
//            'category' => $approval->category,
//            'approvable_type' => $approval->approvable_type,
//            'approvable_id' => $approval->approvable_id,
//            'config' => $approval->config,
//        ]);
//
//        $approval->delete();
//
//        Notification::make()
//            ->title("Successfully unmarked as $this->name!")
//            ->success()
//            ->send();
//    }
//
//    public function createPendingApproval(): void
//    {
//        $record = $this->getRecord();
//        $recordName = strtolower(class_basename($record));
//        assert($this->status instanceof BackedEnum);
//
//        PendingApproval::create([
//            'category' => $this->category ?? "approvals-$recordName",
//            'approvable_type' => get_class($record),
//            'approvable_id' => $record->id ?? null,
//            'status' => $this->status->value,
//            'status_at' => now(),
//            'approver_id' => auth()->id(),
//            'config' => json_encode($this->getConfig()),
//        ]);
//
//        Notification::make()
//            ->title("Successfully marked as $this->name!")
//            ->success()
//            ->send();
//    }
//
//    public function removePendingApproval(): void
//    {
//        $record = $this->getRecord();
//        $recordName = strtolower(class_basename($record));
//        assert($this->status instanceof BackedEnum);
//
//        PendingApproval::where([
//            'category' => $this->category ?? "approvals-$recordName",
//            'approvable_type' => get_class($record),
//            'approvable_id' => $record->id ?? null,
//            'approver_id' => auth()->id(),
//            'status' => $this->status->value,
//        ])->delete();
//
//        Notification::make()
//            ->title("Successfully unmarked as $this->name!")
//            ->success()
//            ->send();
//    }

//    public function getConfig(): array
//    {
//        if (! $this->approvalFlow) {
//            return $this->defaultConfig;
//        }
//
//        if (! $this->approvalFlow->isChained()) {
//            return $this->approvalFlow->bestMatchStep($this->getRecord(), $this->getCategory(), $this->getStatusEnumClass())['config'];
//        }
//
//        $currentStep = $this->approvalFlow->currentStep($this->getRecord(), $this->getCategory(), $this->getStatusEnumClass());
//
//        if (! $currentStep) {
//            return $this->defaultConfig;
//        }
//
//        return $currentStep['config'];
//    }



    protected function getApprovedStatusColor(): string
    {
        return $this->statusCategoryColors['approved'];
    }

    protected function getDeclinedStatusColor(): string
    {
        return $this->statusCategoryColors['declined'];
    }

    protected function getPendingStatusColor(): string
    {
        return $this->statusCategoryColors['pending'];
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
//    public function getColor(): string | array | null
//    {
//        if (! $this->actionHasCurrentApprovalStatus()) {
//            return 'gray';
//        }
//
//        $statusEnum = $this->getStatusEnumClass();
//
//        if (in_array($this->status, $statusEnum::getApprovedStatuses(), true)) {
//            return $this->getApprovedStatusColor();
//        }
//
//        if (in_array($this->status, $statusEnum::getDeclinedStatuses(), true)) {
//            return $this->getDeclinedStatusColor();
//        }
//
//        if (in_array($this->status, $statusEnum::getPendingStatuses(), true)) {
//            return $this->getPendingStatusColor();
//        }
//
//        return parent::getColor();
//    }

    public function statusCategoryColors(array $colors): static
    {
        $allowedKeys = ['approved', 'declined', 'pending'];
        $validColors = array_keys(FilamentColor::getColors());

        foreach ($colors as $key => $value) {
            if (! in_array($key, $allowedKeys, true)) {
                throw new InvalidArgumentException("Invalid status key: {$key}. Allowed keys are: " . implode(', ', $allowedKeys));
            }

            if (! in_array($value, $validColors, true)) {
                throw new InvalidArgumentException("Invalid color value for '{$key}': {$value}. Allowed colors are: " . implode(', ', $validColors));
            }

            $this->statusCategoryColors[$key] = $value;
        }

        return $this;
    }



//    protected function approvalActionTaken(): bool
//    {
//        return Approval::where('category', $this->category)
//            ->where('approvable_type', get_class($this->getRecord()))
//            ->where('approvable_id', $this->getRecord()->getKey())
//            ->exists();
//    }



//    public function isHidden(): bool
//    {
//
//        if (parent::isHidden()) {
//            return true;
//        }
//
////        $hasResponded = PendingApproval::userHasResponded($this->getRecord(), $this->category) || Approval::userHasResponded($this->getRecord(), $this->category); //ToDo
////
////        if (! $this->actionHasCurrentApprovalStatus() && $hasResponded) {
////            return true;
////        }
//
//        return false;
//    }
//
//    public function isPending(): bool
//    {
//        return false; //ToDo
//        $pendingStatuses = $this->status::getPendingStatuses();
//
//        return in_array($this->status, $pendingStatuses, true);
//    }
}
