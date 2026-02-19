<?php

namespace Ffhs\Approvals\Contracts;

use Closure;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ApprovableByComponent
{
    public function approvable(): Approvable|Model;

    public function approvalBy(ApprovalBy $approvalBy): static;

    public function getApprovalBy(): ApprovalBy;

    public function approvalKey(string|Closure $approvalKey): static;

    public function getApprovalKey(): string;

    public function canApprove(): bool;

    /**
     * @return Collection<int, Approval>
     */
    public function getBoundApprovals(): Collection;
}
