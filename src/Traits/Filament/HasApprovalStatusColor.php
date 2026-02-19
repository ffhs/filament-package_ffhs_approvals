<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Enums\ApprovalState;
use Filament\Support\Colors\Color;

trait HasApprovalStatusColor
{
    /**  @var array<string, mixed>|Closure|string[] */
    protected array|Closure $approvalStatusColors = [
        'approved' => 'success',
        'denied' => 'danger',
        'pending' => 'info',
        'open' => 'white',
    ];

    /**
     * @param array<string, mixed>|Closure|string[] $statusCategoryColors
     * @return $this
     */
    public function approvalStatusColors(array|Closure $statusCategoryColors): static
    {
        $this->approvalStatusColors = $statusCategoryColors;

        return $this;
    }

    /**
     * @param ApprovalState $approvalState
     * @param mixed|Closure $statusCategoryColors
     * @return $this
     */
    public function approvalStatusColor(
        ApprovalState $approvalState,
        mixed $statusCategoryColors
    ): static {
        $this->approvalStatusColors[$approvalState->value] = $statusCategoryColors;
        return $this;
    }

    public function getApprovalStatusColor(HasApprovalStatuses $actionCase): mixed
    {
        if (in_array($actionCase, $actionCase::getApprovedStatuses(), true)) {
            return $this->getApprovedStatusColor();
        }

        if (in_array($actionCase, $actionCase::getDeniedStatuses(), true)) {
            return $this->getDeniedStatusColor();
        }

        if (in_array($actionCase, $actionCase::getPendingStatuses(), true)) {
            return $this->getPendingStatusColor();
        }

        return $this->getOpenStatusColor();
    }

    public function getApprovedStatusColor(): mixed
    {
        $colorRaw = $this->getApprovalStatusColors()['approved'] ?? 'success';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'success';
    }

    /**
     * @return array<string, mixed>
     */
    public function getApprovalStatusColors(): array
    {
        if (is_array($this->approvalStatusColors)) {
            $this->approvalStatusColors = $this->evaluate($this->approvalStatusColors);
        }

        /**@phpstan-ignore-next-line */
        return $this->approvalStatusColors ?? [];
    }

    public function getDeniedStatusColor(): mixed
    {
        $colorRaw = $this->getApprovalStatusColors()['denied'] ?? 'danger';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'danger';
    }

    public function getPendingStatusColor(): mixed
    {
        $colorRaw = $this->getApprovalStatusColors()['pending'] ?? Color::Blue;

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? Color::Blue;
    }

    public function getOpenStatusColor(): mixed
    {
        $colorRaw = $this->getApprovalStatusColors()['open'] ?? 'gray';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'gray';
    }

}
