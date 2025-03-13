<?php

namespace Ffhs\Approvals\Contracts;

use Closure;
use Ffhs\Approvals\Enums\ApprovalState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ApprovalBy
{
    public function canApproveUsing(Closure $canApproveUsing): static;

    public function canApprove(Approver|Model $approver, Approvable $approvable): bool;

    public function approved(Model|Approvable $approvable, string $key): ApprovalState;

    public function getApprovals(Model|Approvable $approvable, $key): Collection;

    public function getName(): string;

    public function getApprovalFlow(Model|Approvable $approvable, string $key): ?ApprovalFlow;
}
