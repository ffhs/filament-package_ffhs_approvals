<?php

namespace Ffhs\Approvals\Contracts;

use Closure;
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

    public function getBoundApprovals(): Collection;
}
