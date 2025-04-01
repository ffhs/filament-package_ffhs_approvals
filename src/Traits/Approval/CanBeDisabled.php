<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;

trait CanBeDisabled
{
    protected bool|Closure $disabled = false;

    public function disabled(bool|Closure $disabled): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->disabled);
    }
}
