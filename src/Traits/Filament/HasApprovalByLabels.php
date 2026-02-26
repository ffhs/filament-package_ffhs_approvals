<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;

trait HasApprovalByLabels
{
    /**  @var array<string, string|Closure|null>|Closure */
    protected array|Closure $approvalByLabels = [];

    /**
     * @param array<string, string|Closure|null>|Closure $approvalByLabels
     * @return $this
     */
    public function approvalByLabels(array|Closure $approvalByLabels): static
    {
        $this->approvalByLabels = $approvalByLabels;

        return $this;
    }

    public function approvalByLabel(string $key, string|Closure $label): static
    {
        if ($this->approvalByLabels instanceof Closure) {
            $this->approvalByLabels = [];
        }

        $this->approvalByLabels[$key] = $label;
        return $this;
    }

    public function getApprovalByLabel(ApprovalBy $approvalBy): string
    {
        $evaluation = $this->evaluate($this->getGroupLabels()[$approvalBy->getName()] ?? null);

        if (!is_null($evaluation)) {
            return $evaluation;
        }

        $label = $approvalBy->getLabel();
        return is_null($label) ? $approvalBy->getName() : $label;
    }

    /**
     * @return array<string, string>
     */
    public function getGroupLabels(): array
    {
        return $this->evaluate($this->approvalByLabels);
    }


}
