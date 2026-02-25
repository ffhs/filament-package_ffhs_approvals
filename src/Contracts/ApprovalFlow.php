<?php

namespace Ffhs\Approvals\Contracts;

use Closure;
use Ffhs\Approvals\Enums\ApprovalState;
use Illuminate\Database\Eloquent\Model;


interface ApprovalFlow
{
    public function disabled(bool|Closure $approvalDisabled): static;


    public function getCategory(): string;

    /**
     * @return null|class-string<HasApprovalStatuses>
     */
    public function getStatusEnumClass(): ?string;

    /**
     * @return HasApprovalStatuses[]
     */
    public function getApprovalStatus(): array;

    public function approved(Model|Approvable $approvable, string $key): ApprovalState;

    public function isDisabled(): bool;

    /**
     * @return array<ApprovalBy>
     */
    public function getApprovalBys(): array;
}
