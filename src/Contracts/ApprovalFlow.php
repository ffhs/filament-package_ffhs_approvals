<?php

namespace Ffhs\Approvals\Contracts;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Enums\ApprovalState;
use Illuminate\Database\Eloquent\Model;


interface ApprovalFlow
{
    public function disabled(bool|Closure $approvalDisabled): static;

    public function approvalBy(array|Closure $approvalBy): static;

    public function category(string|Closure $category): static;

    public function getCategory(): string;

    public function approvalStatus(array|Closure $approvalStatus): static;
    
    /**
     * @return null|class-string<HasApprovalStatuses>
     */
    public function getStatusEnumClass(): ?string;

    /**
     * @return array<BackedEnum|HasApprovalStatuses>
     */
    public function getApprovalStatus(): array;

    public function approved(Model|Approvable $approvable, string $key): ApprovalState;

    public function isDisabled(): bool;

    /**
     * @return array<ApprovalBy>
     */
    public function getApprovalBys(): array;
}
