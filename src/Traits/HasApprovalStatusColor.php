<?php

namespace Ffhs\Approvals\Traits;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Enums\ApprovalState;

trait HasApprovalStatusColor
{
    private array|Closure $approvalStatusColors = [
        'approved' => 'success',
        'denied' => 'danger',
        'pending' => 'info',
        'open' => 'white',
    ];

    public function approvalStatusColors(array|Closure $statusCategoryColors): static
    {
        $this->approvalStatusColors = $statusCategoryColors;

        return $this;
    }

    public function approvalStatusColor(
        BackedEnum|ApprovalState $approvalState,
        array|Closure $statusCategoryColors
    ): static {
        if ($this->approvalStatusColors instanceof Closure) {
            $this->approvalStatusColors = [];
        }
        if (!is_string($approvalState)) {
            $approvalState = $approvalState->value;
        }

        $this->approvalStatusColors[$approvalState] = $statusCategoryColors;

        return $this;
    }

    public function getApprovalStatusColors(): array
    {
        if (is_array($this->approvalStatusColors)) {
            $this->approvalStatusColors = $this->evaluate($this->approvalStatusColors);
        }

        return $this->approvalStatusColors;
    }


    public function getApprovalStatusColor(BackedEnum|string $approvalCase, ApprovalFlow $flow): mixed
    {
        if (!is_string($approvalCase)) {
            $approvalCase = $approvalCase->value;
        }

        /** @var HasApprovalStatuses $statusEnum */
        $statusEnum = $flow->getStatusEnumClass();
        if (in_array($approvalCase, $statusEnum::getApprovedStatuses(), true)) {
            return $this->getApprovedStatusColor();
        }

        if (in_array($approvalCase, $statusEnum::getDeniedStatuses(), true)) {
            return $this->getDeniedStatusColor();
        }

        if (in_array($approvalCase, $statusEnum::getPendingStatuses(), true)) {
            return $this->getPendingStatusColor();
        }

        return $this->getOpenStatusColor();
    }

    public function getApprovedStatusColor(): mixed
    {
        return $this->statusCategoryColors['approved'] ?? 'green';
    }

    public function getDeniedStatusColor(): mixed
    {
        return $this->statusCategoryColors['denied'] ?? 'red';
    }

    public function getPendingStatusColor(): mixed
    {
        return $this->statusCategoryColors['pending'] ?? 'blue';
    }

    public function getOpenStatusColor(): mixed
    {
        return $this->statusCategoryColors['open'] ?? 'white';
    }

}
