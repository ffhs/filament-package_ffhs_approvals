<?php

namespace Ffhs\Approvals\Approval;

use Closure;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Enums\ApprovalState;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SimpleApprovalFlow implements ApprovalFlow
{
    use EvaluatesClosures;

    private bool|Closure $approvalDisabled = false;
    private array|Closure $approvalBy = [];
    private string|Closure $category;
    private array|Closure $approvalStatus;

    public static function make(): static
    {
        $approvalFlow = app(static::class);
        $approvalFlow->setUp();

        return $approvalFlow;
    }

    protected function setUp()
    {
    }

    public function approvalDisabled(bool|Closure $approvalDisabled): static
    {
        $this->approvalDisabled = $approvalDisabled;

        return $this;
    }

    public function approvalBy(array|Closure $approvalBy): static
    {
        $this->approvalBy = $approvalBy;

        return $this;
    }

    public function category(string|Closure $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->evaluate($this->category);
    }

    public function approvalStatus(array|Closure $approvalStatus): static
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    public function getStatusEnumClass(): ?string
    {
        if (empty($this->getApprovalStatus())) {
            return null;
        }

        return $this->getApprovalStatus()[0]::class;
    }

    /**
     * @return array<UnitEnum|HasApprovalStatuses>
     */
    public function getApprovalStatus(): array
    {
        return $this->evaluate($this->approvalStatus);
    }

    public function approved(Model|Approvable $approvable, string $key): ApprovalState
    {
        if ($this->isApprovalDisabled()) {
            return ApprovalState::APPROVED;
        }

        $isPending = false;
        $isOpen = false;

        foreach ($this->getApprovalBys() as $approvalBy) {
            $approved = $approvalBy->approved($approvable, $key);

            if ($approved == ApprovalState::PENDING) {
                $isPending = true;
            } elseif ($approved == ApprovalState::OPEN) {
                $isOpen = true;
            } elseif ($approved == ApprovalState::DENIED) {
                return ApprovalState::DENIED;
            }
        }

        if ($isPending) {
            return ApprovalState::PENDING;
        } elseif ($isOpen) {
            return ApprovalState::OPEN;
        }

        return ApprovalState::APPROVED;
    }

    public function isApprovalDisabled(): bool
    {
        return $this->evaluate($this->approvalDisabled);
    }

    /**
     * @return array<ApprovalBy>
     */
    public function getApprovalBys(): array
    {
        return $this->evaluate($this->approvalBy);
    }
}
