<?php

namespace Ffhs\Approvals\Policies;

use App\Models\User;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalByPolicy
{
    use HandlesAuthorization;

    public function approve(User $user, ApprovalBy $approvalBy): bool
    {
        return $approvalBy->canApproveFromPermissions($user);
    }
}
