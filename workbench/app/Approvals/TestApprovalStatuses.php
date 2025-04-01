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
            self::APPROVED,
        ];
    }

    public static function getDeniedStatuses(): array
    {
        return [
            self::DENIED,
        ];
    }

    public static function getPendingStatuses(): array
    {
        return [
            self::PENDING,
        ];
    }
}
