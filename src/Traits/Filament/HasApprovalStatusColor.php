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
    public function approvalStateColor(
        ApprovalState $approvalState,
        mixed $statusCategoryColors
    ): static {
        $this->approvalStatusColors[$approvalState->value] = $statusCategoryColors;
        return $this;
    }

    public function getApprovalStateColor(HasApprovalStatuses $actionCase): mixed
    {
        if (in_array($actionCase, $actionCase::getApprovedStatuses(), true)) {
            return $this->getApprovedStateColor();
        }

        if (in_array($actionCase, $actionCase::getDeniedStatuses(), true)) {
            return $this->getDeniedStateColor();
        }

        if (in_array($actionCase, $actionCase::getPendingStatuses(), true)) {
            return $this->getPendingStateColor();
        }

        return $this->getOpenStateColor();
    }

    public function getApprovedStateColor(): mixed
    {
        $colorRaw = $this->getApprovalStateColors()['approved'] ?? 'success';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'success';
    }

    /**
     * @return array<string, mixed>
     */
    public function getApprovalStateColors(): array
    {
        if (is_array($this->approvalStatusColors)) {
            $this->approvalStatusColors = $this->evaluate($this->approvalStatusColors);
        }

        /**@phpstan-ignore-next-line */
        return $this->approvalStatusColors ?? [];
    }

    public function getDeniedStateColor(): mixed
    {
        $colorRaw = $this->getApprovalStateColors()['denied'] ?? 'danger';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'danger';
    }

    public function getPendingStateColor(): mixed
    {
        $colorRaw = $this->getApprovalStateColors()['pending'] ?? Color::Blue;

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? Color::Blue;
    }

    public function getOpenStateColor(): mixed
    {
        $colorRaw = $this->getApprovalStateColors()['open'] ?? 'gray';

        /**@phpstan-ignore-next-line */
        return $this->evaluate($colorRaw) ?? 'gray';
    }

}
