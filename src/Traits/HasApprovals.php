<?php

namespace Ffhs\Approvals\Traits;

use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApprovals
{
    public function approvals(): MorphMany
    {
        return $this->morphMany(config('approvals.models.approvals', Approval::class), 'approvable');
    }
}
