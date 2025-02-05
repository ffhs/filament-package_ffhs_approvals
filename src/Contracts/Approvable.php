<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection approvals
 */
interface Approvable
{

    public function approvals(): MorphMany;

    /**
     * @return array<array-key, ApprovalFlow>
     */
    public function getApprovalFlows(): array;

}
