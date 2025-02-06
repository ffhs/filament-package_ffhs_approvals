<?php

namespace Ffhs\Approvals\Concerns;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\HasApprovalKey;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\RuntimeException;

trait HandlesApprovals
{
    use HasApprovalKey;

    private ?ApprovalFlow $approvalFlow = null;
    private ?Collection $cachedApprovals = null;
    private ApprovalBy $approvalBy;



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
        if($this->approvalFlow) return $this->approvalFlow;
        throw new RuntimeException('No approval flow was found for component'); //todo find right exeption
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
    protected function hasCurrentApprovalStatus(): bool
    {
        return $this->getBoundApprovals()->count() > 0;
    }




    // -----------------------------------------------




//        Notification::make()
//            ->title("Successfully marked as $this->name!")
//            ->success()
//            ->send();
//        Notification::make()
//            ->title("Successfully unmarked as $this->name!")
//            ->success()
//            ->send();
//        Notification::make()
//            ->title("Successfully marked as $this->name!")
//            ->success()
//            ->send();

//        Notification::make()
//            ->title("Successfully unmarked as $this->name!")
//            ->success()
//            ->send();




    protected function getApprovedStatusColor(): string
    {
        return $this->statusCategoryColors['approved'] ?? 'green';
    }

    protected function getDeniedStatusColor(): string
    {
        return $this->statusCategoryColors['denied'] ?? 'red';
    }

    protected function getPendingStatusColor(): string
    {
        return $this->statusCategoryColors['pending'] ?? 'blue';
    }


    public function statusCategoryColors(array $colors): static
    {
        $allowedKeys = ['approved', 'denied', 'pending'];
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


}
