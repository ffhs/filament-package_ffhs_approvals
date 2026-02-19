<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;

trait HasGroupLabels
{
    /**  @var array<string, string|Closure|null>|Closure */
    private array|Closure $groupLabels = [];

    /**
     * @param array<string, string|Closure|null>|Closure $approvalActionsGroupLabel
     * @return $this
     */
    public function groupLabels(array|Closure $approvalActionsGroupLabel): static
    {
        $this->groupLabels = $approvalActionsGroupLabel;

        return $this;
    }

    public function groupLabel(string $key, string|Closure $label): static
    {
        if ($this->groupLabels instanceof Closure) {
            $this->groupLabels = [];
        }
        $this->groupLabels[$key] = $label;
        return $this;
    }

    public function getGroupLabel(string $group): string
    {
        return $this->evaluate($this->getGroupLabels()[$group] ?? $group);
    }

    /**
     * @return array<string, string>
     */
    public function getGroupLabels(): array
    {
        if (!is_array($this->groupLabels)) {
            return $this->groupLabels = $this->evaluate($this->groupLabels);
        }
        return $this->groupLabels;
    }


}
