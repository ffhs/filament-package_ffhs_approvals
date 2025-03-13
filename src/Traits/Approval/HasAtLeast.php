<?php

namespace Ffhs\Approvals\Traits\Approval;

use Closure;

trait HasAtLeast
{

    protected int|Closure $atLeast = 1;

    public function atLeast(int|Closure $atLeast): static
    {
        $this->atLeast = $atLeast;

        return $this;
    }


    public function getAtLeast(): int
    {
        return $this->evaluate($this->atLeast);
    }

}
