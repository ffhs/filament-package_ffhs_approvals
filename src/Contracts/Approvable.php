<?php

namespace Ffhs\Approvals\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection approvals
 */
interface Approvable
{

    public function approvals(): MorphMany;

    public function getApprovalFlows(): array;

}
