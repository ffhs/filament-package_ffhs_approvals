<?php

namespace App\Approvals;

use Ffhs\Approvals\Contracts\HasApprovalStatuses;

enum TestApprovalStatuses: string implements HasApprovalStatuses
{

    case APPROVED = 'approved';
    case PENDING = 'pending';
    case DENIED = 'denied';

    public static function getApprovedStatuses(): array
    {
      return [
          'approved'
      ];
    }

    public static function getDeclinedStatuses(): array
    {
        return [
            'denied'
        ];
    }

    public static function getPendingStatuses(): array
    {
        return [
            'pending'
        ];
    }
}
