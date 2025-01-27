<?php

namespace Ffhs\Approvals\Traits;

use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Models\PendingApproval;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApprovals
{
    public function approvals(): MorphMany
    {
        return $this->morphMany(config('filament-package_ffhs_approvals.models.approvals', Approval::class), 'approvable');
    }

    public function pendingApprovals(): MorphMany
    {
        return $this->morphMany(config('filament-package_ffhs_approvals.models.pendings', PendingApproval::class), 'approvable');
    }
}
