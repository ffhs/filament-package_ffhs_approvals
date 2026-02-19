<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ApprovalBy
{
    public function approved(Model|Approvable $approvable, string $key): ApprovalState;

    /**
     * @param Model|Approvable $approvable
     * @param string $key
     * @return Collection<int, Approval>
     */
    public function getApprovals(Model|Approvable $approvable, string $key): Collection;

    public function getName(): string;

    public function getLabel(): ?string;

    public function getApprovalFlow(Model|Approvable $approvable, string $key): ?ApprovalFlow;

    public function canApproveFromPermissions(Approver|Model $approver): bool;

    public function canApprove(Approver|Model $approver, Approvable $approvable): bool;
}
