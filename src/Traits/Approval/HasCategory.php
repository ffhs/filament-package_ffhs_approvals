<?php

namespace Ffhs\Approvals\Traits\Approval;

use BackedEnum;
use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;

trait HasCategory
{
    protected string|Closure $category;

    public function category(string|Closure $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->evaluate($this->category);
    }

}
