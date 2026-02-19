<?php

namespace Ffhs\Approvals\Traits\Approval;

trait HasLabel
{
    protected null|\Closure|string $label = null;

    public function label(null|\Closure|string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

}
