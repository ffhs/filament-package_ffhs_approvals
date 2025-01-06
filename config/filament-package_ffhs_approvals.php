<?php

return [
    'models' => [
        'approver' => '\App\Models\User',
        'approval' => Ffhs\Approvals\Models\Approval::class,
    ],
    'tables' => [
        'approvers' => 'users',
        'approvals' => 'approvals',
    ],
];
