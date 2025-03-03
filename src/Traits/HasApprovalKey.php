<?php

namespace Ffhs\Approvals\Traits;

use Closure;

trait HasApprovalKey
{

    private string|Closure $approvalKey;


    public function approvalKey(string|Closure  $approvalKey):static
    {
        $this->approvalKey = $approvalKey;
        return $this;
    }

    public function getApprovalKey():string
    {
        return $this->evaluate($this->approvalKey);
    }

}
