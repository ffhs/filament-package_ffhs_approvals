<?php

use App\Models\User;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Models\PendingApproval;

return [
    'models' => [
        'approver' => User::class,
        'approval' => Approval::class,
        'pending_approval' => PendingApproval::class,
    ],
    'tables' => [
        'approvers' => 'users',
        'approvals' => 'approvals',
        'pendings' => 'pendings',
    ],
];
