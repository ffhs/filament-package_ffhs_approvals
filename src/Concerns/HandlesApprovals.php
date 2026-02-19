<?php

namespace Ffhs\Approvals\Concerns;

use App\Models\User;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Filament\HasApprovalKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\RuntimeException;

trait HandlesApprovals
{
    use HasApprovalKey;


    /** @var Collection<int, Approval>|null */
    private ?Collection $cachedApprovals = null;
    private ?ApprovalFlow $approvalFlow = null;
    private ApprovalBy $approvalBy;


    public function canApprove(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->getApprovalBy()->canApprove($user, $this->approvable());
    }

    public function getApprovalBy(): ApprovalBy
    {
        return $this->approvalBy;
    }

    public function approvable(): Approvable|Model
    {
        return $this->getRecord();
    }

    public function approvalFlow(ApprovalFlow $approvalFlow): static
    {
        $this->approvalFlow = $approvalFlow;

        return $this;
    }

    public function approvalBy(ApprovalBy $approvalBy): static
    {
        $this->approvalBy = $approvalBy;

        return $this;
    }

    public function hasCurrentApprovalStatus(): bool
    {
        return $this->getBoundApprovals()->count() > 0;
    }

    public function getBoundApprovals(): Collection
    {
        if (!$this->cachedApprovals) {
            $this->cachedApprovals = $this->getApprovalBy()
                ->getApprovals(
                    $this->approvable(),
                    $this->getApprovalKey()
                );
        }

        return $this->cachedApprovals;
    }

    public function getApprovalFlow(): ApprovalFlow
    {
        if (!$this->approvalFlow) {
            throw new RuntimeException('No approval flow was found for component'); //todo find right exception
        }

        return $this->approvalFlow;
    }
}
