<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Approval\ApprovalBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ApprovableByComponent
{
    public function approvable(): Approvable|Model;

    public function approvalBy(ApprovalBy $approvalBy): static;

    public function getApprovalBy(): ApprovalBy;

    public function approvalKey(string|\Closure $approvalKey): static;

    public function getApprovalKey(): string;

    public function canApprove(): bool;

    public function getBoundApprovals(): ?Collection;
}
