<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;


trait HasCasesIcons
{
    protected ?Closure $caseIconUsing = null;

    /** @var array<string, string|null|BackedEnum|Closure> */
    private array|Closure $casesIcons = [];

    public function getCaseIcon(HasApprovalStatuses $actionCase): string|null
    {
        if (is_null($this->caseIconUsing)) {
            return $this->getCaseIconDefault($actionCase);
        }

        return $this->resolveIconValue(
            $this->evaluate($this->caseIconUsing, ['approvalCase' => $actionCase])
        );
    }

    public function getCaseIconDefault(HasApprovalStatuses $actionCase): string|null
    {
        $caseIcon = $this->evaluate($this->casesIcons)[$actionCase->value] ?? null;

        return $this->resolveIconValue(
            $this->evaluate($caseIcon, ['approvalCase' => $actionCase])
        );
    }

    /**
     * Resolve a BackedEnum icon (e.g. Heroicon::OutlinedCheck) to its string value.
     */
    private function resolveIconValue(mixed $icon): string|null
    {
        if ($icon instanceof BackedEnum) {
            return $icon->value;
        }

        return $icon;
    }

    public function caseIconUsing(?Closure $caseIconUsing): static
    {
        $this->caseIconUsing = $caseIconUsing;
        return $this;
    }

    /**
     * @param array<string, string|null|BackedEnum|Closure> $caseIcons
     * @return $this
     */
    public function casesIcons(array|Closure $caseIcons): static
    {
        $this->casesIcons = $caseIcons;
        return $this;
    }

    public function caseIcon(
        HasApprovalStatuses $actionCase,
        string|null|BackedEnum|Closure $caseIcon
    ): static {
        if (!is_null($this->caseIconUsing)) {
            $this->caseIconUsing = null;
        }

        $this->casesIcons[$actionCase->value] = $caseIcon;

        return $this;
    }
}
