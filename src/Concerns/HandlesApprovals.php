<?php

namespace Ffhs\Approvals\Concerns;

use App\Models\User;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\HasApprovalKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\RuntimeException;

trait HandlesApprovals
{
    use HasApprovalKey;

    private ?ApprovalFlow $approvalFlow = null;
    private ?Collection $cachedApprovals = null;
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

    public function getBoundApprovals(): ?Collection
    {
        if ($this->cachedApprovals) {
            return $this->cachedApprovals;
        }

        $this->cachedApprovals = $this
            ->approvable()
            ->approvals
            ->where(fn(Approval $approval) => $approval->key == $this->getApprovalKey()
                && $approval->approval_by == $this->getApprovalBy()->getName()
            );

        return $this->cachedApprovals;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'approvals' => $this->getBoundApprovals(),
            'approvalFlow' => $this->getApprovalFlow(),
            'approvalBy' => $this->getApprovalBy(),
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    public function getApprovalFlow(): ?ApprovalFlow
    {
        if ($this->approvalFlow) {
            return $this->approvalFlow;
        }

        throw new RuntimeException('No approval flow was found for component'); //todo find right exeption
    }
}
