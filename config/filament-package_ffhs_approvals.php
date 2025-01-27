<?php

return [
    'models' => [
        'approver' => '\App\Models\User',
        'approval' => Ffhs\Approvals\Models\Approval::class,
        'pending_approval' => Ffhs\Approvals\Models\PendingApproval::class,
    ],
    'tables' => [
        'approvers' => 'users',
        'approvals' => 'approvals',
        'pendings' => 'pendings',
    ],
];
